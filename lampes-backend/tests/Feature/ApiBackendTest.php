<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Categorie;
use App\Models\Client;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\LignePanier;
use App\Models\Paiement;
use App\Models\Panier;
use App\Models\Produit;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ApiBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_route_requires_bearer_token(): void
    {
        $this->getJson('/api/v1/me')
            ->assertUnauthorized();
    }

    public function test_register_creates_client_cart_and_token(): void
    {
        $response = $this->withCsrf()->postJson('/api/v1/register', [
            'nom' => 'Doe',
            'prenom' => 'Jane',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'telephone' => '0600000001',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'client' => ['id_client', 'nom', 'prenom', 'email'],
                ],
            ]);

        $client = Client::where('email', 'jane@example.com')->firstOrFail();

        $this->assertNotNull($client->api_token);
        $this->assertDatabaseHas('paniers', [
            'id_client' => $client->id_client,
        ]);
    }

    public function test_public_mutations_require_csrf_token(): void
    {
        $payload = [
            'name' => 'Client CSRF',
            'email' => 'csrf@example.com',
            'subject' => 'Question boutique',
            'message' => 'Bonjour, je veux des informations sur vos lampes solaires.',
        ];

        $this->postJson('/api/v1/contact', $payload)
            ->assertStatus(419);

        $this->withCsrf()->postJson('/api/v1/contact', $payload)
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'csrf@example.com',
            'subject' => 'Question boutique',
        ]);
    }

    public function test_login_rotates_token_and_allows_access_to_profile(): void
    {
        $client = Client::create($this->clientData('login@example.com'));
        $oldToken = $this->issueToken($client, 'old-token');

        $response = $this->withCsrf()->postJson('/api/v1/login', [
            'email' => $client->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $newToken = $this->cookieValue($response, config('security.auth_cookie_name', 'access_token'));

        $this->assertNotSame($oldToken, $newToken);

        $this->withToken($oldToken)->getJson('/api/v1/me')
            ->assertUnauthorized();

        $this->withToken($newToken)->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.email', $client->email);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $client = Client::create($this->clientData('client@example.com'));

        $this->withToken($this->issueToken($client))
            ->getJson('/api/v1/admin/dashboard/stats')
            ->assertForbidden();
    }

    public function test_popular_categories_endpoint_filters_empty_categories(): void
    {
        $popularCategory = Categorie::create(['nom' => 'Populaire']);
        Categorie::create(['nom' => 'Vide']);

        Produit::create([
            'nom' => 'Lampe Populaire',
            'description' => 'Produit pour categorie populaire',
            'prix' => 100,
            'stock' => 3,
            'image_url' => 'https://example.com/populaire.jpg',
            'id_categorie' => $popularCategory->id_categorie,
        ]);

        $this->getJson('/api/v1/categories/populaires')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nom', 'Populaire')
            ->assertJsonPath('data.0.produits_count', 1);
    }

    public function test_admin_can_partially_update_user(): void
    {
        $admin = Client::create([
            ...$this->clientData('partial-admin@example.com'),
            'role' => 'admin',
        ]);
        $client = Client::create($this->clientData('partial-user@example.com'));

        $this->withToken($this->issueToken($admin))
            ->putJson("/api/v1/admin/users/{$client->id_client}", [
                'role' => 'admin',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.role', 'admin');

        $this->assertDatabaseHas('clients', [
            'id_client' => $client->id_client,
            'role' => 'admin',
        ]);
    }

    public function test_admin_read_endpoints_return_successfully(): void
    {
        $admin = Client::create([
            ...$this->clientData('read-admin@example.com'),
            'role' => 'admin',
        ]);
        $client = Client::create($this->clientData('read-user@example.com'));
        $product = $this->createProduct('Lampe Admin Read');
        $commande = Commande::create([
            'id_client' => $client->id_client,
            'date_commande' => now(),
            'statut' => 'livree',
            'total' => 150,
        ]);

        LigneCommande::create([
            'id_commande' => $commande->id_commande,
            'id_produit' => $product->id_produit,
            'quantite' => 1,
            'prix_unitaire' => 150,
        ]);

        Paiement::create([
            'id_commande' => $commande->id_commande,
            'id_client' => $client->id_client,
            'montant' => 150,
            'methode' => 'livraison',
            'statut' => 'valide',
        ]);

        $token = $this->issueToken($admin);

        foreach ([
            '/api/v1/admin/dashboard/stats',
            '/api/v1/admin/dashboard/recent-orders',
            '/api/v1/admin/dashboard/top-products',
            '/api/v1/admin/dashboard/monthly-sales',
            '/api/v1/admin/products',
            '/api/v1/admin/users',
            '/api/v1/admin/orders',
            '/api/v1/admin/payments',
        ] as $endpoint) {
            $this->withToken($token)
                ->getJson($endpoint)
                ->assertOk()
                ->assertJsonPath('success', true);
        }
    }

    public function test_user_can_create_order_and_stock_is_updated(): void
    {
        $client = Client::create($this->clientData('order@example.com'));
        $category = Categorie::create(['nom' => 'Salon']);
        $product = Produit::create([
            'nom' => 'Lampe Commande',
            'description' => 'Lampe pour commande',
            'prix' => 120,
            'stock' => 5,
            'image_url' => 'https://example.com/order.jpg',
            'id_categorie' => $category->id_categorie,
        ]);
        $panier = Panier::create([
            'id_client' => $client->id_client,
            'date_creation' => now(),
        ]);
        LignePanier::create([
            'id_panier' => $panier->id_panier,
            'id_produit' => $product->id_produit,
            'quantite' => 2,
        ]);

        $response = $this->withToken($this->issueToken($client))
            ->postJson('/api/v1/orders', [
                'adresse' => '123 Rue Test',
                'ville' => 'Casablanca',
                'code_postal' => '20000',
                'pays' => 'Maroc',
                'methode_paiement' => 'livraison',
            ]);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('commandes', [
            'id_client' => $client->id_client,
            'statut' => 'en_attente',
            'total' => 270,
        ]);
        $this->assertDatabaseHas('paiements', [
            'methode' => 'livraison',
            'statut' => 'en_attente',
        ]);
        $this->assertDatabaseHas('livraisons', [
            'ville' => 'Casablanca',
        ]);
        $this->assertDatabaseHas('produits', [
            'id_produit' => $product->id_produit,
            'stock' => 3,
        ]);
        $this->assertDatabaseMissing('ligne_paniers', [
            'id_panier' => $panier->id_panier,
        ]);
    }

    public function test_review_requires_purchase_and_prevents_duplicates(): void
    {
        $client = Client::create($this->clientData('review@example.com'));
        $category = Categorie::create(['nom' => 'Reviews']);
        $product = Produit::create([
            'nom' => 'Lampe Review',
            'description' => 'Produit review',
            'prix' => 50,
            'stock' => 5,
            'image_url' => 'https://example.com/review.jpg',
            'id_categorie' => $category->id_categorie,
        ]);

        $token = $this->issueToken($client);

        $this->withToken($token)->postJson('/api/v1/reviews', [
            'id_produit' => $product->id_produit,
            'note' => 5,
            'commentaire' => 'Excellent produit vraiment top',
        ])->assertForbidden();

        $commande = Commande::create([
            'id_client' => $client->id_client,
            'date_commande' => now(),
            'statut' => 'livree',
            'total' => 50,
        ]);
        LigneCommande::create([
            'id_commande' => $commande->id_commande,
            'id_produit' => $product->id_produit,
            'quantite' => 1,
            'prix_unitaire' => 50,
        ]);

        $this->withToken($token)->postJson('/api/v1/reviews', [
            'id_produit' => $product->id_produit,
            'note' => 5,
            'commentaire' => 'Excellent produit vraiment top',
        ])->assertCreated();

        $this->withToken($token)->postJson('/api/v1/reviews', [
            'id_produit' => $product->id_produit,
            'note' => 4,
            'commentaire' => 'Toujours tres bien merci',
        ])->assertStatus(400);
    }

    public function test_review_allows_pending_order_after_checkout(): void
    {
        $client = Client::create($this->clientData('review-pending@example.com'));
        $product = $this->createProduct('Lampe Pending Review');
        $token = $this->issueToken($client);

        $commande = Commande::create([
            'id_client' => $client->id_client,
            'date_commande' => now(),
            'statut' => 'en_attente',
            'payment_status' => 'pending',
            'total' => 50,
        ]);

        LigneCommande::create([
            'id_commande' => $commande->id_commande,
            'id_produit' => $product->id_produit,
            'quantite' => 1,
            'prix_unitaire' => 50,
        ]);

        $this->withToken($token)->postJson('/api/v1/reviews', [
            'id_produit' => $product->id_produit,
            'note' => 5,
            'commentaire' => 'Commande faite et produit tres bien',
        ])->assertCreated();
    }

    public function test_public_testimonials_include_product_reviews(): void
    {
        $client = Client::create($this->clientData('review-contact@example.com'));
        $product = $this->createProduct('Lampe Contact Review');

        Avis::create([
            'note' => 5,
            'commentaire' => 'Cette lampe achetee est excellente pour la terrasse',
            'date_avis' => now(),
            'id_client' => $client->id_client,
            'id_produit' => $product->id_produit,
        ]);

        $this->getJson('/api/reviews')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment([
                'source' => 'product',
                'product_name' => 'Lampe Contact Review',
                'comment' => 'Cette lampe achetee est excellente pour la terrasse',
                'rating' => 5,
            ]);
    }

    public function test_review_owner_can_update_and_delete_review(): void
    {
        $client = Client::create($this->clientData('review-owner@example.com'));
        $review = Avis::create([
            'note' => 4,
            'commentaire' => 'Avis initial assez long',
            'date_avis' => now(),
            'id_client' => $client->id_client,
            'id_produit' => $this->createProduct()->id_produit,
        ]);

        $token = $this->issueToken($client);

        $this->withToken($token)->putJson("/api/v1/reviews/{$review->id_avis}", [
            'note' => 5,
            'commentaire' => 'Avis modifie encore meilleur',
        ])->assertOk();

        $this->assertDatabaseHas('avis', [
            'id_avis' => $review->id_avis,
            'note' => 5,
        ]);

        $this->withToken($token)->deleteJson("/api/v1/reviews/{$review->id_avis}")
            ->assertOk();

        $this->assertDatabaseMissing('avis', [
            'id_avis' => $review->id_avis,
        ]);
    }

    public function test_user_cannot_update_another_users_cart_line(): void
    {
        $owner = Client::create($this->clientData('owner@example.com'));
        $intruder = Client::create($this->clientData('intruder@example.com'));
        $product = $this->createProduct('Lampe A');
        $panier = Panier::create([
            'id_client' => $owner->id_client,
            'date_creation' => now(),
        ]);
        $line = LignePanier::create([
            'id_panier' => $panier->id_panier,
            'id_produit' => $product->id_produit,
            'quantite' => 1,
        ]);

        $response = $this->withToken($this->issueToken($intruder))->putJson("/api/v1/cart/{$line->id_ligne_panier}", [
            'quantite' => 2,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('ligne_paniers', [
            'id_ligne_panier' => $line->id_ligne_panier,
            'quantite' => 1,
        ]);
    }

    public function test_user_cannot_remove_another_users_cart_line(): void
    {
        $owner = Client::create($this->clientData('owner2@example.com'));
        $intruder = Client::create($this->clientData('intruder2@example.com'));
        $product = $this->createProduct('Lampe B');
        $panier = Panier::create([
            'id_client' => $owner->id_client,
            'date_creation' => now(),
        ]);
        $line = LignePanier::create([
            'id_panier' => $panier->id_panier,
            'id_produit' => $product->id_produit,
            'quantite' => 1,
        ]);

        $response = $this->withToken($this->issueToken($intruder))
            ->deleteJson("/api/v1/cart/{$line->id_ligne_panier}");

        $response->assertForbidden();
        $this->assertDatabaseHas('ligne_paniers', [
            'id_ligne_panier' => $line->id_ligne_panier,
        ]);
    }

    public function test_user_cannot_cancel_another_users_order(): void
    {
        $owner = Client::create($this->clientData('owner3@example.com'));
        $intruder = Client::create($this->clientData('intruder3@example.com'));
        $product = $this->createProduct('Lampe C');
        $commande = Commande::create([
            'id_client' => $owner->id_client,
            'date_commande' => now(),
            'statut' => 'en_attente',
            'total' => 150,
        ]);
        LigneCommande::create([
            'id_commande' => $commande->id_commande,
            'id_produit' => $product->id_produit,
            'quantite' => 1,
            'prix_unitaire' => 150,
        ]);

        $response = $this->withToken($this->issueToken($intruder))
            ->putJson("/api/v1/orders/{$commande->id_commande}/cancel");

        $response->assertForbidden();
        $this->assertDatabaseHas('commandes', [
            'id_commande' => $commande->id_commande,
            'statut' => 'en_attente',
        ]);
    }

    public function test_admin_monthly_sales_endpoint_works_on_sqlite(): void
    {
        $admin = Client::create([
            ...$this->clientData('admin@lampes.ma'),
            'role' => 'admin',
        ]);

        Commande::create([
            'id_client' => $admin->id_client,
            'date_commande' => now()->subMonth(),
            'statut' => 'livree',
            'total' => 200,
        ]);

        $response = $this->withToken($this->issueToken($admin))
            ->getJson('/api/v1/admin/dashboard/monthly-sales');

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_login_is_throttled_after_too_many_attempts(): void
    {
        Client::create($this->clientData('throttle@example.com'));

        foreach (range(1, 5) as $attempt) {
            $this->withCsrf()->postJson('/api/v1/login', [
                'email' => 'throttle@example.com',
                'password' => 'wrong-password',
            ])->assertUnauthorized();
        }

        $this->withCsrf()->postJson('/api/v1/login', [
            'email' => 'throttle@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);

        RateLimiter::clear('127.0.0.1|throttle@example.com');
    }

    private function createProduct(string $name = 'Lampe Test'): Produit
    {
        $category = Categorie::create([
            'nom' => $name.' Category',
        ]);

        return Produit::create([
            'nom' => $name,
            'description' => 'Description de '.$name,
            'prix' => 150,
            'stock' => 10,
            'image_url' => 'https://example.com/'.strtolower(str_replace(' ', '-', $name)).'.jpg',
            'id_categorie' => $category->id_categorie,
        ]);
    }

    private function clientData(string $email): array
    {
        return [
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => $email,
            'mot_de_passe' => bcrypt('password123'),
            'telephone' => '0600000000',
            'date_inscription' => now(),
            'role' => 'user',
        ];
    }

    private function issueToken(Client $client, ?string $plainToken = null): string
    {
        $tokenId = $plainToken ?? 'token-'.$client->id_client.'-'.bin2hex(random_bytes(8));

        $client->forceFill([
            'api_token' => hash('sha256', $tokenId),
        ])->save();

        return app(JwtService::class)->generateForClient($client, $tokenId);
    }

    private function withCsrf(): self
    {
        $token = bin2hex(random_bytes(32));
        $cookieName = config('security.csrf_cookie_name', 'XSRF-TOKEN');

        return $this
            ->withCredentials()
            ->withUnencryptedCookie($cookieName, $token)
            ->withHeader('X-CSRF-TOKEN', $token);
    }

    private function cookieValue($response, string $name): ?string
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $name) {
                return $cookie->getValue();
            }
        }

        return null;
    }
}

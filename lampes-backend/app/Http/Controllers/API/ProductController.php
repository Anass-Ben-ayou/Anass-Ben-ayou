<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductSummaryResource;
use App\Models\Produit;
use App\Support\SanitizesInput;
use App\Support\StorefrontCollections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // Retourne la liste complete des produits pour l administration.
    public function adminIndex(Request $request)
    {
        $perPage = min((int) $request->get('per_page', 100), 100);

        try {
            $produits = $this->baseProductQuery()
                ->latest()
                ->paginate(max($perPage, 1));

            $produits->getCollection()->transform(function ($produit) {
                return (new ProductResource($produit))->resolve();
            });

            return response()->json([
                'success' => true,
                'data' => $produits,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des produits admin',
            ], 500);
        }
    }

    // Retourne le catalogue avec filtres, recherche, tri et pagination.
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categorie_id' => 'sometimes|integer|exists:categories,id_categorie',
            'category' => 'sometimes|string|max:255',
            'collection' => 'sometimes|string|max:255',
            'prix_min' => 'sometimes|numeric|min:0',
            'prix_max' => 'sometimes|numeric|min:0',
            'search' => 'sometimes|string|max:255',
            'in_stock' => 'sometimes|boolean',
            'sort' => 'sometimes|in:latest,prix_asc,prix_desc,nom_asc,nom_desc,popularite',
            'per_page' => 'sometimes|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $cacheKey = 'products.index.'.md5(json_encode($request->query()));

            $data = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($request) {
                $query = $this->baseProductQuery(! $request->filled('collection'));

                if ($request->filled('categorie_id')) {
                    $query->where('id_categorie', $request->integer('categorie_id'));
                }

                if ($request->filled('category')) {
                    $category = $request->string('category')->toString();
                    $query->whereHas('categorie', function ($categoryQuery) use ($category) {
                        $categoryQuery
                            ->where('slug', $category)
                            ->orWhere('nom', 'like', '%'.$category.'%');
                    });
                }

                if ($request->filled('collection')) {
                    $collection = StorefrontCollections::find($request->string('collection')->toString());

                    if (! $collection) {
                        $query->whereRaw('1 = 0');
                    } else {
                        $query->whereIn('id_categorie', StorefrontCollections::categoryIds($collection));
                    }
                }

                if ($request->filled('prix_min')) {
                    $query->where('prix', '>=', $request->prix_min);
                }

                if ($request->filled('prix_max')) {
                    $query->where('prix', '<=', $request->prix_max);
                }

                if ($request->filled('search')) {
                    $search = $request->string('search')->toString();
                    $query->where(function ($searchQuery) use ($search) {
                        $searchQuery
                            ->where('nom', 'like', '%'.$search.'%')
                            ->orWhere('description', 'like', '%'.$search.'%')
                            ->orWhere('short_description', 'like', '%'.$search.'%')
                            ->orWhereHas('categorie', function ($categoryQuery) use ($search) {
                                $categoryQuery->where('nom', 'like', '%'.$search.'%');
                            });
                    });
                }

                if ($request->boolean('in_stock')) {
                    $query->where('stock', '>', 0);
                }

                $this->applySorting($query, $request->get('sort', 'latest'));

                $perPage = (int) $request->get('per_page', 12);
                $produits = $query->paginate($perPage);

                $produits->getCollection()->transform(function ($produit) {
                    return (new ProductSummaryResource($produit))->resolve();
                });

                return $produits;
            });

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des produits',
            ], 500);
        }
    }

    // Retourne les produits les plus commandes.
    public function bestSellers()
    {
        try {
            $products = Cache::remember('products.best_sellers', now()->addMinutes(5), function () {
                return $this->baseProductQuery(true)
                    ->withCount('ligneCommandes')
                    ->orderByDesc('ligne_commandes_count')
                    ->limit(6)
                    ->get()
                    ->map(fn ($produit) => (new ProductSummaryResource($produit))->resolve());
            });

            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des meilleures ventes',
            ], 500);
        }
    }

    // Retourne les derniers produits pour la page d accueil.
    public function nouveautes()
    {
        try {
            $products = Cache::remember('products.nouveautes', now()->addMinutes(5), function () {
                return $this->baseProductQuery(true)
                    ->latest()
                    ->limit(8)
                    ->get()
                    ->map(fn ($produit) => (new ProductSummaryResource($produit))->resolve());
            });

            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des nouveautes',
            ], 500);
        }
    }

    // Retourne cinq produits mis en avant pour la page d accueil.
    public function featured()
    {
        try {
            $products = Cache::remember('products.featured', now()->addMinutes(5), function () {
                return $this->baseProductQuery(true)
                    ->where('stock', '>', 0)
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get()
                    ->map(fn ($produit) => (new ProductSummaryResource($produit))->resolve());
            });

            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des produits mis en avant',
            ], 500);
        }
    }

    // Reutilise la liste principale pour afficher une seule categorie.
    public function byCategory($id, Request $request)
    {
        $request->merge(['categorie_id' => $id]);

        return $this->index($request);
    }

    // Retourne les details d un produit.
    public function show($id)
    {
        try {
            $produit = $this->findProduct($id);
            $produit = $this->baseProductQuery(true)
                ->with(['avis.client'])
                ->withSum('ligneCommandes', 'quantite')
                ->where('id_produit', $produit->id_produit)
                ->firstOrFail();

            $produit->nombre_ventes = (int) ($produit->ligne_commandes_sum_quantite ?? 0);

            return response()->json([
                'success' => true,
                'data' => (new ProductResource($produit))->resolve(),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouve',
            ], 404);
        }
    }

    // Cree un nouveau produit depuis l espace admin.
    public function store(Request $request)
    {
        $request->merge($this->normalizedInput($request));

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:produits,slug',
            'description' => 'required|string|max:10000',
            'short_description' => 'nullable|string|max:500',
            'prix' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive,draft,out_of_stock',
            'image_url' => 'nullable|string|max:500',
            'image' => 'nullable|string|max:500',
            'image_file' => 'required_without_all:image,image_url|file|mimes:jpg,jpeg,png,webp|max:4096',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'nullable|string|max:500',
            'product_url' => 'nullable|url:http,https|max:500|unique:produits,product_url',
            'specifications' => 'nullable|array',
            'id_categorie' => 'required|exists:categories,id_categorie',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $payload = $this->normalizePayload($request);
            $produit = Produit::create($payload);
            $produit->load('categorie');
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Produit cree avec succes',
                'data' => (new ProductResource($produit))->resolve(),
            ], 201);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la creation du produit',
            ], 500);
        }
    }

    // Met a jour un produit depuis l espace admin.
    public function update(Request $request, $id)
    {
        $request->merge($this->normalizedInput($request));

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:255',
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('produits', 'slug')->ignore((int) $id, 'id_produit')],
            'description' => 'sometimes|string|max:10000',
            'short_description' => 'nullable|string|max:500',
            'prix' => 'sometimes|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'status' => 'nullable|in:active,inactive,draft,out_of_stock',
            'image_url' => 'nullable|string|max:500',
            'image' => 'nullable|string|max:500',
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'nullable|string|max:500',
            'product_url' => ['nullable', 'url:http,https', 'max:500', Rule::unique('produits', 'product_url')->ignore((int) $id, 'id_produit')],
            'specifications' => 'nullable|array',
            'id_categorie' => 'sometimes|exists:categories,id_categorie',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $produit = Produit::findOrFail($id);
            $produit->update($this->normalizePayload($request, true, (int) $produit->id_produit));
            $produit->load('categorie');
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Produit mis a jour avec succes',
                'data' => (new ProductResource($produit))->resolve(),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise a jour du produit',
            ], 500);
        }
    }

    // Supprime un produit du catalogue.
    public function destroy($id)
    {
        try {
            $produit = Produit::findOrFail($id);
            $produit->delete();
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Produit supprime avec succes',
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du produit',
            ], 500);
        }
    }

    // Applique le tri choisi a la requete des produits.
    protected function applySorting($query, string $sort): void
    {
        switch ($sort) {
            case 'prix_asc':
                $query->orderBy('prix');
                break;
            case 'prix_desc':
                $query->orderByDesc('prix');
                break;
            case 'nom_asc':
                $query->orderBy('nom');
                break;
            case 'nom_desc':
                $query->orderByDesc('nom');
                break;
            case 'popularite':
                $query->withCount('ligneCommandes')->orderByDesc('ligne_commandes_count');
                break;
            default:
                $query->latest();
        }
    }

    // Prepare la requete commune avec les relations et calculs necessaires.
    protected function baseProductQuery(bool $activeOnly = false)
    {
        return Produit::query()
            ->when($activeOnly, fn ($query) => $query->where('status', 'active'))
            ->with('categorie')
            ->withCount('avis')
            ->withAvg('avis', 'note');
    }

    // Accepte soit un identifiant numerique, soit un slug.
    protected function findProduct(string $id): Produit
    {
        return is_numeric($id)
            ? Produit::findOrFail((int) $id)
            : Produit::where('slug', $id)->firstOrFail();
    }

    // Garde les champs image et les valeurs par defaut coherents avant sauvegarde.
    protected function normalizePayload(Request $request, bool $partial = false, ?int $currentProductId = null): array
    {
        $payload = $request->only([
            'nom',
            'slug',
            'description',
            'short_description',
            'prix',
            'old_price',
            'stock',
            'status',
            'image',
            'image_url',
            'gallery_images',
            'product_url',
            'specifications',
            'id_categorie',
        ]);

        if ($request->hasFile('image_file')) {
            $storedPath = $request->file('image_file')->store('products', 'public');
            $publicPath = '/storage/'.ltrim($storedPath, '/');
            $payload['image'] = $publicPath;
            $payload['image_url'] = $publicPath;
            $payload['gallery_images'] = [$publicPath];
        }

        if (empty($payload['slug'] ?? null) && ! empty($payload['nom'] ?? null)) {
            $payload['slug'] = $this->uniqueSlug((string) $payload['nom'], $currentProductId);
        }

        if (array_key_exists('image_url', $payload) && ! array_key_exists('image', $payload)) {
            $payload['image'] = $payload['image_url'];
        }

        if (array_key_exists('image', $payload) && ! array_key_exists('image_url', $payload)) {
            $payload['image_url'] = $payload['image'];
        }

        if (! $partial) {
            $payload['status'] = $payload['status'] ?? (($payload['stock'] ?? 0) > 0 ? 'active' : 'out_of_stock');
            $payload['short_description'] = $payload['short_description'] ?? str($payload['description'] ?? '')->limit(180, '')->toString();
        }

        if (array_key_exists('gallery_images', $payload) && is_array($payload['gallery_images'])) {
            $payload['gallery_images'] = array_values(array_filter($payload['gallery_images'], fn ($value) => filled($value)));
        }

        if (
            (! array_key_exists('gallery_images', $payload) || empty($payload['gallery_images']))
            && ($payload['image_url'] ?? $payload['image'] ?? null)
        ) {
            $payload['gallery_images'] = [$payload['image_url'] ?? $payload['image']];
        }

        return $payload;
    }

    // Cree un slug qui ne rentre pas en conflit avec un autre produit.
    protected function uniqueSlug(string $name, ?int $currentProductId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'produit';
        $slug = $baseSlug;
        $counter = 2;

        while (
            Produit::query()
                ->where('slug', $slug)
                ->when($currentProductId, fn ($query) => $query->whereKeyNot($currentProductId))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    // Nettoie les champs admin avant validation pour eviter de stocker du HTML dangereux.
    protected function normalizedInput(Request $request): array
    {
        $data = [];

        foreach (['nom', 'slug', 'status'] as $field) {
            if ($request->has($field)) {
                $data[$field] = SanitizesInput::plain($request->input($field), 255);
            }
        }

        if ($request->has('description')) {
            $data['description'] = SanitizesInput::paragraph($request->input('description'), 10000);
        }

        if ($request->has('short_description')) {
            $data['short_description'] = SanitizesInput::paragraph($request->input('short_description'), 500);
        }

        foreach (['image', 'image_url'] as $field) {
            if ($request->has($field)) {
                $data[$field] = SanitizesInput::urlOrPath($request->input($field));
            }
        }

        if ($request->has('gallery_images')) {
            $data['gallery_images'] = SanitizesInput::stringList($request->input('gallery_images'));
        }

        if ($request->has('specifications')) {
            $data['specifications'] = SanitizesInput::plainMap($request->input('specifications'));
        }

        if ($request->has('product_url')) {
            $data['product_url'] = SanitizesInput::plain($request->input('product_url'), 500);
        }

        return $data;
    }
}

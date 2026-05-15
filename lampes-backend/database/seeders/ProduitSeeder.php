<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProduitSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('produits')->truncate();
        Schema::enableForeignKeyConstraints();

        $catalog = [
            'Lustres' => [
                ['nom' => 'Lustre cristal prestige', 'prix' => 1599, 'ancien_prix' => 1899, 'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lustre moderne or mat', 'prix' => 1399, 'ancien_prix' => 1699, 'image' => 'https://images.unsplash.com/photo-1484101403633-562f891dc89a?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lustre contemporain 5 lumieres', 'prix' => 1299, 'ancien_prix' => 1499, 'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lustre cascade verre fume', 'prix' => 1799, 'ancien_prix' => 2099, 'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lustre design noir et laiton', 'prix' => 1499, 'ancien_prix' => 1699, 'image' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lustre anneaux LED', 'prix' => 1899, 'ancien_prix' => 2299, 'image' => 'https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lustre minimaliste chambre', 'prix' => 999, 'ancien_prix' => 1199, 'image' => 'https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lustre signature cuivre', 'prix' => 1699, 'ancien_prix' => 1999, 'image' => 'https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lustre sphere opaline', 'prix' => 1199, 'ancien_prix' => 1399, 'image' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lustre grand salon luxe', 'prix' => 2399, 'ancien_prix' => 2699, 'image' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=900&q=80'],
            ],
            'Suspensions' => [
                ['nom' => 'Suspension halo doree', 'prix' => 799, 'ancien_prix' => 949, 'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Suspension industrielle noire', 'prix' => 459, 'ancien_prix' => 559, 'image' => 'https://images.unsplash.com/photo-1484101403633-562f891dc89a?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Suspension verre ambre', 'prix' => 699, 'ancien_prix' => 849, 'image' => 'https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Suspension lineaire salle a manger', 'prix' => 999, 'ancien_prix' => 1199, 'image' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Suspension globe blanche', 'prix' => 549, 'ancien_prix' => 649, 'image' => 'https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Suspension trio moderne', 'prix' => 879, 'ancien_prix' => 999, 'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Suspension laiton minimal', 'prix' => 649, 'ancien_prix' => 799, 'image' => 'https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Suspension scandinave bois', 'prix' => 589, 'ancien_prix' => 699, 'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Suspension opaline premium', 'prix' => 729, 'ancien_prix' => 859, 'image' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Suspension cuisine LED', 'prix' => 519, 'ancien_prix' => 629, 'image' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80'],
            ],
            'Appliques' => [
                ['nom' => 'Applique murale LED slim', 'prix' => 289, 'ancien_prix' => 349, 'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Applique noire orientable', 'prix' => 319, 'ancien_prix' => 389, 'image' => 'https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Applique laiton chambre', 'prix' => 359, 'ancien_prix' => 429, 'image' => 'https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Applique verre satine', 'prix' => 269, 'ancien_prix' => 329, 'image' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Applique duo salon', 'prix' => 399, 'ancien_prix' => 469, 'image' => 'https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Applique lecture pivotante', 'prix' => 249, 'ancien_prix' => 299, 'image' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Applique couloir moderne', 'prix' => 219, 'ancien_prix' => 279, 'image' => 'https://images.unsplash.com/photo-1484101403633-562f891dc89a?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Applique hotel premium', 'prix' => 429, 'ancien_prix' => 499, 'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Applique geometrique or', 'prix' => 339, 'ancien_prix' => 399, 'image' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Applique ambiance douce', 'prix' => 279, 'ancien_prix' => 329, 'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80'],
            ],
            'Lampadaires' => [
                ['nom' => 'Lampadaire arc luxe', 'prix' => 1199, 'ancien_prix' => 1399, 'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampadaire noir mat', 'prix' => 749, 'ancien_prix' => 899, 'image' => 'https://images.unsplash.com/photo-1484101403633-562f891dc89a?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampadaire tripode bois', 'prix' => 899, 'ancien_prix' => 1049, 'image' => 'https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampadaire lecture LED', 'prix' => 629, 'ancien_prix' => 749, 'image' => 'https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampadaire design salon', 'prix' => 979, 'ancien_prix' => 1149, 'image' => 'https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampadaire courbe dore', 'prix' => 1099, 'ancien_prix' => 1299, 'image' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampadaire textile beige', 'prix' => 689, 'ancien_prix' => 799, 'image' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampadaire minimal LED', 'prix' => 839, 'ancien_prix' => 959, 'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampadaire opaline premium', 'prix' => 929, 'ancien_prix' => 1049, 'image' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampadaire chambre cosy', 'prix' => 599, 'ancien_prix' => 699, 'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80'],
            ],
            'Lampes a poser' => [
                ['nom' => 'Lampe de chevet opaline', 'prix' => 299, 'ancien_prix' => 359, 'image' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampe de bureau noire', 'prix' => 249, 'ancien_prix' => 299, 'image' => 'https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampe de table laiton', 'prix' => 379, 'ancien_prix' => 449, 'image' => 'https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampe vintage atelier', 'prix' => 329, 'ancien_prix' => 389, 'image' => 'https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampe boule verre fume', 'prix' => 419, 'ancien_prix' => 499, 'image' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampe ceramique blanche', 'prix' => 289, 'ancien_prix' => 349, 'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampe de nuit tactile', 'prix' => 199, 'ancien_prix' => 249, 'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampe marbre et or', 'prix' => 459, 'ancien_prix' => 529, 'image' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampe ambiance salon', 'prix' => 279, 'ancien_prix' => 329, 'image' => 'https://images.unsplash.com/photo-1484101403633-562f891dc89a?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Lampe LED rechargeable', 'prix' => 229, 'ancien_prix' => 289, 'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80'],
            ],
            'Spots' => [
                ['nom' => 'Spot LED blanc chaud', 'prix' => 89, 'ancien_prix' => 109, 'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Spot encastrable noir', 'prix' => 99, 'ancien_prix' => 119, 'image' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Spot orientable plafond', 'prix' => 129, 'ancien_prix' => 149, 'image' => 'https://images.unsplash.com/photo-1484101403633-562f891dc89a?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Spot cuisine LED', 'prix' => 109, 'ancien_prix' => 129, 'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Spot salle de bain IP65', 'prix' => 139, 'ancien_prix' => 159, 'image' => 'https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Spot rail moderne', 'prix' => 179, 'ancien_prix' => 219, 'image' => 'https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Spot mini encastre', 'prix' => 79, 'ancien_prix' => 99, 'image' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Spot duo directionnel', 'prix' => 189, 'ancien_prix' => 229, 'image' => 'https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Spot salon premium', 'prix' => 159, 'ancien_prix' => 189, 'image' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=900&q=80'],
                ['nom' => 'Spot basse consommation', 'prix' => 95, 'ancien_prix' => 115, 'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80'],
            ],
        ];

        $categories = Categorie::query()->get()->keyBy('nom');

        foreach ($catalog as $categoryName => $products) {
            $category = $categories->get($categoryName);

            if (! $category) {
                continue;
            }

            foreach ($products as $index => $product) {
                $name = $product['nom'];
                $description = $this->buildDescription($name, $categoryName, $index + 1);
                $stock = 6 + (($index * 3) % 19);

                DB::table('produits')->insert([
                    'nom' => $name,
                    'slug' => Str::slug($name),
                    'description' => $description,
                    'short_description' => Str::limit($description, 160, ''),
                    'prix' => $product['prix'],
                    'old_price' => $product['ancien_prix'],
                    'stock' => $stock,
                    'status' => $stock > 0 ? 'active' : 'out_of_stock',
                    'image' => $product['image'],
                    'image_url' => $product['image'],
                    'product_url' => 'https://solarlight.ma/produits/'.Str::slug($name),
                    'specifications' => json_encode([
                        'categorie' => $categoryName,
                        'finition' => $this->finitionFor($index),
                        'usage' => $this->usageFor($categoryName),
                        'garantie' => '2 ans',
                    ], JSON_UNESCAPED_UNICODE),
                    'id_categorie' => $category->id_categorie,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    protected function buildDescription(string $name, string $categoryName, int $position): string
    {
        return "{$name} est un {$categoryName} soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele {$position} de notre collection avec installation simple et rendu lumineux confortable au quotidien.";
    }

    protected function finitionFor(int $index): string
    {
        $finishes = ['noir mat', 'laiton brosse', 'verre opalin', 'chrome satine', 'blanc sable'];

        return $finishes[$index % count($finishes)];
    }

    protected function usageFor(string $categoryName): string
    {
        return match ($categoryName) {
            'Lustres' => 'salon et salle a manger',
            'Suspensions' => 'ilot, table et entree',
            'Appliques' => 'couloir, tete de lit et sejour',
            'Lampadaires' => 'coin lecture et salon',
            'Lampes a poser' => 'chevet, bureau et console',
            'Spots' => 'plafond et circulation',
            default => 'interieur',
        };
    }
}

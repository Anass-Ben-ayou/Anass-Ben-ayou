<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductSummaryResource;
use App\Models\Categorie;
use App\Models\Produit;

class CollectionController extends Controller
{
    // Returns the public collection cards displayed on the storefront.
    public function index()
    {
        $collections = collect($this->definitions())
            ->map(function (array $collection) {
                $previewProduct = $this->collectionQuery($collection)->first();

                return [
                    'id' => $collection['id'],
                    'slug' => $collection['slug'],
                    'title' => $collection['title'],
                    'description' => $collection['description'],
                    'image' => $this->resolveImageUrl($previewProduct?->image_url ?: $collection['fallback_image']),
                    'product_count' => $this->collectionQuery($collection)->count(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $collections,
        ]);
    }

    // Returns the products linked to one collection card.
    public function products(string $id)
    {
        $collection = collect($this->definitions())
            ->first(fn (array $item) => $item['id'] === $id || $item['slug'] === $id);

        if (! $collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection non trouvee',
            ], 404);
        }

        $products = $this->collectionQuery($collection)
            ->get()
            ->map(fn ($produit) => (new ProductSummaryResource($produit))->resolve());

        return response()->json([
            'success' => true,
            'data' => [
                'collection' => [
                    'id' => $collection['id'],
                    'slug' => $collection['slug'],
                    'title' => $collection['title'],
                    'description' => $collection['description'],
                    'image' => $products->first()['image'] ?? $this->resolveImageUrl($collection['fallback_image']),
                ],
                'products' => $products,
            ],
        ]);
    }

    // Builds the product query for a collection based on category ids.
    protected function collectionQuery(array $collection)
    {
        $categoryIds = Categorie::query()
            ->whereIn('slug', $collection['category_slugs'])
            ->pluck('id_categorie');

        return Produit::query()
            ->with('categorie')
            ->whereIn('id_categorie', $categoryIds)
            ->latest()
            ->limit(12);
    }

    // Defines the storefront collections shown to customers.
    protected function definitions(): array
    {
        return [
            [
                'id' => 'lampes-solaires-jardin',
                'slug' => 'lampes-solaires-jardin',
                'title' => 'Lampes solaires jardin',
                'description' => 'Des lampes exterieures pensees pour les allees, terrasses et coins detente.',
                'category_slugs' => ['piquets-solaires', 'bornes-et-potelets-solaires', 'guirlandes-solaires'],
                'fallback_image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 'projecteurs-solaires',
                'slug' => 'projecteurs-solaires',
                'title' => 'Projecteurs solaires',
                'description' => 'Des solutions lumineuses plus directes pour facades, acces et zones exterieures.',
                'category_slugs' => ['projecteur-solaire', 'lampadaires-solaires'],
                'fallback_image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 'appliques-murales-solaires',
                'slug' => 'appliques-murales-solaires',
                'title' => 'Appliques murales solaires',
                'description' => 'Un eclairage mural simple a poser pour l entree, les couloirs et les murs exterieurs.',
                'category_slugs' => ['applique-solaire'],
                'fallback_image' => 'https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 'guirlandes-solaires',
                'slug' => 'guirlandes-solaires',
                'title' => 'Guirlandes solaires',
                'description' => 'Des ambiances plus douces pour jardins, pergolas et repas en plein air.',
                'category_slugs' => ['guirlandes-solaires', 'spots-encastrables-solaires'],
                'fallback_image' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 'kits-solaires',
                'slug' => 'kits-solaires',
                'title' => 'Kits solaires',
                'description' => 'Des ensembles complets pour equiper vos espaces avec une signature lumineuse coherente.',
                'category_slugs' => ['kits-photovoltaiques-dautoconsommation-plugplay'],
                'fallback_image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80',
            ],
        ];
    }

    protected function resolveImageUrl(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return url(ltrim($value, '/'));
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductSummaryResource;
use App\Support\StorefrontCollections;

class CollectionController extends Controller
{
    // Returns the public collection cards displayed on the storefront.
    public function index()
    {
        $collections = collect(StorefrontCollections::all())
            ->map(function (array $collection) {
                $previewProduct = StorefrontCollections::productQuery($collection)
                    ->latest()
                    ->first();

                return [
                    'id' => $collection['id'],
                    'slug' => $collection['slug'],
                    'title' => $collection['title'],
                    'description' => $collection['description'],
                    'image' => $this->resolveImageUrl($previewProduct?->image_url ?: $collection['fallback_image']),
                    'product_count' => StorefrontCollections::productQuery($collection)->count(),
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
        $collection = StorefrontCollections::find($id);

        if (! $collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection non trouvee',
            ], 404);
        }

        $products = StorefrontCollections::productQuery($collection)
            ->latest()
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

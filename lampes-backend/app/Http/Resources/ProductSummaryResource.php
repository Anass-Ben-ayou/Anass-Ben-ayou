<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $category = $this->whenLoaded('categorie');
        $imageUrl = $this->resolveImageUrl($this->image_url ?: $this->image);
        $galleryImages = collect($this->gallery_images ?? [])
            ->map(fn ($value) => $this->resolveImageUrl($value))
            ->filter()
            ->values()
            ->all();

        if ($galleryImages === [] && $imageUrl) {
            $galleryImages = [$imageUrl];
        }
        $averageRating = isset($this->note_moyenne)
            ? $this->note_moyenne
            : (isset($this->avis_avg_note) ? round((float) $this->avis_avg_note, 1) : null);
        $reviewCount = isset($this->nombre_avis)
            ? $this->nombre_avis
            : (isset($this->avis_count) ? (int) $this->avis_count : null);
        $categoryPayload = $category ? [
            'id' => $category->id_categorie,
            'id_categorie' => $category->id_categorie,
            'name' => $category->nom,
            'nom' => $category->nom,
            'slug' => $category->slug,
        ] : null;

        return [
            'id' => $this->id_produit,
            'id_produit' => $this->id_produit,
            'name' => $this->nom,
            'nom' => $this->nom,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'price' => (float) $this->prix,
            'prix' => (float) $this->prix,
            'old_price' => $this->old_price !== null ? (float) $this->old_price : null,
            'image' => $imageUrl,
            'image_url' => $imageUrl,
            'gallery_images' => $galleryImages,
            'stock' => (int) $this->stock,
            'status' => $this->status,
            'id_categorie' => $this->id_categorie,
            'category' => $categoryPayload,
            'categorie' => $categoryPayload,
            'note_moyenne' => $averageRating,
            'nombre_avis' => $reviewCount,
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

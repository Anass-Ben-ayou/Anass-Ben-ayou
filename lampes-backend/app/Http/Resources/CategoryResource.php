<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_categorie,
            'id_categorie' => $this->id_categorie,
            'name' => $this->nom,
            'nom' => $this->nom,
            'slug' => $this->slug,
            'description' => $this->description,
            'products_count' => $this->whenCounted('produits', $this->produits_count),
            'produits_count' => $this->whenCounted('produits', $this->produits_count),
            'products' => ProductResource::collection($this->whenLoaded('produits')),
        ];
    }
}

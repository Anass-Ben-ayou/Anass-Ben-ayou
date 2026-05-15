<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    // Laisse les middlewares de route decider qui peut gerer les produits.
    public function authorize(): bool
    {
        return true;
    }

    // Definit les regles de validation pour creer ou modifier un produit.
    public function rules(): array
    {
        $productId = (int) ($this->route('id') ?? $this->route('product') ?? 0);

        return [
            'nom' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('produits', 'slug')->ignore($productId, 'id_produit')],
            'description' => 'required|string|max:10000',
            'short_description' => 'nullable|string|max:500',
            'prix' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive,draft,out_of_stock',
            'image_url' => 'nullable|url:http,https|max:500',
            'product_url' => ['nullable', 'url:http,https', 'max:500', Rule::unique('produits', 'product_url')->ignore($productId, 'id_produit')],
            'id_categorie' => 'required|exists:categories,id_categorie',
        ];
    }
}

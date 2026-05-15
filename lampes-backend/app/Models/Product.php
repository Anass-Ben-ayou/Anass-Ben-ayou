<?php

namespace App\Models;

class Product extends Produit
{
    // Garde les champs modifiables de cet alias alignes avec le modele Produit.
    protected $fillable = [
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
    ];

    // Fournit un alias en anglais pour la relation avec la categorie.
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_categorie', 'id_categorie');
    }
}

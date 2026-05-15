<?php

namespace App\Models;

class Category extends Categorie
{
    protected $fillable = [
        'nom',
        'slug',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'id_categorie', 'id_categorie');
    }
}

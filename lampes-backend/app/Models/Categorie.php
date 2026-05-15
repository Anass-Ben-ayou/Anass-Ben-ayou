<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $primaryKey = 'id_categorie';

    protected $fillable = [
        'nom',
        'slug',
        'description',
    ];

    // Relation avec les produits
    public function produits()
    {
        return $this->hasMany(Produit::class, 'id_categorie');
    }
}

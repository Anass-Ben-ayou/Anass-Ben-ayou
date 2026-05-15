<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LignePanier extends Model
{
    use HasFactory;

    protected $table = 'ligne_paniers';

    protected $primaryKey = 'id_ligne_panier';

    protected $fillable = [
        'quantite',
        'id_panier',
        'id_produit',
    ];

    // Relation avec le panier
    public function panier()
    {
        return $this->belongsTo(Panier::class, 'id_panier');
    }

    // Relation avec le produit
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit');
    }

    // Accesseur pour le sous-total
    public function getSousTotalAttribute()
    {
        return $this->quantite * $this->produit->prix;
    }
}

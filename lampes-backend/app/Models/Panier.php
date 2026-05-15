<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    use HasFactory;

    protected $table = 'paniers';

    protected $primaryKey = 'id_panier';

    protected $fillable = [
        'date_creation',
        'id_client',
    ];

    protected $casts = [
        'date_creation' => 'datetime',
    ];

    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    // Relation avec les lignes panier
    public function lignePaniers()
    {
        return $this->hasMany(LignePanier::class, 'id_panier');
    }

    // Calcul du total du panier
    public function getTotalAttribute()
    {
        $total = 0;
        foreach ($this->lignePaniers as $ligne) {
            $total += $ligne->quantite * $ligne->produit->prix;
        }

        return $total;
    }
}

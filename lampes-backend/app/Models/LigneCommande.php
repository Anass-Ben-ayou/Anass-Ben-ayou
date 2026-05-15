<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    use HasFactory;

    protected $table = 'ligne_commandes';

    protected $primaryKey = 'id_ligne_commande';

    protected $fillable = [
        'quantite',
        'prix_unitaire',
        'id_commande',
        'id_produit',
    ];

    // Relation avec la commande
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_commande');
    }

    // Relation avec le produit
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit');
    }
}

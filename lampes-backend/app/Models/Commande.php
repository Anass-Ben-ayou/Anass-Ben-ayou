<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $table = 'commandes';

    protected $primaryKey = 'id_commande';

    protected $fillable = [
        'date_commande',
        'statut',
        'payment_status',
        'total',
        'currency',
        'id_client',
    ];

    protected $casts = [
        'date_commande' => 'datetime',
        'total' => 'decimal:2',
    ];

    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    // Relation avec les lignes commande
    public function ligneCommandes()
    {
        return $this->hasMany(LigneCommande::class, 'id_commande');
    }

    // Relation avec le paiement
    public function paiement()
    {
        return $this->hasOne(Paiement::class, 'id_commande');
    }

    // Relation avec la livraison
    public function livraison()
    {
        return $this->hasOne(Livraison::class, 'id_commande');
    }
}

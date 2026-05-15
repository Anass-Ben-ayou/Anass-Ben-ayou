<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'paiements';

    protected $primaryKey = 'id_paiement';

    protected $fillable = [
        'date_paiement',
        'montant',
        'currency',
        'methode',
        'payment_gateway',
        'transaction_id',
        'payment_token',
        'statut',
        'payment_status',
        'card_brand',
        'card_last4',
        'card_country',
        'gateway_response',
        'reference_externe',
        'id_commande',
        'id_client',
    ];

    protected $casts = [
        'gateway_response' => 'array',
    ];

    // Relation avec la commande
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_commande');
    }
}

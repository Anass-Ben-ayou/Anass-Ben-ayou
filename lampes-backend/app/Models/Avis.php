<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    use HasFactory;

    protected $table = 'avis';

    protected $primaryKey = 'id_avis';

    protected $fillable = [
        'note',
        'commentaire',
        'date_avis',
        'id_client',
        'id_produit',
    ];

    protected $casts = [
        'date_avis' => 'datetime',
        'note' => 'integer',
    ];

    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    // Relation avec le produit
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit');
    }
}

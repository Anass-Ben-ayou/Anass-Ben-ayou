<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    use HasFactory;

    protected $table = 'livraisons';

    protected $primaryKey = 'id_livraison';

    protected $fillable = [
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'statut',
        'id_commande',
    ];

    // Relation avec la commande
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_commande');
    }
}

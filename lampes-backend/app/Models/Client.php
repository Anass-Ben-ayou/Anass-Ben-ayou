<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'clients';

    protected $primaryKey = 'id_client';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'api_token',
        'telephone',
        'date_inscription',
        'role',
    ];

    protected $hidden = [
        'mot_de_passe',
        'api_token',
        'remember_token',
    ];

    protected $casts = [
        'date_inscription' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // Relation avec le panier
    public function panier()
    {
        return $this->hasOne(Panier::class, 'id_client');
    }

    // Relation avec les commandes
    public function commandes()
    {
        return $this->hasMany(Commande::class, 'id_client');
    }

    // Relation avec les avis
    public function avis()
    {
        return $this->hasMany(Avis::class, 'id_client');
    }

    // Accesseur pour le nom complet
    public function getNomCompletAttribute()
    {
        return $this->prenom.' '.$this->nom;
    }

    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}

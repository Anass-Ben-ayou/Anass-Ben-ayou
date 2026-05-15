<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Produit extends Model
{
    use HasFactory;

    protected $table = 'produits';

    protected $primaryKey = 'id_produit';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'short_description',
        'prix',
        'old_price',
        'stock',
        'status',
        'image',
        'image_url',
        'gallery_images',
        'product_url',
        'specifications',
        'id_categorie',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'old_price' => 'decimal:2',
        'stock' => 'integer',
        'gallery_images' => 'array',
        'specifications' => 'array',
    ];

    // Relie chaque produit a sa categorie.
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_categorie');
    }

    // Liste les lignes de panier qui utilisent ce produit.
    public function lignePaniers()
    {
        return $this->hasMany(LignePanier::class, 'id_produit');
    }

    // Liste les lignes de commande qui contiennent ce produit.
    public function ligneCommandes()
    {
        return $this->hasMany(LigneCommande::class, 'id_produit');
    }

    // Liste les avis clients associes a ce produit.
    public function avis()
    {
        return $this->hasMany(Avis::class, 'id_produit');
    }

    // Calcule la note moyenne des avis.
    public function getNoteMoyenneAttribute()
    {
        return $this->avis()->avg('note') ?? 0;
    }

    // Formate le prix du produit pour l affichage.
    public function getPrixFormateAttribute()
    {
        return number_format($this->prix, 2, ',', ' ').' DH';
    }

    // Utilise l ancien champ image si image_url est vide.
    public function getImageUrlAttribute($value)
    {
        return $value ?: ($this->attributes['image'] ?? null);
    }

    // Cree une courte description depuis la description complete si elle manque.
    public function getShortDescriptionAttribute($value)
    {
        if (! empty($value)) {
            return $value;
        }

        return Str::limit(trim(strip_tags((string) ($this->attributes['description'] ?? ''))), 180, '');
    }
}

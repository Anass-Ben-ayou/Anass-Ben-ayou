<?php

namespace App\Support;

use App\Models\Categorie;
use App\Models\Produit;

class StorefrontCollections
{
    public static function all(): array
    {
        return [
            [
                'id' => 'lampes-solaires-jardin',
                'slug' => 'lampes-solaires-jardin',
                'title' => 'Lampes solaires jardin',
                'description' => 'Des lampes exterieures pensees pour les allees, terrasses et coins detente.',
                'category_slugs' => [
                    'piquet-solaire-luminaire-solaire',
                    'potelet-borne-solaire-luminaire-eclairage',
                    'guirlande-solaire-luminaire-solaire',
                ],
                'fallback_image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 'projecteurs-solaires',
                'slug' => 'projecteurs-solaires',
                'title' => 'Projecteurs solaires',
                'description' => 'Des solutions lumineuses plus directes pour facades, acces et zones exterieures.',
                'category_slugs' => [
                    'projecteur-solaire-solar',
                    'projecteur-solaire-luminaire-solaire',
                    'solaire-luminaire-urbain-collectivite-luminaire-solaire',
                ],
                'fallback_image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 'appliques-murales-solaires',
                'slug' => 'appliques-murales-solaires',
                'title' => 'Appliques murales solaires',
                'description' => 'Un eclairage mural simple a poser pour l entree, les couloirs et les murs exterieurs.',
                'category_slugs' => ['applique-solaire-luminaire-solaire'],
                'fallback_image' => 'https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 'guirlandes-solaires',
                'slug' => 'guirlandes-solaires',
                'title' => 'Guirlandes solaires',
                'description' => 'Des ambiances plus douces pour jardins, pergolas et repas en plein air.',
                'category_slugs' => [
                    'guirlande-solaire-luminaire-solaire',
                    'spot-encastrable-solaire-luminaire-solaire',
                ],
                'fallback_image' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 'kits-solaires',
                'slug' => 'kits-solaires',
                'title' => 'Kits solaires',
                'description' => 'Des ensembles complets pour equiper vos espaces avec une signature lumineuse coherente.',
                'category_slugs' => ['kit-photovoltaique-plugplay'],
                'fallback_image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80',
            ],
        ];
    }

    public static function find(string $id): ?array
    {
        return collect(self::all())
            ->first(fn (array $item) => $item['id'] === $id || $item['slug'] === $id);
    }

    public static function categoryIds(array $collection)
    {
        return Categorie::query()
            ->whereIn('slug', $collection['category_slugs'])
            ->pluck('id_categorie');
    }

    public static function productQuery(array $collection)
    {
        return Produit::query()
            ->with('categorie')
            ->whereIn('id_categorie', self::categoryIds($collection));
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('categories')->truncate();
        Schema::enableForeignKeyConstraints();

        $categories = [
            ['nom' => 'Lustres', 'description' => 'Lustres elegants pour salon, salle a manger et entree.'],
            ['nom' => 'Suspensions', 'description' => 'Suspensions design pour creer une ambiance moderne et lumineuse.'],
            ['nom' => 'Appliques', 'description' => 'Appliques murales decoratives et fonctionnelles.'],
            ['nom' => 'Lampadaires', 'description' => 'Lampadaires contemporains pour sejour, chambre et bureau.'],
            ['nom' => 'Lampes a poser', 'description' => 'Lampes de table et lampes de chevet pour tous les styles.'],
            ['nom' => 'Spots', 'description' => 'Spots LED pour plafonds, couloirs et espaces techniques.'],
        ];

        foreach ($categories as $categorie) {
            DB::table('categories')->insert([
                'nom' => $categorie['nom'],
                'slug' => Str::slug($categorie['nom']),
                'description' => $categorie['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

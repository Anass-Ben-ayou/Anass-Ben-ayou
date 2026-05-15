<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('clients')->truncate();
        Schema::enableForeignKeyConstraints();

        $clients = [
            [
                'nom' => 'Admin',
                'prenom' => 'Super',
                'email' => 'admin@lampes.ma',
                'mot_de_passe' => Hash::make('admin123'),
                'telephone' => '0600000000',
                'role' => 'admin',
                'date_inscription' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'email' => 'jean.dupont@email.com',
                'mot_de_passe' => Hash::make('password123'),
                'telephone' => '0612345678',
                'role' => 'user',
                'date_inscription' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Marie',
                'email' => 'marie.martin@email.com',
                'mot_de_passe' => Hash::make('password123'),
                'telephone' => '0687654321',
                'role' => 'user',
                'date_inscription' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($clients as $client) {
            DB::table('clients')->insert($client);
        }
    }
}

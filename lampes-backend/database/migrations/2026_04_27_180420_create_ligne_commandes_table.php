<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ligne_commandes', function (Blueprint $table) {
            $table->id('id_ligne_commande');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->foreignId('id_commande')->constrained('commandes', 'id_commande')->onDelete('cascade');
            $table->foreignId('id_produit')->constrained('produits', 'id_produit')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_commandes');
    }
};

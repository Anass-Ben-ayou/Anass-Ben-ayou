<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ligne_paniers', function (Blueprint $table) {
            $table->id('id_ligne_panier');
            $table->integer('quantite');
            $table->foreignId('id_panier')->constrained('paniers', 'id_panier')->onDelete('cascade');
            $table->foreignId('id_produit')->constrained('produits', 'id_produit')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_paniers');
    }
};

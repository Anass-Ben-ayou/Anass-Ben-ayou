<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id('id_paiement');
            $table->timestamp('date_paiement')->useCurrent();
            $table->decimal('montant', 10, 2);
            $table->enum('methode', ['carte', 'paypal', 'virement', 'livraison']);
            $table->enum('statut', ['en_attente', 'valide', 'echoue'])->default('en_attente');
            $table->foreignId('id_commande')->constrained('commandes', 'id_commande')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};

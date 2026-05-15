<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('id_client')->nullable()->after('id_commande')->constrained('clients', 'id_client')->nullOnDelete();
            $table->string('payment_gateway')->nullable()->after('methode');
            $table->string('transaction_id')->nullable()->after('payment_gateway');
            $table->string('payment_token')->nullable()->after('transaction_id');
            $table->string('currency', 3)->default('MAD')->after('montant');
            $table->string('payment_status')->nullable()->after('statut');
            $table->string('card_brand')->nullable()->after('payment_status');
            $table->string('card_last4', 4)->nullable()->after('card_brand');
            $table->string('card_country', 2)->nullable()->after('card_last4');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_client');
            $table->dropColumn([
                'payment_gateway',
                'transaction_id',
                'payment_token',
                'currency',
                'payment_status',
                'card_brand',
                'card_last4',
                'card_country',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('currency', 3)->default('MAD')->after('total');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->json('gateway_response')->nullable()->after('card_country');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn('gateway_response');
        });
    }
};

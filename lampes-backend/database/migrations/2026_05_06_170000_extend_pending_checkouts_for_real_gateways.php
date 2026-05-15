<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_checkouts', function (Blueprint $table) {
            $table->string('payment_gateway')->nullable()->after('stripe_session_id');
            $table->string('gateway_session_id')->nullable()->after('payment_gateway');
            $table->string('gateway_reference')->nullable()->after('gateway_session_id');
            $table->json('gateway_payload')->nullable()->after('payload');
            $table->index('gateway_session_id');
            $table->index('gateway_reference');
        });
    }

    public function down(): void
    {
        Schema::table('pending_checkouts', function (Blueprint $table) {
            $table->dropIndex(['gateway_session_id']);
            $table->dropIndex(['gateway_reference']);
            $table->dropColumn([
                'payment_gateway',
                'gateway_session_id',
                'gateway_reference',
                'gateway_payload',
            ]);
        });
    }
};

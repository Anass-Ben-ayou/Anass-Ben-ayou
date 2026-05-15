<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_reviews', function (Blueprint $table) {
            $table->id('id_site_review');
            $table->string('customer_name');
            $table->string('email')->nullable();
            $table->tinyInteger('rating')->unsigned();
            $table->text('comment');
            $table->date('review_date')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_reviews');
    }
};

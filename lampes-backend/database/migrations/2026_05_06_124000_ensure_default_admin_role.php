<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('clients')
            ->where('email', 'admin@lampes.ma')
            ->update(['role' => 'admin']);
    }

    public function down(): void
    {
        DB::table('clients')
            ->where('email', 'admin@lampes.ma')
            ->update(['role' => 'user']);
    }
};

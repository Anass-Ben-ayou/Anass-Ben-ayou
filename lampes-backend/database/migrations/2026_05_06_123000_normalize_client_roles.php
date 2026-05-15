<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('clients')
            ->where('role', 'client')
            ->update(['role' => 'user']);
    }

    public function down(): void
    {
        DB::table('clients')
            ->where('role', 'user')
            ->update(['role' => 'client']);
    }
};

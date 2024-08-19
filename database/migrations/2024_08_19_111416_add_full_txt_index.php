<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('zaposleni', function (Blueprint $table) {
            $table->fullText(['ime', 'prezime', 'srednje_slovo', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zaposleni', function (Blueprint $table) {
            $table->dropFullText(['ime', 'prezime', 'srednje_slovo', 'email']);
        });
    }
};

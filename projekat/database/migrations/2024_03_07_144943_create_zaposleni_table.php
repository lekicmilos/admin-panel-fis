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
        Schema::create('zaposleni', function (Blueprint $table) {
            $table->id();
            $table->string("ime");
            $table->string("prezime");
            $table->string("srednje_slovo");
            $table->string("email");
            $table->enum("pol", ["Muski", "Zenski"]);
            $table->integer("fis_broj")->unique();
            $table->boolean("u_penziji");
            $table->date("datum_penzije")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zaposleni');
    }
};

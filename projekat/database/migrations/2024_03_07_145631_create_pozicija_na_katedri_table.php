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
        Schema::create('pozicija_na_katedri', function (Blueprint $table) {
            $table->id();
            $table->date("datum_od");
            $table->date("datum_do")->nullable();
            $table->enum("pozicija", ["Sef katedre", "Zamenik katedre"]);
            $table->foreignId("zaposleni")->references("id")->on("zaposleni");
            $table->foreignId("katedra")->references("id")->on("katedra");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pozicija_na_katedri', function (Blueprint $table){
            $table->dropForeign(['zaposleni']);
            $table->dropColumn(['zaposleni']);

            $table->dropForeign(['katedra']);
            $table->dropColumn(['katedra']);
        });
        Schema::dropIfExists('pozicija_na_katedri');
    }
};

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
            $table->enum("pozicija", ["Šef katedre", "Zamenik katedre"]);
            $table->foreignId("zaposleni_id")->references("id")->on("zaposleni");
            $table->foreignId("katedra_id")->references("id")->on("katedra");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pozicija_na_katedri', function (Blueprint $table){
            $table->dropForeign(['zaposleni_id']);
            $table->dropColumn(['zaposleni_id']);

            $table->dropForeign(['katedra_id']);
            $table->dropColumn(['katedra_id']);
        });
        Schema::dropIfExists('pozicija_na_katedri');
    }
};

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
        Schema::create('angazovanje_na_katedri', function (Blueprint $table) {
            $table->id();
            $table->date("datum_od");
            $table->date("datum_do")->nullable();
            $table->foreignId("katedra_id")->references("id")->on("katedra");
            $table->foreignId("zaposleni_id")->references("id")->on("zaposleni");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('angazovanje_na_katedri', function (Blueprint $table){
            $table->dropForeign(['katedra_id']);
            $table->dropColumn(['katedra_id']);

            $table->dropForeign(['zaposleni_id']);
            $table->dropColumn(['zaposleni_id']);
        });
        Schema::dropIfExists('angazovanje_na_katedri');
    }
};

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
        Schema::create('izbor_u_zvanje', function (Blueprint $table) {
            $table->id();
            $table->date("datum_od");
            $table->date("datum_do")->nullable();
            $table->foreignId("zvanje_id")->references("id")->on("zvanje");
            $table->foreignId("zaposleni_id")->references("id")->on("zaposleni");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izbor_u_zvanje', function (Blueprint $table) {
            $table->dropForeign(['zvanje_id']);
            $table->dropColumn(['zvanje_id']);

            $table->dropForeign(['zaposleni_id']);
            $table->dropColumn(['zaposleni_id']);
        });

        Schema::dropIfExists('izbor_u_zvanje');
    }
};

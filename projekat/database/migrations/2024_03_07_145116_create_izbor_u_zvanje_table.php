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
            $table->foreignId("zvanje")->references("id")->on("zvanje");
            $table->foreignId("zaposleni")->references("id")->on("zaposleni");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izbor_u_zvanje', function (Blueprint $table) {
            $table->dropForeign(['zvanje']);
            $table->dropColumn(['zvanje']);

            $table->dropForeign(['zaposleni']);
            $table->dropColumn(['zaposleni']);
        });

        Schema::dropIfExists('izbor_u_zvanje');
    }
};

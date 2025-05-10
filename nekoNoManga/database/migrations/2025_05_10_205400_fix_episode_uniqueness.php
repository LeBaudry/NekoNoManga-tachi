<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('episodes', function (Blueprint $table) {
            // Supprime l'unique sur mal_id
            $table->dropUnique('episodes_mal_id_unique');

            // Ajoute un unique composite pour éviter les doublons d'un même numéro
            $table->unique(['anime_id', 'numero'], 'episodes_anime_numero_unique');
        });
    }

    public function down()
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropUnique('episodes_anime_numero_unique');
            $table->unique('mal_id', 'episodes_mal_id_unique');
        });
    }
};

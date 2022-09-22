<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagazinesAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magazines_authors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('magazine_id');
            $table->bigInteger('author_id');
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');

            $table
                ->foreign('magazine_id')
                ->references('id')
                ->on('magazines')
                ->onDelete('cascade')
            ;

            $table
                ->foreign('author_id')
                ->references('id')
                ->on('authors')
                ->onDelete('restrict')
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magazines_authors');
    }
}

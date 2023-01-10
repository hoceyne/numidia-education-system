<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('members')->default(0);
            $table->integer('capacity');

            $table->uuid('teacher_id')->nullable();
            $table->foreign('teacher_id')->references('id')->on('teachers');
            $table->uuid('departement_id')->nullable();
            $table->foreign('departement_id')->references('id')->on('departements');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
};

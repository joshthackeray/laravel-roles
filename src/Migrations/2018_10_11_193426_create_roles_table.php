<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name')->unique();
            $table->string('label');
            $table->longText('description')->nullable();
            $table->smallInteger('status');
        });

        Schema::create('role_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('relation_type');
            $table->integer('relation_id')->unsigned();
            $table->integer('role_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_relations');
        Schema::dropIfExists('roles');
    }
}

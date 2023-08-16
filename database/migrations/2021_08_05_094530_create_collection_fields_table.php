<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_fields', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60);
            $table->string('label', 60);
            $table->string('name', 60);
            $table->string('description')->nullable();
            $table->string('placeholder')->nullable();
            $table->json('options')->nullable();
            $table->json('validations')->nullable();
            $table->integer('project_id');
            $table->integer('collection_id');
            $table->integer('order')->nullable();
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
        Schema::dropIfExists('collection_fields');
    }
}

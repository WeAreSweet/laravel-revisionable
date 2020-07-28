<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by TablePlus 3.7.0(327)
 * @author https://tableplus.com
 * @source https://github.com/TablePlus/tabledump
 */
class CreateRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revisions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('data')->nullable();
            $table->unsignedBigInteger('revisionable_id')->nullable();
            $table->text('revisionable_type')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->datetime('created_at')->nullable();
            $table->datetime('calculated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('revision');
    }
}
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtmRecorderTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $link_with = config('utm-recorder.link_visits_with');
        $utms = config('utm-recorder.record_attributes');

        Schema::create('visits', function (Blueprint $table) use ($link_with) {
            $table->increments('id');
            $table->integer('owner_id')->unsigned();
            $table->foreign('owner_id')
                ->references('id')
                ->on($link_with)
                ->onDelete('cascade');
            $table->string('url');
            $table->string('referrer_domain')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->string('referrer_url')->nullable();
            $table->string('method')->nullable();
            $table->index('owner_id');
            $table->timestamps();
        });

        Schema::create('utm_params', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('utm_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('visit_id')->unsigned();
            $table->foreign('visit_id')
                ->references('id')
                ->on('visits')
                ->onDelete('cascade');
            $table->integer('utm_param_id')->unsigned();
            $table->foreign('utm_param_id')
                ->references('id')
                ->on('utm_params')
                ->onDelete('cascade');
            $table->index(['utm_param_id', 'visit_id']);
            $table->string('content');
        });

        $table = DB::table('utm_params');
        foreach ($utms as $utm) {
            $table->insert([
                'name' => $utm,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('utm_contents');
        Schema::drop('visits');
        Schema::drop('utm_params');
    }
}

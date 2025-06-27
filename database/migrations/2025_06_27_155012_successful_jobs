<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('successful_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('connection');
            $table->string('queue');
            $table->longText('payload');
            $table->timestamp('processed_at')->useCurrent();
        });
    }
};

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
        //
        Schema::create('job_it', function (Blueprint $table) {
            $table->id();
            $table->string('job_code');
            $table->string('title');
            $table->string('location');
            $table->text('description')->nullable();
            $table->integer('type')->default(0);
            $table->integer('status')->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('job_it');
    }
};

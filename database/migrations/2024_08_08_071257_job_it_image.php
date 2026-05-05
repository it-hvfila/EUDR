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
        Schema::create('job_it_image', function (Blueprint $table) {
            $table->id();
            $table->string('job_code');
            $table->binary('image')->nullable(); // คอลัมน์ BLOB สำหรับเก็บข้อมูลรูปภาพ
            $table->string('image_extension')->nullable(); // คอลัมน์สำหรับเก็บนามสกุลไฟล์
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('job_it_image');
    }
};

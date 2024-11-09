<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->tinyInteger("checked")->default(0)->index();
           // $table->bigInteger('version')->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('file_holder_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('file_holder_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'file_holder_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};

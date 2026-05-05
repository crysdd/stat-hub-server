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
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->text('user_agent');
            $table->string('browser_name')->default('');
            $table->string('browser_version')->default('');
            $table->string('os_name')->default('');
            $table->string('screen_resolution')->default('');
            $table->integer('color_depth')->default(24);
            $table->string('language')->default('');
            $table->text('host')->default('');
            $table->text('page_url')->default('');
            $table->string('client_ip')->default('');
            $table->text('referrer')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->default('web');
            $table->string('language', 8)->default('en');
            $table->string('location')->nullable();
            $table->enum('status', ['open', 'pending_agent', 'resolved'])->default('open');
            $table->string('external_id')->nullable(); // ID from helpdesk
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};



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
        Schema::create('site_views', function (Blueprint $table) {
            $table->id();
            $table->string('ip_hash', 64)->index(); // SHA-256 hashed IP for privacy
            $table->date('viewed_at')->index();
            $table->timestamp('created_at')->useCurrent();

            // Prevent duplicate counts: one view per IP per day
            $table->unique(['ip_hash', 'viewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_views');
    }
};

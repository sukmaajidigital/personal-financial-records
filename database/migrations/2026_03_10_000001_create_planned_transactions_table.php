<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planned_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['income', 'expense']);
            $table->date('planned_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'posted'])->default('draft');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planned_transactions');
    }
};

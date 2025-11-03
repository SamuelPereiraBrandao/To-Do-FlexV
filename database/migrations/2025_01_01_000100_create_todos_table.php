<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // criador
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete(); // atribuído
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('theme_id')->nullable();
            $table->boolean('done')->default(false);
            $table->timestamp('due_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};


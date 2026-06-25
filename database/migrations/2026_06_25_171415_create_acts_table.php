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
      Schema::create('acts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained();

            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();

            $table->boolean('is_signed')->default(false);
            $table->timestamp('signed_at')->nullable();

            $table->text('manager_comment')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acts');
    }
};

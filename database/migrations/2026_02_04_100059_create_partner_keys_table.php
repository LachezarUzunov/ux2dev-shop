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
        Schema::create('partner_keys', function (Blueprint $table) {
            # IDs
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();

            # Strings
            $table->string('key_hash')->unique();
            $table->string('key_prefix')->index();
            $table->string('rate_limit_plan')->default('StartUp Plan');

            # Timestamps
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_keys');
    }
};

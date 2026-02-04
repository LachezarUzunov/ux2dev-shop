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
        Schema::create('idempotency_keys', function (Blueprint $table) {
            # IDs
            $table->id();
            $table->foreignId('partner_id')->constrained();

            # Strings
            $table->string('idempotency_key');
            $table->string('request_hash');

            $table->json('response_body')->nullable();
            $table->integer('response_code')->nullable();

            # Timestamps
            $table->timestamp('expires_at')->index();
            $table->timestamps();

            $table->unique(['partner_id', 'idempotency_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
    }
};

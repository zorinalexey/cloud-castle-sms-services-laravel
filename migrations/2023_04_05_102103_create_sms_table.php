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
        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->string('provider_class', 250);
            $table->string('provider_name');
            $table->integer('message_id')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('client_ip', 20)->nullable();
            $table->float('balance', 20, 2)->nullable()->default(0);
            $table->string('type', 10);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('inn', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->double('commission', 3, 2)->default(0);
            $table->boolean('is_active')->default(1);
            $table->boolean('auto')->default(0);
            $table->jsonb('config')->nullable()->default(json_encode([
                'auth' => [
                    'username' => null,
                    'password' => null,
                    'token' => null,
                ],
                'webhook' => [
                    'host' => null,
                    'token' => null,
                    'status' => false
                ],
                'sms' => [
                    'host' => null,
                    'token' => null,
                    'status' => false
                ],
                'notifications' => [
                    'payment' => 0,
                    'warnings' => 0, // checkDebit errors, webhook errors
                ]
            ]));
            $table->unsignedInteger('created_by')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};

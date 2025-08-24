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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();

            // --- General ---
            $table->unsignedBigInteger('partner_id')->index()->comment('Partner ID');
            $table->string('ext_id', 64)->unique()->comment('External transfer ID from partner');

            // --- Receiver ---
            $table->string('receiver_account_type', 32)->comment('e.g. CARD or TOKEN');
            $table->string('receiver_account', 64)->comment('Card number or token');

            // --- Debit (sender → system) ---
            $table->decimal('debit_amount', 18, 2)->comment('Debit amount in minor units');
            $table->string('debit_currency', 3)->comment('Debit currency ISO alpha/numeric');
            $table->string('debit_description', 255)->nullable();
            $table->decimal('debit_commission', 18, 2)->default(0);
            $table->string('debit_form_url', 255)->nullable()->comment('Payment form URL for 3DS');
            $table->string('debit_ref_num', 64)->nullable()->comment('Reference number from processing');

            // --- Credit (system → receiver) ---
            $table->enum('credit_stage', ['nmtCheck', 'clientCheck', 'payment'])
                ->default('nmtCheck')->comment('Current credit stage');
            $table->decimal('exchange_rate', 18, 6)->nullable();

            $table->decimal('credit_amount', 18, 2)->nullable();
            $table->string('credit_currency', 3)->nullable();
            $table->string('credit_ref_num', 64)->nullable()->comment('Partner pay_id or external ref');

            // --- Sender ---
            $table->string('sender_account', 64)->nullable();
            $table->string('sender_name', 128)->nullable();
            $table->string('sender_surname', 128)->nullable();
            $table->string('sender_series', 32)->nullable()->comment('Passport/ID series');
            $table->date('sender_birth_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};

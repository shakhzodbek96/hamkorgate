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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->integer('partner_id')->default(0);
            $table->string('name')->index();
            $table->string('guard_name')->default('web');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_user', function (Blueprint $table) {
            $table->dropForeign(['role_id']); // Replace 'role_id' with your actual foreign key column name
        });

        Schema::table('permission_role', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });

        // Now drop the roles table
        Schema::dropIfExists('roles');
    }


};

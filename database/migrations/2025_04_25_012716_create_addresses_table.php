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
        Schema::create('addresses', function (Blueprint $table) {
            $table->integer('address_id', true);
            $table->integer('address_type_id')->index('address_type_id');
            $table->integer('employee_id');
            $table->string('address_street');
            $table->string('street_number', 50);
            $table->string('suite_number', 50)->nullable();
            $table->string('neighbourhood', 100)->nullable();
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('zip_code', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

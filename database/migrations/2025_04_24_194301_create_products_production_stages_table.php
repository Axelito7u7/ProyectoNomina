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
        Schema::create('products_production_stages', function (Blueprint $table) {
            $table->integer('production_stages_id', true);
            $table->integer('stage_types_id')->index('stage_types_id');
            $table->string('name');
            $table->boolean('it_is_sellable');
            $table->integer('quantity_to_produce');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_production_stages');
    }
};

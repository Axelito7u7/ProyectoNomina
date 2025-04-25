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
        Schema::create('activity_log', function (Blueprint $table) {
            $table->integer('activity_log_id', true);
            $table->integer('production_stages_id')->index('production_stages_id');
            $table->integer('employee_id')->index('employee_id');
            $table->integer('biweekly_id')->index('biweekly_id');
            $table->date('date_production');
            $table->integer('quantity_produced');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};

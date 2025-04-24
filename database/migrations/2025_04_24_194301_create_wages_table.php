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
        Schema::create('wages', function (Blueprint $table) {
            $table->integer('activity_log_id');
            $table->integer('biweekly_id')->index('biweekly_id');
            $table->decimal('pay_by_day_and_number', 10);
            $table->date('processing_date');

            $table->primary(['activity_log_id', 'biweekly_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wages');
    }
};

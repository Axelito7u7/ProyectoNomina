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
        Schema::create('biweekly', function (Blueprint $table) {
            $table->integer('biweekly_id', true);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('payment_date');
            $table->string('day_for_biweekly', 50);
            $table->integer('wage_by_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biweekly');
    }
};

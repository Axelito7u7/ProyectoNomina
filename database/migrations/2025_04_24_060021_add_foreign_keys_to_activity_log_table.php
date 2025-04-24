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
        Schema::table('activity_log', function (Blueprint $table) {
            $table->foreign(['production_stages_id'], 'activity_log_ibfk_1')->references(['production_stages_id'])->on('products_production_stages')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['employee_id'], 'activity_log_ibfk_2')->references(['employee_id'])->on('employees')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['biweekly_id'], 'activity_log_ibfk_3')->references(['biweekly_id'])->on('biweekly')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropForeign('activity_log_ibfk_1');
            $table->dropForeign('activity_log_ibfk_2');
            $table->dropForeign('activity_log_ibfk_3');
        });
    }
};

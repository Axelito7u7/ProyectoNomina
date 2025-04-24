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
        Schema::table('wages', function (Blueprint $table) {
            $table->foreign(['activity_log_id'], 'wages_ibfk_1')->references(['activity_log_id'])->on('activity_log')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['biweekly_id'], 'wages_ibfk_2')->references(['biweekly_id'])->on('biweekly')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wages', function (Blueprint $table) {
            $table->dropForeign('wages_ibfk_1');
            $table->dropForeign('wages_ibfk_2');
        });
    }
};

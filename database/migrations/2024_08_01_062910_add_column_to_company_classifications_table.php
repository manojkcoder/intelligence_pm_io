<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void{
        Schema::table('company_classifications',function(Blueprint $table){
            $table->integer('revenue_max')->nullable()->after('revenue_threshold');
            $table->integer('employee_max')->nullable()->after('employee_threshold');
            $table->text('wz_codes')->nullable()->after('employee_max');
        });
    }
    public function down(): void{
        Schema::table('company_classifications',function(Blueprint $table){
            $table->dropColumn(['revenue_max','employee_max','wz_codes']);
        });
    }
};
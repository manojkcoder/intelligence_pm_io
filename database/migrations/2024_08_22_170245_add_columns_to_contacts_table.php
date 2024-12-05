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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('email_domain')->nullable();
            $table->string('approach')->nullable();
            $table->string('target_category')->nullable();
            $table->string('linkedin_hub_url')->nullable();
            $table->string('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('email_domain');
            $table->dropColumn('approach');
            $table->dropColumn('target_category');
            $table->dropColumn('linkedin_hub_url');
            $table->dropColumn('country');
        });
    }
};

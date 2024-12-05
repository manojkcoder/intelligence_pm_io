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
        Schema::table('industries', function (Blueprint $table) {
            $table->string('market_size_competitive_intensity')->nullable();
            $table->string('market_size_competitive_intensity_weight')->nullable();
            $table->string('growth_potential')->nullable();
            $table->string('growth_potential_weight')->nullable();
            $table->string('regulatory_framework_evaluation')->nullable();
            $table->string('regulatory_framework_evaluation_weight')->nullable();
            $table->string('technology_adoption')->nullable();
            $table->string('technology_adoption_weight')->nullable();
            $table->string('willingness_to_pay_for_saas_tools')->nullable();
            $table->string('willingness_to_pay_for_saas_tools_weight')->nullable();
            $table->string('dependence_on_software_solutions')->nullable();
            $table->string('dependence_on_software_solutions_weight')->nullable();
            $table->string('pressure_for_change')->nullable();
            $table->string('pressure_for_change_weight')->nullable();
            $table->string('consulting_affinity')->nullable();
            $table->string('consulting_affinity_weight')->nullable();
            $table->string('cost_pressure')->nullable();
            $table->string('cost_pressure_weight')->nullable();
            $table->string('financial_strength')->nullable();
            $table->string('financial_strength_weight')->nullable();
            $table->string('openness_to_innovation')->nullable();
            $table->string('openness_to_innovation_weight')->nullable();
            $table->string('agility_index')->nullable();
            $table->string('agility_index_weight')->nullable();
            $table->string('urge_for_diversification')->nullable();
            $table->string('urge_for_diversification_weight')->nullable();
            $table->string('epm_ready')->nullable();
            $table->string('epm_ready_weight')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('industries', function (Blueprint $table) {
            $table->dropColumn('market_size_competitive_intensity');
            $table->dropColumn('market_size_competitive_intensity_weight');
            $table->dropColumn('growth_potential');
            $table->dropColumn('growth_potential_weight');
            $table->dropColumn('regulatory_framework_evaluation');
            $table->dropColumn('regulatory_framework_evaluation_weight');
            $table->dropColumn('technology_adoption');
            $table->dropColumn('technology_adoption_weight');
            $table->dropColumn('willingness_to_pay_for_saas_tools');
            $table->dropColumn('willingness_to_pay_for_saas_tools_weight');
            $table->dropColumn('dependence_on_software_solutions');
            $table->dropColumn('dependence_on_software_solutions_weight');
            $table->dropColumn('pressure_for_change');
            $table->dropColumn('pressure_for_change_weight');
            $table->dropColumn('consulting_affinity');
            $table->dropColumn('consulting_affinity_weight');
            $table->dropColumn('cost_pressure');
            $table->dropColumn('cost_pressure_weight');
            $table->dropColumn('financial_strength');
            $table->dropColumn('financial_strength_weight');
            $table->dropColumn('openness_to_innovation');
            $table->dropColumn('openness_to_innovation_weight');
            $table->dropColumn('agility_index');
            $table->dropColumn('agility_index_weight');
            $table->dropColumn('urge_for_diversification');
            $table->dropColumn('urge_for_diversification_weight');
            $table->dropColumn('epm_ready');
            $table->dropColumn('epm_ready_weight');
        });
    }
};

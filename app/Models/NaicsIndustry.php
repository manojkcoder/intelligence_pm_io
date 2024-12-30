<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NaicsIndustry extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch',
        'associated_industries',
        'code',
        'score',
        'enabled',
        'market_size_competitive_intensity',
        'market_size_competitive_intensity_weight',
        'growth_potential',
        'growth_potential_weight',
        'regulatory_framework_evaluation',
        'regulatory_framework_evaluation_weight',
        'technology_adoption',
        'technology_adoption_weight',
        'willingness_to_pay_for_saas_tools',
        'willingness_to_pay_for_saas_tools_weight',
        'dependence_on_software_solutions',
        'dependence_on_software_solutions_weight',
        'pressure_for_change',
        'pressure_for_change_weight',
        'consulting_affinity',
        'consulting_affinity_weight',
        'cost_pressure',
        'cost_pressure_weight',
        'financial_strength',
        'financial_strength_weight',
        'openness_to_innovation',
        'openness_to_innovation_weight',
        'agility_index',
        'agility_index_weight',
        'urge_for_diversification',
        'urge_for_diversification_weight',
        'epm_ready',
        'epm_ready_weight'
    ];
}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Industry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm p-8">
                <form method="POST" action="{{ route('naics_industries.update', $industry->id) }}">
                    @method('PATCH')
                    @csrf
                    <div class="flex flex-wrap gap-y-4">
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="code">NAICS Code</label>
                            <input id="code" class="block mt-1 w-full" type="text" name="code" value="{{ $industry->code }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="score">Score</label>
                            <input id="score" class="block mt-1 w-full" type="text" name="score" value="{{ $industry->score }}" required autofocus />
                        </div>
                        <div class="w-full flex gap-2">
                            <input type="hidden" name="enabled" value="0">
                            <input id="enabled" class="block mt-1" type="checkbox" name="enabled" {{ $industry->enabled ? 'checked' : '' }} required autofocus />
                            <label for="enabled">Enabled</label>
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="market_size_competitive_intensity">Market Size Competitive Intensity</label>
                            <input id="market_size_competitive_intensity" class="block mt-1 w-full" type="text" name="market_size_competitive_intensity" value="{{ $industry->market_size_competitive_intensity }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="market_size_competitive_intensity_weight">Market Size Competitive Intensity Weight</label>
                            <input id="market_size_competitive_intensity_weight" class="block mt-1 w-full" type="text" name="market_size_competitive_intensity_weight" value="{{ $industry->market_size_competitive_intensity_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="growth_potential">Growth Potential</label>
                            <input id="growth_potential" class="block mt-1 w-full" type="text" name="growth_potential" value="{{ $industry->growth_potential }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="growth_potential_weight">Growth Potential Weight</label>
                            <input id="growth_potential_weight" class="block mt-1 w-full" type="text" name="growth_potential_weight" value="{{ $industry->growth_potential_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="regulatory_framework_evaluation">Regulatory Framework Evaluation</label>
                            <input id="regulatory_framework_evaluation" class="block mt-1 w-full" type="text" name="regulatory_framework_evaluation" value="{{ $industry->regulatory_framework_evaluation }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="regulatory_framework_evaluation_weight">Regulatory Framework Evaluation Weight</label>
                            <input id="regulatory_framework_evaluation_weight" class="block mt-1 w-full" type="text" name="regulatory_framework_evaluation_weight" value="{{ $industry->regulatory_framework_evaluation_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="technology_adoption">Technology Adoption</label>
                            <input id="technology_adoption" class="block mt-1 w-full" type="text" name="technology_adoption" value="{{ $industry->technology_adoption }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="technology_adoption_weight">Technology Adoption Weight</label>
                            <input id="technology_adoption_weight" class="block mt-1 w-full" type="text" name="technology_adoption_weight" value="{{ $industry->technology_adoption_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="willingness_to_pay_for_saas_tools">Willingness to Pay for SaaS Tools</label>
                            <input id="willingness_to_pay_for_saas_tools" class="block mt-1 w-full" type="text" name="willingness_to_pay_for_saas_tools" value="{{ $industry->willingness_to_pay_for_saas_tools }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="willingness_to_pay_for_saas_tools_weight">Willingness to Pay for SaaS Tools Weight</label>
                            <input id="willingness_to_pay_for_saas_tools_weight" class="block mt-1 w-full" type="text" name="willingness_to_pay_for_saas_tools_weight" value="{{ $industry->willingness_to_pay_for_saas_tools_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="dependence_on_software_solutions">Dependence on Software Solutions</label>
                            <input id="dependence_on_software_solutions" class="block mt-1 w-full" type="text" name="dependence_on_software_solutions" value="{{ $industry->dependence_on_software_solutions }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="dependence_on_software_solutions_weight">Dependence on Software Solutions Weight</label>
                            <input id="dependence_on_software_solutions_weight" class="block mt-1 w-full" type="text" name="dependence_on_software_solutions_weight" value="{{ $industry->dependence_on_software_solutions_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="pressure_for_change">Pressure for Change</label>
                            <input id="pressure_for_change" class="block mt-1 w-full" type="text" name="pressure_for_change" value="{{ $industry->pressure_for_change }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="pressure_for_change_weight">Pressure for Change Weight</label>
                            <input id="pressure_for_change_weight" class="block mt-1 w-full" type="text" name="pressure_for_change_weight" value="{{ $industry->pressure_for_change_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="consulting_affinity">Consulting Affinity</label>
                            <input id="consulting_affinity" class="block mt-1 w-full" type="text" name="consulting_affinity" value="{{ $industry->consulting_affinity }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="consulting_affinity_weight">Consulting Affinity Weight</label>
                            <input id="consulting_affinity_weight" class="block mt-1 w-full" type="text" name="consulting_affinity_weight" value="{{ $industry->consulting_affinity_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="cost_pressure">Cost Pressure</label>
                            <input id="cost_pressure" class="block mt-1 w-full" type="text" name="cost_pressure" value="{{ $industry->cost_pressure }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="cost_pressure_weight">Cost Pressure Weight</label>
                            <input id="cost_pressure_weight" class="block mt-1 w-full" type="text" name="cost_pressure_weight" value="{{ $industry->cost_pressure_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="financial_strength">Financial Strength</label>
                            <input id="financial_strength" class="block mt-1 w-full" type="text" name="financial_strength" value="{{ $industry->financial_strength }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="financial_strength_weight">Financial Strength Weight</label>
                            <input id="financial_strength_weight" class="block mt-1 w-full" type="text" name="financial_strength_weight" value="{{ $industry->financial_strength_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="openness_to_innovation">Openness to Innovation</label>
                            <input id="openness_to_innovation" class="block mt-1 w-full" type="text" name="openness_to_innovation" value="{{ $industry->openness_to_innovation }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="openness_to_innovation_weight">Openness to Innovation Weight</label>
                            <input id="openness_to_innovation_weight" class="block mt-1 w-full" type="text" name="openness_to_innovation_weight" value="{{ $industry->openness_to_innovation_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="agility_index">Low Security Requirements</label>
                            <input id="agility_index" class="block mt-1 w-full" type="text" name="agility_index" value="{{ $industry->agility_index }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="agility_index_weight">Low Security Requirements Weight</label>
                            <input id="agility_index_weight" class="block mt-1 w-full" type="text" name="agility_index_weight" value="{{ $industry->agility_index_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="urge_for_diversification">Urge for Diversification</label>
                            <input id="urge_for_diversification" class="block mt-1 w-full" type="text" name="urge_for_diversification" value="{{ $industry->urge_for_diversification }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="urge_for_diversification_weight">Urge for Diversification Weight</label>
                            <input id="urge_for_diversification_weight" class="block mt-1 w-full" type="text" name="urge_for_diversification_weight" value="{{ $industry->urge_for_diversification_weight }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="epm_ready">EPM Ready</label>
                            <input id="epm_ready" class="block mt-1 w-full" type="text" name="epm_ready" value="{{ $industry->epm_ready }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="epm_ready_weight">EPM Ready Weight</label>
                            <input id="epm_ready_weight" class="block mt-1 w-full" type="text" name="epm_ready_weight" value="{{ $industry->epm_ready_weight }}" required autofocus />
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <button class="ml-4 btn-bg-primary text-white  py-2 px-4">
                            {{ __('Update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style>
     @media(max-width:767px){
        form .flex div {
          width: 100%;
          padding:0px;
        }
        .ml-4.btn-bg-primary {
            width: 100%;
            margin: 0px;
        }
     }
    </style>
</x-app-layout>

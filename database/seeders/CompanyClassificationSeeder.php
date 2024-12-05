<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classifications = [
            [
                'name' => 'TAM',
                'description' => 'Total Addressable Market',
                'revenue_threshold' => 0,
                'employee_threshold' => 0,
                'score_threshold' => 0,
            ],
            [
                'name' => 'SAM',
                'description' => 'Serviceable Addressable Market',
                'revenue_threshold' => 100,
                'employee_threshold' => 500,
                'score_threshold' => 65,
            ],
            [
                'name' => 'SOM',
                'description' => 'Serviceable Obtainable Market',
                'revenue_threshold' => 100000,
                'employee_threshold' => 1000,
                'score_threshold' => 79,
            ],
        ];

        foreach ($classifications as $classification) {
            \App\Models\CompanyClassification::create($classification);
        }
    }
}

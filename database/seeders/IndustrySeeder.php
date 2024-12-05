<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Read from storage industries.json
        $industries = json_decode(file_get_contents(storage_path('industries.json')), true);
        foreach ($industries as $industry) {
            \App\Models\Industry::create($industry);
        }
        
    }
}

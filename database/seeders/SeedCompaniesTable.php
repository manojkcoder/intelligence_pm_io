<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeedCompaniesTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // get companies data from companies.txt file stored in storage/app folder
        $companies = file_get_contents(storage_path('app/companies.txt'));
        $companies = explode("\n", $companies);
        $companies = array_map(function ($company) {
            return ['name' => $company];
        }, $companies);
        
        \DB::table('companies')->truncate();

        foreach ($companies as $company) {
            \DB::table('companies')->insert($company);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class SeedCompaniesTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // get companies data from companies.txt file stored in storage/app folder
        $companies = file_get_contents(storage_path('app/companies.json'));
        $companies = json_decode($companies, true);

        Company::truncate();

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}

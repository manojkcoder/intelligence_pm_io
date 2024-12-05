<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// storage
use Illuminate\Support\Facades\Storage;

class ProcessIterative implements ShouldQueue
{
    use Queueable;

    protected $page;

    /**
     * Create a new job instance.
     */
    public function __construct($page = 1)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $offset = 0;
        // return if file already exists
        if (Storage::exists('raw/' . ($this->page + $offset) . '.json')) {
          // ProcessIterative::dispatch($this->page + 1);
          return;
        }
        

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://dashboard-services.lusha.com/v2/prospecting-full',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
    "filters": {
        "companyIndustryLabels": [
            {
                "value": "Facilities Services",
                "id": 6,
                "mainIndustry": "Administrative & Support Services",
                "mainIndustryId": 2,
                "subIndustriesCount": 8
            },
            {
                "value": "Translation & Localization",
                "id": 10,
                "mainIndustry": "Administrative & Support Services",
                "mainIndustryId": 2,
                "subIndustriesCount": 8
            },
            {
                "value": "Travel Arrangements",
                "id": 11,
                "mainIndustry": "Administrative & Support Services",
                "mainIndustryId": 2,
                "subIndustriesCount": 8
            },
            {
                "value": "Security & Investigations",
                "id": 8,
                "mainIndustry": "Administrative & Support Services",
                "mainIndustryId": 2,
                "subIndustriesCount": 8
            },
            {
                "value": "Staffing & Recruiting",
                "id": 9,
                "mainIndustry": "Administrative & Support Services",
                "mainIndustryId": 2,
                "subIndustriesCount": 8
            },
            {
                "value": "Events Services",
                "id": 5,
                "mainIndustry": "Administrative & Support Services",
                "mainIndustryId": 2,
                "subIndustriesCount": 8
            },
            {
                "value": "Fundraising",
                "id": 7,
                "mainIndustry": "Administrative & Support Services",
                "mainIndustryId": 2,
                "subIndustriesCount": 8
            },
            {
                "value": "Writing & Editing",
                "id": 12,
                "mainIndustry": "Administrative & Support Services",
                "mainIndustryId": 2,
                "subIndustriesCount": 8
            },
            {
                "value": "Biotechnology Research Services",
                "id": 106,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Advertising & Marketing Services",
                "id": 93,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Law Firms & Legal Services",
                "id": 104,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Market Research Services",
                "id": 95,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Environmental Services",
                "id": 98,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Outsourcing & Offshoring Consulting",
                "id": 100,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Veterinary Services",
                "id": 108,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Photography Services",
                "id": 105,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Public Relations & Communications Services",
                "id": 94,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Architecture & Planning",
                "id": 96,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Business Consulting & Services",
                "id": 97,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Research Services",
                "id": 107,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Human Resources Services",
                "id": 99,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "IT Consulting & IT Services",
                "id": 103,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Accounting & Services",
                "id": 92,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Design Services",
                "id": 101,
                "mainIndustry": "Business Services",
                "mainIndustryId": 14,
                "subIndustriesCount": 16
            },
            {
                "value": "Civil Engineering",
                "id": 16,
                "mainIndustry": "Construction",
                "mainIndustryId": 3,
                "subIndustriesCount": 2
            },
            {
                "value": "Building Construction",
                "id": 15,
                "mainIndustry": "Construction",
                "mainIndustryId": 3,
                "subIndustriesCount": 2
            },
            {
                "value": "Philanthropic Fundraising Services",
                "id": 18,
                "mainIndustry": "Consumer Services",
                "mainIndustryId": 4,
                "subIndustriesCount": 3
            },
            {
                "value": "Repair & Maintenance",
                "id": 19,
                "mainIndustry": "Consumer Services",
                "mainIndustryId": 4,
                "subIndustriesCount": 3
            },
            {
                "value": "Personal care services",
                "id": 17,
                "mainIndustry": "Consumer Services",
                "mainIndustryId": 4,
                "subIndustriesCount": 3
            },
            {
                "value": "Higher Education",
                "id": 24,
                "mainIndustry": "Education",
                "mainIndustryId": 6,
                "subIndustriesCount": 5
            },
            {
                "value": "Training",
                "id": 26,
                "mainIndustry": "Education",
                "mainIndustryId": 6,
                "subIndustriesCount": 5
            },
            {
                "value": "Primary & Secondary Education",
                "id": 25,
                "mainIndustry": "Education",
                "mainIndustryId": 6,
                "subIndustriesCount": 5
            },
            {
                "value": "E-Learning Providers",
                "id": 23,
                "mainIndustry": "Education",
                "mainIndustryId": 6,
                "subIndustriesCount": 5
            },
            {
                "value": "Schools",
                "id": 27,
                "mainIndustry": "Education",
                "mainIndustryId": 6,
                "subIndustriesCount": 5
            },
            {
                "value": "Museums, Historical Sites, & Zoos",
                "id": 29,
                "mainIndustry": "Entertainment",
                "mainIndustryId": 7,
                "subIndustriesCount": 8
            },
            {
                "value": "Entertainment Providers",
                "id": 28,
                "mainIndustry": "Entertainment",
                "mainIndustryId": 7,
                "subIndustriesCount": 8
            },
            {
                "value": "Performing Arts",
                "id": 31,
                "mainIndustry": "Entertainment",
                "mainIndustryId": 7,
                "subIndustriesCount": 8
            },
            {
                "value": "Recreational Facilities",
                "id": 33,
                "mainIndustry": "Entertainment",
                "mainIndustryId": 7,
                "subIndustriesCount": 8
            },
            {
                "value": "Wellness & Fitness Services",
                "id": 35,
                "mainIndustry": "Entertainment",
                "mainIndustryId": 7,
                "subIndustriesCount": 8
            },
            {
                "value": "Gambling Facilities & Casinos",
                "id": 34,
                "mainIndustry": "Entertainment",
                "mainIndustryId": 7,
                "subIndustriesCount": 8
            },
            {
                "value": "Sports",
                "id": 32,
                "mainIndustry": "Entertainment",
                "mainIndustryId": 7,
                "subIndustriesCount": 8
            },
            {
                "value": "Musicians, Artists & Writers",
                "id": 30,
                "mainIndustry": "Entertainment",
                "mainIndustryId": 7,
                "subIndustriesCount": 8
            },
            {
                "value": "Farming, Ranching, Forestry",
                "id": 8,
                "mainIndustry": "Farming, Ranching, Forestry",
                "mainIndustryId": 8,
                "subIndustriesCount": 1
            },
            {
                "value": "Banking",
                "id": 42,
                "mainIndustry": "Finance",
                "mainIndustryId": 9,
                "subIndustriesCount": 8
            },
            {
                "value": "International Trade & Development",
                "id": 43,
                "mainIndustry": "Finance",
                "mainIndustryId": 9,
                "subIndustriesCount": 8
            },
            {
                "value": "Capital Markets",
                "id": 38,
                "mainIndustry": "Finance",
                "mainIndustryId": 9,
                "subIndustriesCount": 8
            },
            {
                "value": "Insurance",
                "id": 44,
                "mainIndustry": "Finance",
                "mainIndustryId": 9,
                "subIndustriesCount": 8
            },
            {
                "value": "Venture Capital & Private Equity Principals",
                "id": 41,
                "mainIndustry": "Finance",
                "mainIndustryId": 9,
                "subIndustriesCount": 8
            },
            {
                "value": "Investment Management",
                "id": 40,
                "mainIndustry": "Finance",
                "mainIndustryId": 9,
                "subIndustriesCount": 8
            },
            {
                "value": "Investment Banking",
                "id": 39,
                "mainIndustry": "Finance",
                "mainIndustryId": 9,
                "subIndustriesCount": 8
            },
            {
                "value": "Financial Services",
                "id": 37,
                "mainIndustry": "Finance",
                "mainIndustryId": 9,
                "subIndustriesCount": 8
            },
            {
                "value": "Education Administration Programs",
                "id": 50,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Government Administration",
                "id": 45,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Military",
                "id": 53,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Administration of Justice",
                "id": 46,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Public Safety",
                "id": 49,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Public Policy Offices",
                "id": 55,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Government Relations Services",
                "id": 58,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "International Affairs",
                "id": 54,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Health & Human Services",
                "id": 51,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Housing & Community Development",
                "id": 52,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Executive Offices",
                "id": 56,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Legislative Offices",
                "id": 57,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Law Enforcement",
                "id": 48,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Fire Protection",
                "id": 47,
                "mainIndustry": "Government",
                "mainIndustryId": 10,
                "subIndustriesCount": 14
            },
            {
                "value": "Food & Beverage Services",
                "id": 1,
                "mainIndustry": "Hospitality",
                "mainIndustryId": 1,
                "subIndustriesCount": 3
            },
            {
                "value": "Restaurants",
                "id": 2,
                "mainIndustry": "Hospitality",
                "mainIndustryId": 1,
                "subIndustriesCount": 3
            },
            {
                "value": "Hotels & Motels",
                "id": 3,
                "mainIndustry": "Hospitality",
                "mainIndustryId": 1,
                "subIndustriesCount": 3
            },
            {
                "value": "Individual & Family Services",
                "id": 61,
                "mainIndustry": "Hospitals, Healthcare & Clinics",
                "mainIndustryId": 11,
                "subIndustriesCount": 8
            },
            {
                "value": "Alternative Medicine",
                "id": 62,
                "mainIndustry": "Hospitals, Healthcare & Clinics",
                "mainIndustryId": 11,
                "subIndustriesCount": 8
            },
            {
                "value": "Mental Health Care",
                "id": 64,
                "mainIndustry": "Hospitals, Healthcare & Clinics",
                "mainIndustryId": 11,
                "subIndustriesCount": 8
            },
            {
                "value": "Medical Practices",
                "id": 65,
                "mainIndustry": "Hospitals, Healthcare & Clinics",
                "mainIndustryId": 11,
                "subIndustriesCount": 8
            },
            {
                "value": "Hospitals & Healthcare",
                "id": 59,
                "mainIndustry": "Hospitals, Healthcare & Clinics",
                "mainIndustryId": 11,
                "subIndustriesCount": 8
            },
            {
                "value": "Nursing Homes & Residential Care Facilities",
                "id": 66,
                "mainIndustry": "Hospitals, Healthcare & Clinics",
                "mainIndustryId": 11,
                "subIndustriesCount": 8
            },
            {
                "value": "Community Services",
                "id": 60,
                "mainIndustry": "Hospitals, Healthcare & Clinics",
                "mainIndustryId": 11,
                "subIndustriesCount": 8
            },
            {
                "value": "Home Health Care Services",
                "id": 63,
                "mainIndustry": "Hospitals, Healthcare & Clinics",
                "mainIndustryId": 11,
                "subIndustriesCount": 8
            },
            {
                "value": "Medical Equipment",
                "id": 80,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Appliances, Electrical, & Electronics",
                "id": 68,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Pharmaceuticals",
                "id": 71,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Food & Beverage",
                "id": 76,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Computer Equipment & Electronics",
                "id": 72,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Railroad Equipment",
                "id": 88,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Apparel",
                "id": 67,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Personal Care Products",
                "id": 70,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Plastics & Rubber Products",
                "id": 82,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Chemicals & Related Products",
                "id": 69,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Sporting Goods",
                "id": 83,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Fabricated Metal Products",
                "id": 75,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Semiconductor & Renewable Energy Semiconductor",
                "id": 74,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Industrial Machinery & Equipment",
                "id": 79,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Motor Vehicles",
                "id": 87,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Textile",
                "id": 84,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Glass, Ceramics, Clay & Concrete",
                "id": 78,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Paper & Forest Product",
                "id": 81,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Furniture",
                "id": 77,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Aerospace & Defense",
                "id": 86,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Computer Hardware",
                "id": 73,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Tobacco",
                "id": 85,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Shipbuilding",
                "id": 89,
                "mainIndustry": "Manufacturing",
                "mainIndustryId": 12,
                "subIndustriesCount": 23
            },
            {
                "value": "Oil & Gas",
                "id": 91,
                "mainIndustry": "Oil, Gas & Mining",
                "mainIndustryId": 13,
                "subIndustriesCount": 2
            },
            {
                "value": "Mining",
                "id": 90,
                "mainIndustry": "Oil, Gas & Mining",
                "mainIndustryId": 13,
                "subIndustriesCount": 2
            },
            {
                "value": "Civic & Social Organizations",
                "id": 21,
                "mainIndustry": "Organizations",
                "mainIndustryId": 5,
                "subIndustriesCount": 3
            },
            {
                "value": "Political Organizations",
                "id": 20,
                "mainIndustry": "Organizations",
                "mainIndustryId": 5,
                "subIndustriesCount": 3
            },
            {
                "value": "Religious Institutions",
                "id": 22,
                "mainIndustry": "Organizations",
                "mainIndustryId": 5,
                "subIndustriesCount": 3
            },
            {
                "value": "Real Estate",
                "id": 15,
                "mainIndustry": "Real Estate",
                "mainIndustryId": 15,
                "subIndustriesCount": 1
            },
            {
                "value": "Grocery Retail",
                "id": 112,
                "mainIndustry": "Retail",
                "mainIndustryId": 16,
                "subIndustriesCount": 5
            },
            {
                "value": "Retail Apparel & Fashion",
                "id": 113,
                "mainIndustry": "Retail",
                "mainIndustryId": 16,
                "subIndustriesCount": 5
            },
            {
                "value": "Retail Office Equipment",
                "id": 114,
                "mainIndustry": "Retail",
                "mainIndustryId": 16,
                "subIndustriesCount": 5
            },
            {
                "value": "Retail Luxury Goods & Jewelry",
                "id": 110,
                "mainIndustry": "Retail",
                "mainIndustryId": 16,
                "subIndustriesCount": 5
            },
            {
                "value": "Food & Beverage Retail",
                "id": 111,
                "mainIndustry": "Retail",
                "mainIndustryId": 16,
                "subIndustriesCount": 5
            },
            {
                "value": "Broadcast Media Production & Distribution",
                "id": 117,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Social Networking Platforms",
                "id": 125,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Internet Publishing",
                "id": 123,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Computer & Mobile Games",
                "id": 126,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Computer Networking Products",
                "id": 127,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Book & Newspaper Publishing",
                "id": 116,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Computer & Network Security Services",
                "id": 128,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Blockchain Services",
                "id": 121,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Software Development",
                "id": 129,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Data Infrastructure & Analytics",
                "id": 120,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Telecommunications",
                "id": 119,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Movies, Videos & Sound",
                "id": 118,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Information Services",
                "id": 122,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Internet Shop & Marketplace",
                "id": 124,
                "mainIndustry": "Technology, Information & Media",
                "mainIndustryId": 17,
                "subIndustriesCount": 14
            },
            {
                "value": "Freight & Package Transportation",
                "id": 131,
                "mainIndustry": "Transportation, Logistics, Supply Chain & Storage",
                "mainIndustryId": 18,
                "subIndustriesCount": 6
            },
            {
                "value": "Ground Passenger Transportation",
                "id": 132,
                "mainIndustry": "Transportation, Logistics, Supply Chain & Storage",
                "mainIndustryId": 18,
                "subIndustriesCount": 6
            },
            {
                "value": "Maritime Transportation",
                "id": 133,
                "mainIndustry": "Transportation, Logistics, Supply Chain & Storage",
                "mainIndustryId": 18,
                "subIndustriesCount": 6
            },
            {
                "value": "Airlines, Airports & Air Services",
                "id": 130,
                "mainIndustry": "Transportation, Logistics, Supply Chain & Storage",
                "mainIndustryId": 18,
                "subIndustriesCount": 6
            },
            {
                "value": "Truck Transportation",
                "id": 134,
                "mainIndustry": "Transportation, Logistics, Supply Chain & Storage",
                "mainIndustryId": 18,
                "subIndustriesCount": 6
            },
            {
                "value": "Warehousing & Storage",
                "id": 135,
                "mainIndustry": "Transportation, Logistics, Supply Chain & Storage",
                "mainIndustryId": 18,
                "subIndustriesCount": 6
            },
            {
                "value": "Utilities",
                "id": 19,
                "mainIndustry": "Utilities",
                "mainIndustryId": 19,
                "subIndustriesCount": 1
            },
            {
                "value": "Wholesale Building Materials",
                "id": 138,
                "mainIndustry": "Wholesale",
                "mainIndustryId": 20,
                "subIndustriesCount": 2
            },
            {
                "value": "Wholesale Import & Export",
                "id": 139,
                "mainIndustry": "Wholesale",
                "mainIndustryId": 20,
                "subIndustriesCount": 2
            }
        ],
        "companyLocation": [
            {
                "country": "Germany",
                "country_grouping": "emea-datch",
                "continent": "Europe",
                "key": "country"
            }
        ],
        "companySize": [
            {
                "min": 10001
            },
            {
                "min": 5001,
                "max": 10000
            },
            {
                "min": 1001,
                "max": 5000
            },
            {
                "min": 501,
                "max": 1000
            }
        ]
    },
    "filtersMetadata": {},
    "display": "companies",
    "pages": {
        "page": '.$this->page.',
        "pageSize": 25
    },
    "sessionId": "f5f0f88b-1065-4ccb-8b32-b1cab1738c1b",
    "searchTrigger": "NewPage",
    "savedSearchId": 0,
    "bulkSearchCompanies": {},
    "isRecent": true,
    "isSaved": false,
    "pageAbove400": null,
    "totalPagesAbove400": 0,
    "excludeRevealedContacts": false,
    "fetchIntentTopics": true
}',
          CURLOPT_HTTPHEADER => array(
            '_csrf: GZ8nlX1uz4K9txy4SvC4SdWl',
            'accept: application/json',
            'accept-language: en-GB,en-IN;q=0.9,en-US;q=0.8,en;q=0.7',
            'content-type: application/json',
            'cookie: utms=[{%22leadId%22:null%2C%22luuid%22:%22VID.6152e94c-2a6d-48a2-81a4-481f8f8d1d2a%22%2C%22date%22:%222024-08-03%2011:51:11%22%2C%22lpUrl%22:%22https%253A%252F%252Fwww.lusha.com%252F%22%2C%22email%22:%22user@anonymous-user.com%22%2C%22hashedEmail%22:null%2C%22utm_source%22:%22direct%22%2C%22utm_medium%22:null}]; _gcl_au=1.1.1568922402.1722685872; _vid_=VID.759dd8b4-553d-48c7-af5c-c3defc77659a; _fbp=fb.1.1722685876819.776092611705924904; _tt_enable_cookie=1; _ttp=GVWEQijru1kOsYwQjwazhWRm2HG; intercom-id-yu27vhse=53719139-c9f5-4b92-ac0e-e449750025ee; intercom-device-id-yu27vhse=5c8d736e-d661-4838-aa26-1a1d0807e584; _gid=GA1.2.952090334.1723050527; rbzid=SpnF6StLG53Y6/FdSN1gJh5mQ774HNnZm563bqPbGfX1uYNveZDA6+olSvw2eVqlLOdqXDZPhDKlYN6Z+6M7+WQg+b7U6XWmVdaZrUd0zYkB0ebxPR3zzYRIW+/2CS+uhsycXxfHEEtwRFe+sb7tfkxf4X+8oOOxKnr4cOsv3e6QY5bgXVXeUo3dc7lYU3zURpS3ykSUnwU9K1dEZ+T3xDc9nvbI0hhbiTbGIjrA1NSAdf64DK9IcIYQuwvIsvRS; rbzsessionid=57a22bc3d97f02e96711e6b466458e84; ab.storage.deviceId.d3ea2daa-b1b1-453c-aeab-bf54fc5bb81b=g%3A9a9740c1-5f99-d4d5-0569-3180d2f29510%7Ce%3Aundefined%7Cc%3A1722685888266%7Cl%3A1723050533958; ab.storage.userId.d3ea2daa-b1b1-453c-aeab-bf54fc5bb81b=g%3A970ebfc66d50462a706a50d9e09a835f8a45392191d53595cde1ebeb5b74a8aa%7Ce%3Aundefined%7Cc%3A1723050533957%7Cl%3A1723050533958; _csrf=GZ8nlX1uz4K9txy4SvC4SdWl; XSRF-TOKEN=5JtSX1tG-qTP178WUDQB_ve4CegFeTs7noSM; _gat_UA-74444829-1=1; _ga=GA1.1.1290124555.1722685871; _clck=1le2am4%7C2%7Cfo5%7C0%7C1676; _clsk=18lnl4l%7C1723115055671%7C1%7C1%7Ct.clarity.ms%2Fcollect; ll=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsInR5cGUiOiJKV1QifQ.eyJpYXQiOjE3MjMxMTUwNTgsImV4cCI6MTcyNTUzNDI1OCwiYXVkIjoiaHR0cHM6Ly93d3cubHVzaGEuY28iLCJpc3MiOiJiY2MyMmQ4Yy01ZjlhLTQxMWItYTIyMS0yODY0M2JiNjk3MzAiLCJzdWIiOiJhbm9ueW1vdXMifQ.MowJZZ2MjhVhVb8SCASaforALxUrIRtNl020VZGAeSc; _ga_7C6N0WZ5Q3=GS1.1.1723115049.9.1.1723115059.50.0.0; _uetsid=ad8c0d9054df11ef867f0f9d1bdea9c9; _uetvid=b0f5d4a0518e11ef972119b53a5ca352; user_guid=default_server_guid; fs_uid=#GBNRN#2372576352370621892:3895870562186007957:::#b5097457#/1754586541; intercom-session-yu27vhse=emRzTjdRdDV2VXE0ZEhLMXQ2dWlocnI2ai92WG5VZXBUTjhxSklxamVzSDF5Y3RQRXFLcDhnekQvcGxCakV6Vi0tZ20rd2V4Wk9sakV6aEh0eHM2WWE1QT09--f62c701832f90731677d4ca5b0f55da3b70235fb; ab.storage.sessionId.d3ea2daa-b1b1-453c-aeab-bf54fc5bb81b=g%3Ac8c00261-6ed5-c554-8579-7c4617b87ff8%7Ce%3A1723116863172%7Cc%3A1723115051013%7Cl%3A1723115063172',
            'dnt: 1',
            'origin: https://dashboard.lusha.com',
            'priority: u=1, i',
            'referer: https://dashboard.lusha.com/',
            'sec-ch-ua: "Not)A;Brand";v="99", "Google Chrome";v="127", "Chromium";v="127"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "macOS"',
            'sec-fetch-dest: empty',
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-site',
            'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36',
            'x-px-cookies: ',
            'x-version: 1.0.0',
            'x-xsrf-token: wLUq5jV6-vzIEmgO9pTK2XV3hPpeO503Z6_g'
          ),
        ));

        $response = curl_exec($curl);


        $response = curl_exec($curl);

        curl_close($curl);
        
        Storage::put('raw/'.($this->page + $offset).'.json', $response);

        $data = json_decode($response, true);

        if(count($data['companies']['results'])){
            ProcessIterative::dispatch(++$this->page);
        }

    }
}

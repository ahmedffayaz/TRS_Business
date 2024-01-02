<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            // starting from ===============> A
            [
                'name' => 'Afghanistan',
                'official_name' => 'Islamic Republic of Afghanistan',
                'continent_name' => 'Asia',
                'alpha_2_code' => 'AF',
                'alpha_3_code' => 'AFG',
                'numeric_code' => '004',
                'country_code' => '93',
                'official_language' => 'Pashto',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Albania',
                'official_name' => 'Republic of Albania',
                'continent_name' => 'Europe',
                'alpha_2_code' => 'AL',
                'alpha_3_code' => 'ALB',
                'numeric_code' => '008',
                'country_code' => '355',
                'official_language' => 'Albanian',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Algeria',
                'official_name' => 'People\'s Democratic Republic of Algeria',
                'continent_name' => 'Africa',
                'alpha_2_code' => 'DZ',
                'alpha_3_code' => 'DZA',
                'numeric_code' => '012',
                'country_code' => '213',
                'official_language' => 'Arabic',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'American Samoa',
                'official_name' => 'American Samoa',
                'continent_name' => 'Oceania',
                'alpha_2_code' => 'AS',
                'alpha_3_code' => 'ASM',
                'numeric_code' => '016',
                'country_code' => '1-684',
                'official_language' => 'Samoan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Andorra',
                'official_name' => 'Principality of Andorra',
                'continent_name' => 'Europe',
                'alpha_2_code' => 'AD',
                'alpha_3_code' => 'AND',
                'numeric_code' => '020',
                'country_code' => '376',
                'official_language' => 'Catalan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Angola',
                'official_name' => 'Republic of Angola',
                'continent_name' => 'Africa',
                'alpha_2_code' => 'AO',
                'alpha_3_code' => 'AGO',
                'numeric_code' => '024',
                'country_code' => '244',
                'official_language' => 'Portuguese',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Anguilla',
                'official_name' => 'Anguilla',
                'continent_name' => 'North America',
                'alpha_2_code' => 'AI',
                'alpha_3_code' => 'AIA',
                'numeric_code' => '660',
                'country_code' => '1-264',
                'official_language' => 'English',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Antarctica',
                'official_name' => 'Antarctica',
                'continent_name' => 'Antarctica',
                'alpha_2_code' => 'AQ',
                'alpha_3_code' => 'ATA',
                'numeric_code' => '010',
                'country_code' => '672',
                'official_language' => 'No Official Language',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Antigua and Barbuda',
                'official_name' => 'Antigua and Barbuda',
                'continent_name' => 'North America',
                'alpha_2_code' => 'AG',
                'alpha_3_code' => 'ATG',
                'numeric_code' => '028',
                'country_code' => '1-268',
                'official_language' => 'English',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Argentina',
                'official_name' => 'Argentine Republic',
                'continent_name' => 'South America',
                'alpha_2_code' => 'AR',
                'alpha_3_code' => 'ARG',
                'numeric_code' => '032',
                'country_code' => '54',
                'official_language' => 'Spanish',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Armenia',
                'official_name' => 'Republic of Armenia',
                'continent_name' => 'Europe',
                'alpha_2_code' => 'AM',
                'alpha_3_code' => 'ARM',
                'numeric_code' => '051',
                'country_code' => '374',
                'official_language' => 'Armenian',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Aruba',
                'official_name' => 'Country of Aruba',
                'continent_name' => 'Europe',
                'alpha_2_code' => 'AW',
                'alpha_3_code' => 'ABW',
                'numeric_code' => '533',
                'country_code' => '297',
                'official_language' => 'Dutch',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Australia',
                'official_name' => 'Commonwealth of Australia',
                'continent_name' => 'Oceania',
                'alpha_2_code' => 'AU',
                'alpha_3_code' => 'AUS',
                'numeric_code' => '036',
                'country_code' => '61',
                'official_language' => 'English',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Austria',
                'official_name' => 'Republic of Austria',
                'continent_name' => 'Europe',
                'alpha_2_code' => 'AT',
                'alpha_3_code' => 'AUT',
                'numeric_code' => '040',
                'country_code' => '43',
                'official_language' => 'German',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Azerbaijan',
                'official_name' => 'Republic of Azerbaijan',
                'continent_name' => 'Europe',
                'alpha_2_code' => 'AZ',
                'alpha_3_code' => 'AZE',
                'numeric_code' => '031',
                'country_code' => '994',
                'official_language' => 'Azerbaijani',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // starting from ===============> B
            // [
            //     'name' => 'Bahamas',
            //     'official_name' => 'Commonwealth of the Bahamas',
            //     'continent_name' => 'North America',
            //     'alpha_2_code' => '',
            //     'alpha_3_code' => '',
            //     'numeric_code' => '',
            //     'country_code' => '',
            //     'official_language' => ''
            // ],
            // [
            //     'name' => '',
            //     'official_name' => 'Kingdom of Bahrain',
            //     'continent_name' => 'Asia',
            //     'alpha_2_code' => '',
            //     'alpha_3_code' => '',
            //     'numeric_code' => '',
            //     'country_code' => '',
            //     'official_language' => ''
            // ],
            // [
            //     'name' => '',
            //     'official_name' => 'People\'s Republic of Bangladesh',
            //     'continent_name' => 'Asia',
            //     'alpha_2_code' => '',
            //     'alpha_3_code' => '',
            //     'numeric_code' => '',
            //     'country_code' => '',
            //     'official_language' => ''
            // ],
            // [
            //     'name' => 'Barbados',
            //     'official_name' => 'Barbados',
            //     'continent_name' => 'North America',
            //     'alpha_2_code' => '',
            //     'alpha_3_code' => '',
            //     'numeric_code' => '',
            //     'country_code' => '',
            //     'official_language' => ''
            // ],
            // [
            //     'name' => '',
            //     'official_name' => 'Republic of Belarus',
            //     'continent_name' => 'Europe',
            //     'alpha_2_code' => '',
            //     'alpha_3_code' => '',
            //     'numeric_code' => '',
            //     'country_code' => '',
            //     'official_language' => ''
            // ],
            // [
            //     'name' => '',
            //     'official_name' => 'Kingdom of Belgium',
            //     'continent_name' => 'Europe',
            //     'alpha_2_code' => '',
            //     'alpha_3_code' => '',
            //     'numeric_code' => '',
            //     'country_code' => '',
            //     'official_language' => ''
            // ],
            // [
            //     'name' => 'Belize',
            //     'official_name' => 'Belize',
            //     'continent_name' => 'North America',
            //     'alpha_2_code' => '',
            //     'alpha_3_code' => '',
            //     'numeric_code' => '',
            //     'country_code' => '',
            //     'official_language' => ''
            // ],
            // [
            //     'name' => '',
            //     'official_name' => '',
            //     'continent_name' => '',
            //     'alpha_2_code' => '',
            //     'alpha_3_code' => '',
            //     'numeric_code' => '',
            //     'country_code' => '',
            //     'official_language' => ''
            // ],
        ];

        DB::table('countries')->insert($countries);
    }
}

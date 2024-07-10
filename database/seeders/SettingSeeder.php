<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Setting::truncate();
        Schema::enableForeignKeyConstraints();

        $settings = array(
            array('name' => 'cms_name', 'value' => 'CMS Reborn'),
            array('name' => 'cms_logo', 'value' => 'logo.png'),
            array('name' => 'cms_favicon', 'value' => 'favicon.png'),
            array('name' => 'date_format', 'value' => 'd m, Y'),
        );

        Setting::insert($settings);
    }
}

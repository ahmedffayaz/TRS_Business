<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;
use App\Enums\Email\EmailStatus;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmailTemplateSeeder extends Seeder
{
    protected $emailTemplateService;

    public function __construct(EmailTemplateService $emailTemplateService)
    {
        $this->emailTemplateService = $emailTemplateService;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        EmailTemplate::truncate();
        Schema::enableForeignKeyConstraints();
        $businesses = Business::get(['id']);
        foreach ($businesses as $business) {
            $this->emailTemplateService->create($business->id);
        }

    }

}

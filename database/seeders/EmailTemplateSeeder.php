<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Email\EmailStatus;
use App\Models\EmailTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Schema;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        EmailTemplate::truncate();
        Schema::enableForeignKeyConstraints();

        $emails = [
            [
                'title' => 'Payment Received',
                'detail' => 'Sent to admin after payment received email',
                'key' => 'admin_payment_received',
                'subject' => 'Payment received',
                'keywords' => '{{SITE_TITLE}}, {{SITE_URL}}, {{CLIENT_NAME}}, {{CLIENT_EMAIL}}, {{PROJECT}}, {{INVOICE_NUMBER}}, {{DATE}}, {{AMOUNT}}',
                'body' => "<table style='width: 100%' cellpadding='0' cellspacing='0' role='presentation'>
                <tr>
                  <td class='sm-px-24' style='mso-line-height-rule: exactly; border-radius: 4px; background-color: #ffffff; padding: 48px; text-align: left; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; font-size: 16px; line-height: 24px; color: #626262;'>
                    <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; font-size: 20px; font-weight: 600;'>Dear {{CLIENT_NAME}}</p>
                    <p
                      style=' font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin: 0; margin-bottom: 24px;'>
                      <b>{{CLIENT_NAME}}</b> payment processed.
                    </p>
                    <p>The Payment details are right below:</p>

                    <table>
                        <tbody>
                            <tr>
                                <th>Invoice Number:</th>
                                <td>{{INVOICE_NUMBER}}</td>
                            </tr>
                            <tr>
                                <th>Date:</th>
                                <td>{{DATE}}</td>
                            </tr>
                            <tr>
                                <th>Amount:</th>
                                <td>{{AMOUNT}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-top: 6px; margin-bottom: 20px; font-size: 16px; line-height: 24px;'>Regards,</p>
                    <p>Khuram Javaid CEO - {{PROJECT}}</p>
                    <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-top: 6px; margin-bottom: 20px; font-size: 16px; line-height: 24px;'>
                      <a href='{{SITE_URL}}'
                        style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;'>{{SITE_NAME}}</a>
                      <span>email: </span>
                      <a href='#' style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;'>hello@therightsw.com</a>
                      <span>| Skype: khuram.javaid</span>
                    </p>
                  </td>
                </tr>
              </table>",
                'status' => EmailStatus::ENABLE->value,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Invoice Payment',
                'detail' => 'Sent to client',
                'key' => 'client_invoice_payment_confirm',
                'subject' => 'Thanks for Payment',
                'keywords' => '{{SITE_TITLE}}, {{SITE_URL}}, {{CLIENT_NAME}}, {{CLIENT_EMAIL}}, {{PROJECT}}, {{INVOICE_NUMBER}}, {{DATE}}, {{AMOUNT}}',
                'body' => '',
                'status' => EmailStatus::ENABLE->value,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        EmailTemplate::insert($emails);
    }
}

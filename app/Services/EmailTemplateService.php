<?php
namespace App\Services;

use App\Models\EmailTemplate;
use App\Enums\Email\EmailStatus;


class EmailTemplateService
{
    public function create($business_id)
    {
        $emails = [
            [
                'title' => 'Payment Received',
                'detail' => 'Sent to admin after payment received email',
                'key' => 'admin_payment_received',
                'subject' => 'Payment received',
                'keywords' => '{{SITE_TITLE}}, {{SITE_URL}}, {{CLIENT_NAME}}, {{CLIENT_EMAIL}}, {{PROJECT}}, {{INVOICE_NUMBER}}, {{DATE}}, {{AMOUNT}},{{COMPANY_EMAIL}},{{COMPANY_SKYPE}},{{CEO_NAME}}',
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
                            <p>{{CEO_NAME}} CEO - {{PROJECT}}</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-top: 6px; margin-bottom: 20px; font-size: 16px; line-height: 24px;'>
                        <a href='{{SITE_URL}}'
                            style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;'>{{SITE_NAME}}</a>
                        <span>email: </span>
                        <a href='mailto:{{COMPANY_EMAIL}}' style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;'>{{COMPANY_EMAIL}}</a>
                        <span>| Skype: {{COMPANY_SKYPE}}</span>
                        </p>
                    </td>
                    </tr>
                </table>",
                'status' => EmailStatus::ENABLE->value,
                'business_id' => $business_id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Invoice Payment',
                'detail' => 'Sent to client',
                'key' => 'client_invoice_payment_confirm',
                'subject' => 'Thanks for Payment',
                'keywords' => '{{SITE_TITLE}}, {{SITE_URL}}, {{CLIENT_NAME}}, {{CLIENT_EMAIL}}, {{PROJECT}}, {{INVOICE_NUMBER}}, {{DATE}}, {{AMOUNT}},{{COMPANY_EMAIL}},{{COMPANY_SKYPE}},{{CEO_NAME}}',
                'body' => "<table style='width: 100%' cellpadding='0' cellspacing='0' role='presentation'>
                    <tr>
                    <td class='sm-px-24' style='mso-line-height-rule: exactly; border-radius: 4px; background-color: #ffffff; padding: 48px; text-align: left; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; font-size: 16px; line-height: 24px; color: #626262;'>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; font-size: 20px; font-weight: 600;'>Dear {{CLIENT_NAME}}!</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin: 0; margin-bottom: 24px;'>You just made our day!</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;'>Thanks for the payment. The Payment details are right below:</p>

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
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-top: 6px; margin-bottom: 20px; font-size: 16px; line-height: 24px;'>We love having you as our customer and look forward to working with you again.</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-top: 6px; margin-bottom: 20px; font-size: 16px; line-height: 24px;'>Regards,</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-top: 6px; margin-bottom: 20px; font-size: 16px; line-height: 24px;'>{{CEO_NAME}} CEO - {{BUSINESS}}</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-top: 6px; margin-bottom: 20px; font-size: 16px; line-height: 24px;'>
                        <a href='{{SITE_URL}}' style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;'>{{SITE_TITLE}}</a>
                        <span style='font-family: 'Montserrat', sans-serif; mso-line-height-rule:'>email: </span>
                        <a href='mailto:{{COMPANY_EMAIL}}' style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;'>{{COMPANY_EMAIL}}</a>
                        <span style='font-family: 'Montserrat', sans-serif; mso-line-height-rule:'>| Skype: {{COMPANY_SKYPE}}</span>
                        </p>
                    </td>
                    </tr>
                </table>",
                'status' => EmailStatus::ENABLE->value,
                'business_id' => $business_id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Invoice creation of project',
                'detail' => 'Sent to admin and user',
                'key' => 'project_invoice_creation',
                'subject' => 'Invoice creation of project',
                'keywords' => '{{SITE_TITLE}}, {{SITE_URL}}, {{CLIENT_NAME}}, {{CLIENT_EMAIL}}, {{PROJECT}}, {{INVOICE_NUMBER}},{{COMPANY_EMAIL}},{{COMPANY_SKYPE}},{{CEO_NAME}}',
                'body' => "<table style='width: 100%' cellpadding='0' cellspacing='0' role='presentation'>
                    <tr>
                    <td class='sm-px-24' style='mso-line-height-rule: exactly; border-radius: 4px; background-color: #ffffff; padding: 48px; text-align: left; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; font-size: 16px; line-height: 24px; color: #626262;'>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; font-size: 20px; font-weight: 600;'>Dear {{CLIENT_NAME}}</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin: 0; margin-bottom: 24px;'>Thank you for your business. Your invoice {{INVOICE_NUMBER}} is attached with this email and can be downloaded as a PDF file.</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;'>We look forward to doing more business with you.</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-top: 6px; margin-bottom: 20px; font-size: 16px; line-height: 24px;'>We love having you as our customer and look forward to working with you again.</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-top: 6px; margin-bottom: 20px; font-size: 16px; line-height: 24px;'>Regards,</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-top: 6px; margin-bottom: 20px; font-size: 16px; line-height: 24px;'>{{CEO_NAME}} CEO - {{PROJECT}}</p>
                        <p style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-top: 6px; margin-bottom: 20px; font-size: 16px; line-height: 24px;'>
                        <a href='{{SITE_URL}}' style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;'>{{SITE_TITLE}}</a>
                        <span style='font-family: 'Montserrat', sans-serif; mso-line-height-rule:'>email: </span>
                        <a href='mailto:{{COMPANY_EMAIL}}' style='font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;'>{{COMPANY_EMAIL}}</a>
                        <span style='font-family: 'Montserrat', sans-serif; mso-line-height-rule:'>| Skype: {{COMPANY_SKYPE}}</span>
                        </p>
                    </td>
                    </tr>
                </table>",
                'status' => EmailStatus::ENABLE->value,
                'business_id' => $business_id,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        EmailTemplate::insert($emails);
    }
}

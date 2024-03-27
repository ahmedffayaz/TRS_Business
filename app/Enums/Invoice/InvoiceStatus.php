<?php

namespace App\Enums\Invoice;

enum InvoiceStatus : string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case PARTIALLYPAID = 'partially_paid';
    case PAID = 'paid';
}
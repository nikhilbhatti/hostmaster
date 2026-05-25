<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useTimestamps = false;

    protected $allowedFields = [

        'payment_number',

        'customer_id',

        'invoice_id',

        'payment_date',

        'amount',

        'payment_mode',

        'reference',

        'notes',

        'attachment',

        'status'

    ];
}
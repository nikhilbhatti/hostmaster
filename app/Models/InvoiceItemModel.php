<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceItemModel extends Model
{
    protected $table = 'invoice_items';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $allowedFields = [

        'invoice_id',

        'item_id',

        'item_name',

        'description',

        /* HSN / SAC */
        'hsn_sac',

        'qty',

        'unit',

        'rate',

        'discount',

        'tax_id',

        'tax_rate',

        'tax_amount',

        'amount'

    ];

    protected $useTimestamps = false;

}
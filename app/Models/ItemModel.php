<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table = 'items';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useTimestamps = false;

    protected $allowedFields = [
        'item_type',
        'name',
        'sku',
        'unit',
        'description',
        'hsn_sac',
        'selling_price',
        'purchase_price',
        'tax_id',
        'status'
    ];
}
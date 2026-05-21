<?php namespace App\Models; use CodeIgniter\Model;
class TaxModel extends Model { protected $table='taxes'; protected $allowedFields=['name','rate','status']; protected $returnType='array'; }
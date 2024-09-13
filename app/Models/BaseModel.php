<?php
namespace App\Models;


use CodeIgniter\Model;


abstract class BaseModel extends Model {
    
    protected $returnType       = \App\Entities\NativeEntity::class;
}
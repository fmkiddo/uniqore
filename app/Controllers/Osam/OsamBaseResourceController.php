<?php
namespace App\Controllers\Osam;


use App\Controllers\BaseClientResource;

abstract class OsamBaseResourceController extends BaseClientResource {
    
    protected $userFieldSearch  = 'uuid';
    protected $userTableName    = 'ousr';
    protected $getparamname     = 'atom';
    
}
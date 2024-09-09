<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseRESTfulController extends ResourceController {
    
    protected $request;
    
    protected $helpers = [];
    
    public function initController(
            RequestInterface $request, 
            ResponseInterface $response, 
            LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
    }
}
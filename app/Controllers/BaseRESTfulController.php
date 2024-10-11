<?php
namespace App\Controllers;

use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Psr\Log\LoggerInterface;

abstract class BaseRESTfulController extends ResourceController {
    
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;
    
    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];
    
    protected $encryptor;
    
    protected $apiName;
    
    protected function __initComponents () { }
    
    protected function addHelper (string $helperName): self {
        array_push ($this->helpers, $helperName);
        return $this;
    }
    
    protected function getRequestAuthorization (): string {
        return $this->request->header ('Authorization')->getValue ();
    }
    
    abstract protected function validateRequestAuthorization (): bool;
    
    /**
     * 
     * @param string $encrypted
     * @return string|bool
     */
    abstract protected function decrypt ($encrypted): string|bool;
    
    /**
     * 
     * @param string $plainText
     * @return string|bool
     */
    abstract protected function encrypt ($plainText): string|bool;
    
    protected function getRequestUserID (): string {
        return "";
    }
    
    protected function getDatabaseConnection (): string|array {
        if ($this->apiName === UNIQORE_NAME) return 'default';
        return '';
    }
    
    /**
     * 
     * @param mixed $level
     * @param string $messages
     * @param number $access_id
     */
    protected function doLog ($level, $messages='', $access_id=0) { }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\BaseResource::initController()
     */
    public function initController(
            RequestInterface $request,
            ResponseInterface $response,
            LoggerInterface $logger) {
        parent::initController ($request, $response, $logger);
        $this->__initComponents ();
        array_push ($this->helpers, 'json');
        helper ($this->helpers);
    }
}
<?php
namespace App\Controllers;

use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Psr\Log\LoggerInterface;
use function Ramsey\Uuid\v1;
use CodeIgniter\Encryption\Exceptions\EncryptionException;

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
    
    /**
     * 
     * @return string|bool
     */
    protected function getRequestAuthorization (): string|bool {
        if (!$this->request->hasHeader ('Authorization')) return FALSE;
        return str_replace ('Basic ', '', $this->request->header ('Authorization')->getValue ());
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
    
    abstract protected function getRequestUserID (): int;
    
    protected function getDatabaseConnection (): string|array {
        if ($this->apiName === UNIQORE_NAME) return 'default';
        $authData       = $this->getRequestAuthorization ();
        if (!$authData) throw new \ErrorException ('Error: Authorization headers not found!');
        $authData       = base64_decode ($authData);
        $authData       = str_replace (':', '', $authData);
        $explodeData    = explode ('#', $authData);
        $clientCode     = base64_decode ($explodeData[1]);
        $db             = \Config\Database::connect ();
        $query          = "SELECT * FROM fmk_ocac JOIN fmk_cac2 ON fmk_ocac.id=fmk_cac2.client_id WHERE client_code='{$clientCode}'";
        $clients        = $db->query ($query)->getResult ();
        if (!count ($clients)) throw new \ErrorException ('Error: client data not found!');
        $decipher       = $this->decrypt (hex2bin ($clients[0]->db_password));
        return [
            'DSN'           => '',
            'hostname'      => 'localhost',
            'username'      => $clients[0]->db_user,
            'password'      => $decipher,
            'database'      => $clients[0]->db_name,
            'DBDriver'      => 'MySQLi',
            'DBPrefix'      => "{$clients[0]->db_prefix}_",
            'pConnect'      => FALSE,
            'DBDebug'       => FALSE,
            'charset'       => 'utf8mb4',
            'DBCollat'      => 'utf8mb4_unicode_520_ci',
            'swapPre'       => '',
            'encrypt'       => FALSE,
            'compress'      => FALSE,
            'strictOn'      => FALSE,
            'failover'      => [],
            'port'          => 3306
        ];
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
        $this->addHelper ('json');
        helper ($this->helpers);
    }
}
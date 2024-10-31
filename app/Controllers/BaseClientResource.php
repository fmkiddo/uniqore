<?php
namespace App\Controllers;

use CodeIgniter\Encryption\Exceptions\EncryptionException;
use CodeIgniter\Database\ConnectionInterface;

abstract class BaseClientResource extends BaseRESTfulController {
    
    protected $userFieldSearch;
    protected $userTableName;
    protected $getparamname;
    protected $format           = 'json';
    
    private $validRequest       = FALSE;
    private $clientCode         = NULL;
    /**
     * 
     * @var ConnectionInterface
     */
    private $db;
    private $dbConfig;
    
    /**
     *
     * @param string $authData
     * @return bool
     */
    private function validateClient (string $authData): bool {
        $validClient    = FALSE;
        if (strpos ($authData, '#')) {
            $explodeData    = explode ('#', $authData);
            $db             = \Config\Database::connect ();
            $clientCode     = base64_decode ($explodeData[1]);
            $clientData     = base64_decode ($explodeData[0]);
            $query          = "SELECT * FROM fmk_ocac WHERE client_code='{$clientCode}'";
            $clients        = $db->query ($query)->getResult ();
            if (count ($clients)) {
                $clientKey          = $clients[0]->client_keycode;
                $encryption         = new \Config\Encryption ();
                $encryption->driver = UNIQORE_DEFAULT_ENCDRIVER;
                $encryption->cipher = UNIQORE_DEFAULT_CIPHER;
                $encryption->key    = hex2bin ($clientKey);
                $encrypter          = \Config\Services::encrypter ($encryption);
                $encryptFailed      = FALSE;
                $decipher           = '';
                try {
                    $decipher           = $encrypter->decrypt (hex2bin ($clientData));
                } catch (EncryptionException $exception) {
                    $encryptFailed      = TRUE;
                }
                if ($encryptFailed) ;
                else {
                    $decipher       = unserialize ($decipher)['data0'];
                    $validClient    = password_verify ($decipher, $clients[0]->client_passcode);
                }
            }
            $db->close ();
            $this->clientCode   = $clientCode;
        }
        return $validClient;
    }
    
    protected function getClientCode (): string {
        return $this->clientCode;
    }
    
    protected final function generateInvalidRequest () {
        // do log here
        return $this->failUnauthorized ('Unauthorized Access Request', 401);
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::validateRequestAuthorization()
     */
    protected function validateRequestAuthorization (): bool {
        $authData   = $this->getRequestAuthorization ();
        if (!$authData || strlen ($authData) == 0) return FALSE;
        $authData   = base64_decode ($authData);
        $authData   = str_replace (':', '', $authData);
        return $this->validateClient ($authData);
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::encrypt()
     */
    protected function encrypt ($plainText, $options=NULL): string|bool {
        if ($options === NULL) $options = config ('Encryption');
        $this->encryptor    = \Config\Services::encrypter ($options);
        try {
            return $this->encryptor->encrypt ($plainText);
        } catch (EncryptionException $exception) {
            return FALSE;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::decrypt()
     */
    protected function decrypt ($encrypted, $options=NULL): string|bool {
        if ($options === NULL) $options = config ('Encryption');
        $this->encryptor    = \Config\Services::encrypter ($options);
        try {
            return $this->encryptor->decrypt ($encrypted);
        } catch (EncryptionException $exception) {
            return FALSE;
        }
    }
    
    /**
     * 
     * @return bool
     */
    protected function isValidRequest (): bool {
        return $this->validRequest;
    }
    
    abstract protected function findWithFilter ($get);
    
    abstract protected function responseFormatter ($queryResult): array;
    
    protected function doFindAll () {
        return $this->model->findAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::doLog()
     */
    protected function doLog ($level, $messages = '', $access_id = 0) {
        $host       = $this->request->getUri ()->getHost ();
        $method     = $this->request->getMethod ();
        $type       = $this->request->header ('Content-Type')->getValue ();
        $api_name   = $this->apiName;
        $ip_address = $this->request->getIPAddress ();
        $user_agent = $this->request->getUserAgent ();
        $dbConfig   = $this->getDatabaseConnection ();
        
        if (!strlen (trim ($messages)))
            $messages   = "API Access to {$api_name} successfully, host: {$host}, method: {$method}, type: {$type}, agent: {$user_agent}, ip: {$ip_address}, id: {$access_id}";
        $logid      = generate_random_uuid_v4();
        $query      = "INSERT INTO {$dbConfig['DBPrefix']}oalg (uuid, level, message, host, method, ctype, app_userid, agent, ip)
            VALUES ('{$logid}', '{$level}', '{$messages}', '{$host}', '{$method}', '{$type}', '{$access_id}', '{$user_agent}', '{$ip_address}');";
        
        $db         = \Config\Database::connect ($dbConfig);
        $db->simpleQuery ($query);
        $db->close ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::getRequestUserID()
     */
    protected function getRequestUserID (): int {
        if ($this->getparamname === NULL) return 0;
        
        $get    = $this->request->getGet ();
        if (!array_key_exists ($this->getparamname, $get)) return 0;
        
        $value  = $get[$this->getparamname];
        if (!is_base64($value)) return 0;
        $value  = base64_decode ($value);
        $sql    = "SELECT * FROM {$this->__getUserTableName ()} WHERE `{$this->userFieldSearch}`='{$value}'";
        $user   = $this->db->query ($sql)->getResult ();
        if (!count ($user)) return 0;
        else return $user[0]->id;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::__initComponents()
     */
    protected function __initComponents() {
        if ($this->modelName === NULL) throw new \ErrorException ('Null Pointer Exception on Controller Model Name');
        $this->addHelper('uuid');
        if ($this->validateRequestAuthorization ()) {
            $this->dbConfig     = $this->getDatabaseConnection ();
            $this->db           = \Config\Database::connect ($this->dbConfig);
            $this->model        = model ($this->modelName, FALSE, $this->db);
            $this->validRequest = TRUE;
        }
    }
    
    protected function __getUserTableName () {
        return "{$this->dbConfig['DBPrefix']}{$this->userTableName}";
    }
    
    protected function doDelete (array $json, $userid = 0) {
        return $this->fail (lang ('RESTful.notImplemented', ['delete']), 501);
    }
    
    protected function doCreate (array $json, $userid = 0) {
        return $this->fail (lang ('RESTful.notImplemented', ['create']), 501);
    }
    
    protected function doUpdate ($id, array $json, $userid=0) {
        return $this->fail (lang ('RESTful.notImplemented', ['update']), 501);
    }
    
    protected function doEdit ($id, $userid = 0) {
        return $this->fail (lang ('RESTful.notImplemented', ['update']), 501);
    }
    
    protected function doNew ($userid = 0) {
        return $this->fail (lang ('RESTful.notImplemented', ['new']), 501);
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::index()
     */
    public function index () {
        if ($this->modelName === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if (!$this->isValidRequest ()) return $this->generateInvalidRequest ();
        
        $userid = $this->getRequestUserID ();
        $get    = $this->request->getGet ();
        $res    = NULL;
        if (!count ($get) || !array_key_exists ('payload', $get)) $res  = $this->doFindAll ();
        else $res   = $this->findWithFilter ($get);
        $rows   = count ($res);
        $time   = time ();
        $this->doLog ('warning', "Query to $this->modelName::index was called on $time and returned $rows result(s)", $userid);
        
        if ($res === NULL) return $this->failServerError ("Null Pointer Exception", 500);
        
        $retVal     = [
            'status'    => 200,
            'error'     => NULL
        ];
        if (!$rows) $retVal['messages']['success'] = 'Server returned empty row or data not found!';
        else {
            $payloads   = $this->responseFormatter ($res);
            $retVal['messages']['success'] = 'OK!';
            $retVal['data'] = [
                'uuid'      => $time,
                'timestamp' => date ('Y-m-d H:i:s'),
                'payload'   => base64_encode (serialize ($payloads))
            ];
        }
        return $this->respond ($retVal);
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::show()
     */
    public function show ($id = null) {
        if ($this->modelName === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if (!$this->isValidRequest ()) return $this->generateInvalidRequest ();
        
        $userid     = $this->getRequestUserID ();
        $isBase64   = is_base64 ($id);
        if (!$isBase64) {
            $json   = [
                'status'    => 442,
                'error'     => 442,
                'messages'  => [
                    'error'     => 'Unknown input parameter format!'
                ]
            ];
            $this->doLog ('alert', "Query to $this->modelName::show was call returned with {$json['messages']['error']}", $userid);
        } else {
            $uuid   = base64_decode ($id);
            $get    = [
                'payload'   => "find#$uuid"
            ];
            $res    = $this->findWithFilter ($get);
            $time   = time ();
            $rows   = count ($res);
            $this->doLog ('warning', "Query to $this->modelName::show was called on $time and returned $rows result(s)", $userid);
            
            if ($res === NULL) return $this->failServerError ("Null Pointer Exception", 500);
            
            $retVal = [
                'status'    => 200,
                'error'     => NULL,
            ];
            if (!$rows) $retVal['messages']['success']  = 'Server returned empty row or data not found!';
            else {
                $payloads   = $this->responseFormatter ($res);
                $retVal['messages']['success']  = 'OK!';
                $retVal['data'] = [
                    'uuid'      => $time,
                    'timestamp' => date ('Y-m-d H:i:s'),
                    'payload'   => base64_encode (serialize ($payloads))
                ];
            }
        }
        return $this->respond ($retVal);
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::delete()
     */
    public function delete ($id = null) {
        if ($this->modelName === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if (!$this->isValidRequest ()) return $this->generateInvalidRequest ();
        $userid     = $this->getRequestUserID ();
        if (!is_base64 ($id)) {
            
        } else {
            $theId  = base64_decode ($id);
            $json   = $this->doDelete ($theId, $userid);
        }
        return $this->respond ($json);
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::update()
     */
    public function update ($id = null) {
        if ($this->modelName === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if (!$this->isValidRequest ()) return $this->generateInvalidRequest ();
        
        $userid     = $this->getRequestUserID ();
        if (!is_base64 ($id)) {
            $json   = [
                'status'    => 442,
                'error'     => 442,
                'messages'  => [
                    'error'     => 'Unknown input parameter format!'
                ]
            ];
            $this->doLog ('alert', "Query to $this->modelName::show was call returned with {$json['messages']['error']}", $userid);
        } else {
            $theId      = base64_decode ($id);
            $json       = json_decode ($this->request->getBody (), TRUE);
            $retVal     = $this->doUpdate ($theId, $json, $userid);
            if (!is_array ($retVal)) $json = $retVal;
            else {
                $time   = time ();
                if ($retVal['status'] === 200) {
                    $level      = 'warning';
                    $messages   = "Query to $this->modelName::update was called on $time reported query was executed successfully";
                } else {
                    $level      = 'error';
                    $messages   = "Query to $this->modelName::update was called on $time reported query execution was failed";
                }
                $this->doLog ($level, $messages, $userid);
                return $this->respondUpdated ($retVal);
            }
        }
        return $this->respond ($json);
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::edit()
     */
    public function edit ($id = null) {
        if ($this->modelName === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if (!$this->isValidRequest ()) return $this->generateInvalidRequest ();
        
        $userid = $this->getRequestUserID ();
        if (!is_base64 ($id)) {
            $json   = [
                'status'    => 442,
                'error'     => 442,
                'messages'  => [
                    'error'     => 'Unknown input parameter format!'
                ]
            ];
            $this->doLog ('alert', "Query to $this->modelName::show was call returned with {$json['messages']['error']}", $userid);
        } else {
            $theId  = base64_decode ($id);
            $json   = $this->doEdit ($theId, $userid);
            return $this->respondUpdated ($json);
        }
        return $this->respond ($json);
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::new()
     */
    public function new () {
        if ($this->modelName === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if (!$this->isValidRequest ()) return $this->generateInvalidRequest ();

        $userid = $this->getRequestUserID ();
        $json   = $this->doNew ($userid);
        return $this->respond ($json);
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::create()
     */
    public function create () {
        if ($this->modelName === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if (!$this->isValidRequest ()) return $this->generateInvalidRequest ();
        
        $userid = $this->getRequestUserID ();
        $json   = json_decode ($this->request->getBody (), TRUE);
        $retVal = $this->doCreate ($json, $userid);
        if (!is_array ($retVal)) return $retVal;
        else {
            $time   = time ();
            if ($retVal['status'] === 200) {
                $level      = 'warning';
                $messages   = "Query to $this->modelName::create was called on $time and was successfully executed";
            } else {
                $level      = 'error';
                $messages   = "Query to $this->modelName::create was called on $time and return reported as failed";
            }
            $this->doLog ($level, $messages, $userid);
            return $this->respondCreated ($retVal);
        } 
    }
    
}
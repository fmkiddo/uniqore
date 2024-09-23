<?php
namespace App\Controllers;


use CodeIgniter\Files\File;
use CodeIgniter\Encryption\Exceptions\EncryptionException;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseUniqoreAPIController extends BaseRESTfulController {
    
    protected $modelName    = NULL;
    
    protected $apiName      = 'Uniqore';
    
    protected $format       = 'json';
    
    private function getPollute () {
        $get = $this->request->getGet ();
        if (! count ($get) || ! array_key_exists ('pollute', $get)) return FALSE;
        return base64_decode ($get['pollute'], TRUE);
    }
    
    private function getUserID () {
        $uuid   = $this->getPollute ();
        if (!$uuid) return 0;
        else {
            $db     = \Config\Database::connect ();
            $sql    = "SELECT id FROM fmk_ousr WHERE uid='$uuid'";
            $res    = $db->query ($sql)->getResult ();
            if (!count ($res)) return 0;
            else return $res[0]->id;
        }
    }
    
    /**
     * 
     * @return array
     */
    private function readAuthFile (): array {
        $randAuthFile   = new File (SYS__UNIQORE_RANDAUTH_PATH);
        $contents       = file_get_contents ($randAuthFile->getPathname());
        return explode('.', $contents);
    }
    
    /**
     * 
     * @return array
     */
    private function readAuthHeader (): array|bool {
        $headers    = $this->request->headers ();
        if (!array_key_exists('Authorization', $headers)) return FALSE;
        $auth = $headers['Authorization']->getValue ();
        $auth = str_replace ('Basic ', '', $auth);
        $auth = base64_decode ($auth);
        $auth = str_replace (':', '', $auth);
        if (!(ctype_xdigit ($auth) && strlen ($auth) % 2 == 0)) return FALSE;
        $auth = hex2bin ($auth);
        if (!$auth) return FALSE;
        $decrypted = $this->decrypt ($auth);
        if (!$decrypted) return FALSE;
        return explode ('.', $decrypted);
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::__initComponents()
     */
    protected function __initComponents() {
        $this->addHelper ('uuid');
        parent::__initComponents ();
    }
    
    protected function generateUnauthorizedCommand ($code='401') {
        $ip_address = $this->request->getIPAddress ();
        $info   = [
            'timestamps'    => time (),
            'ip_address'    => $ip_address
        ];
        log_message('alert', "[ALERT] Unauthorized request made from IP {$ip_address}", $info);
        return $this->failUnauthorized ('Authorization Failed. This incident has been logged.');
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::decrypt()
     */
    protected function decrypt($encrypted): string|bool {
        $encConfig          = config ('Encryption');
        $this->encryptor    = \Config\Services::encrypter ($encConfig);
        try {
            return $this->encryptor->decrypt ($encrypted);
        } catch (EncryptionException $exc) {
            return FALSE;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::encrypt()
     */
    protected function encrypt($plainText): string|bool {
        $encConfig          = config ('Encryption');
        $this->encryptor    = \Config\Services::encrypter ($encConfig);
        try {
            return $this->encryptor->encrypt ($plainText);
        } catch (EncryptionException $exc) {
            return FALSE;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::validateRequestAuthorization()
     */
    protected function validateRequestAuthorization (): bool {
        $auth       = $this->readAuthHeader ();
        if (!$auth) return FALSE;
        $authFile   = $this->readAuthFile ();
        if ($auth[0] === $authFile[0] && $auth[1] === $authFile[1]) return TRUE;
        return FALSE;
    }
    
    protected function getInputParams (): array|ResponseInterface {
        $header = $this->request->header('Content-Type')->getValue ();
        if ($header !== HEADER_APP_JSON) return $this->fail ('Unsupported Media Type', 415);
        else return json_decode ($this->request->getBody (), TRUE);
    }
    
    /**
     * 
     * @param array $json
     * @param number $userid
     * @return array|ResponseInterface
     */
    protected function doCreate (array $json, $userid=0): array|ResponseInterface {
        return $this->fail (lang ('RESTful.notImplemented', ['create']), 501);
    }
    
    protected function doNew () {
        return $this->fail (lang ('RESTful.notImplemented', ['new']), 501);
    }
    
    protected function doEdit ($id) {
        return $this->fail (lang ('RESTful.notImplemented', ['edit']), 501);
    }
    
    /**
     * 
     * @param string|array $id
     * @param array $json
     * @param number $userid
     * @return array|ResponseInterface
     */
    protected function doUpdate ($id, array $json, $userid=0): array|ResponseInterface {
        return $this->fail (lang ('RESTful.notImplemented', ['update']), 501);
    }
    
    protected function doDelete ($id): array|ResponseInterface {
        return $this->fail (lang ('RESTful.notImplemented', ['delete']), 501);
    }
    
    abstract protected function findWithFilter ($get);
    
    abstract protected function responseFormatter ($queryResult): array;
    
    protected function doLog ($level, $messages='', $access_id=0) {
        $host       = $this->request->getUri ()->getHost ();
        $method     = $this->request->getMethod ();
        $type       = $this->request->header ('Content-Type')->getValue ();
        $api_name   = $this->apiName;
        $ip_address = $this->request->getIPAddress ();
        $user_agent = $this->request->getUserAgent();
        if (!strlen (trim ($messages))) 
            $messages   = "API Access to {$api_name} successfully, host: {$host}, method: {$method}, type: {$type}, agent: {$user_agent}, ip: {$ip_address}, id: {$access_id}";
        $query      = "INSERT INTO fmk_oalg (level, message, host, method, ctype, app_userid, agent, ip) 
        VALUES ('{$level}', '{$messages}', '{$host}', '{$method}', '{$type}', '{$access_id}', '{$user_agent}', '{$ip_address}');";
        $db         = \Config\Database::connect ($this->getDatabaseConnection ());
        $db->simpleQuery ($query);
        $db->close ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::create()
     */
    public function create () {
        if ($this->modelName === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) {
            $userid = $this->getUserID ();
            $json   = json_decode ($this->request->getBody(), TRUE);
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
                return $this->respond ($retVal);
            } 
        }
        return $this->generateUnauthorizedCommand ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::index()
     */
    public function index () {
        if ($this->modelName === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) {
            $time   = time ();
            $get    = $this->request->getGet ();
            $userid = $this->getUserID ();
            $res    = NULL;
            if (! count ($get) || ! array_key_exists ('payload', $get)) $res    = $this->model->findAll ();
            else $res = $this->findWithFilter ($get, $userid);
            $rows   = count ($res);
            $time   = time ();
            $this->doLog ('warning', "Query to $this->modelName::index was called on $time and returned $rows result(s)", $userid);
            
            if ($res === NULL) return $this->failServerError ("Null Pointer Exception", 500);
            
            return $this->respond ($this->responseFormatter ($res));
        }
        return $this->generateUnauthorizedCommand ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::new()
     */
    public function new () {
        if ($this->modelName === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) return $this->doNew ();
        return $this->generateUnauthorizedCommand ();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::edit()
     */
    public function edit ($id = null) {
        if ($this->modelName === NULL || $id === NULL) 
            return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) return $this->doEdit ($id);
        return $this->generateUnauthorizedCommand ();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::update()
     */
    public function update ($id = null) {
        if ($this->modelName === NULL || $id === NULL) 
            return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) {
            $userid     = $this->getUserID ();
            $isBase64   = is_base64 ($id);
            if ($isBase64) {
                $theId      = base64_decode ($id);
                $json       = json_decode ($this->request->getBody (), TRUE);
                $retVal     = $this->doUpdate ($theId, $json, $userid);
                if (!is_array ($retVal)) return $retVal;
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
                    return $this->respond ($retVal);
                }
            }
        }
        return $this->generateUnauthorizedCommand ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::delete()
     */
    public function delete ($id = null) {
        if ($this->modelName === NULL || $id === NULL) 
            return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) return $this->doDelete ($id);
        return $this->generateUnauthorizedCommand ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::show()
     */
    public function show ($id = null) {
        if ($this->modelName === NULL || $id === NULL) 
            return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) {
            $userid     = $this->getUserID ();
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
                
                $json = $this->responseFormatter ($res);
            }
            return $this->respond ($json);
        }
        return $this->generateUnauthorizedCommand ();
    }
}
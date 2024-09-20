<?php
namespace App\Controllers;


use CodeIgniter\Files\File;
use CodeIgniter\Encryption\Exceptions\EncryptionException;
use Config\Encryption;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseUniqoreAPIController extends BaseRESTfulController {
    
    
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
    
    protected function doCreate () {
        return $this->fail(lang('RESTful.notImplemented', ['create']), 501);
    }
    
    protected function doIndex () {
        return $this->fail(lang('RESTful.notImplemented', ['index']), 501);
    }
    
    protected function doNew () {
        return $this->fail(lang('RESTful.notImplemented', ['new']), 501);
    }
    
    protected function doEdit ($id) {
        return $this->fail(lang('RESTful.notImplemented', ['edit']), 501);
    }
    
    protected function doUpdate ($id) {
        return $this->fail(lang('RESTful.notImplemented', ['update']), 501);
    }
    
    protected function doDelete ($id) {
        return $this->fail(lang('RESTful.notImplemented', ['delete']), 501);
    }
    
    protected function doShow ($id) {
        return $this->fail(lang('RESTful.notImplemented', ['show']), 501);
    }
    
    protected function doLog ($level, $access_id=0) {
        $info = [
            'host'           => $this->request->getUri ()->getHost (),
            'method'        => $this->request->getMethod (),
            'type'          => $this->request->header ('Content-Type')->getValue (),
            'api_name'      => $this->apiName,
            'id'            => $access_id,
            'ip_address'    => $this->request->getIPAddress (),
            'user_agent'    => $this->request->getUserAgent(),
        ];
        log_message ($level, "API Access to {api_name} successfully, host: {host}, method: {method}, type: {type}, agent: {user_agent}, ip: {ip_address}, id: {id}, ", $info);
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::create()
     */
    public function create () {
        if ($this->model === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) return $this->doCreate ();
        return $this->generateUnauthorizedCommand ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::index()
     */
    public function index () {
        if ($this->model === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) return $this->doIndex ();
        return $this->generateUnauthorizedCommand ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::new()
     */
    public function new () {
        if ($this->model === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) return $this->doNew ();
        return $this->generateUnauthorizedCommand ();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::edit()
     */
    public function edit ($id = null) {
        if ($this->model === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) return $this->doEdit ($id);
        return $this->generateUnauthorizedCommand ();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::update()
     */
    public function update($id = null) {
        if ($this->model === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) return $this->doUpdate ($id);
        return $this->generateUnauthorizedCommand ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::delete()
     */
    public function delete($id = null) {
        if ($this->model === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) return $this->doDelete ($id);
        return $this->generateUnauthorizedCommand ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::show()
     */
    public function show($id = null) {
        if ($this->model === NULL) return $this->failServerError ('Code Error: Unproper API Implementations => Null Object Reference');
        if ($this->validateRequestAuthorization ()) return $this->doShow ($id);
        return $this->generateUnauthorizedCommand ();
    }
}
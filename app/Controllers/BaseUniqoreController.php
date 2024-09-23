<?php
namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\Files\File;
use CodeIgniter\Encryption\Exceptions\EncryptionException;


abstract class BaseUniqoreController extends BaseController {
    
    private $authToken;
    
    private function loadToken (): bool {
        $ci_file = new \CodeIgniter\Files\File (SYS__UNIQORE_RANDAUTH_PATH);
        if (!file_exists ($ci_file->getPathname ())) return FALSE;
        else {
            $file = fopen ($ci_file->getPathname(), 'r');
            $this->authToken = fread ($file, filesize ($ci_file->getPathname ()));
            return fclose ($file);
        }
    }
    
    protected function getLoggedUUID (): string {
        $payload    = $this->session->get ('payload');
        return $this->decrypt (hex2bin ($payload[0]));
    }
    
    protected function getUserName (): string {
        $payload    = $this->session->get('payload');
        return $this->decrypt (hex2bin ($payload[1]));
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::decrypt ()
     */
    protected function decrypt ($encrypted): string|bool {
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
     * @see \App\Controllers\BaseController::encrypt ()
     */
    protected function encrypt ($plainText): string|bool {
        $encConfig          = config ('Encryption');
        $this->encryptor    = \Config\Services::encrypter ($encConfig);
        try {
            return $this->encryptor->encrypt ($plainText);
        } catch (EncryptionException $exc) {
            return FALSE;
        }
    }
    
    protected function getAuthToken (): string {
        return $this->authToken;
    }
    
    
    protected function isAuthFileExists (): bool {
        $authFile = new File (SYS__UNIQORE_RANDAUTH_PATH);
        return file_exists ($authFile->getPath ());
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::initController()
     */
    public function initController(
            RequestInterface $request, 
            ResponseInterface $response, 
            LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->loadToken ();
    }
}
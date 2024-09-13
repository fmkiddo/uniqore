<?php
namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\Files\File;


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
    
    protected function getAuthToken (): string {
        return $this->authToken;
    }
    
    
    protected function isAuthFileExists (): bool {
        $authFile = new File (SYS__UNIQORE_RANDAUTH_PATH);
        return file_exists ($authFile->getPath ());
    }
}
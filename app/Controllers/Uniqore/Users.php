<?php
namespace App\Controllers\Uniqore;


use App\Controllers\BaseUniqoreAPIController;
use CodeIgniter\HTTP\Message;


class Users extends BaseUniqoreAPIController {
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::__initComponents()
     */
    protected function __initComponents() {
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::index()
     */
    public function index() {
        $auth = $this->request->header('Authorization')->getValue();
        $auth = str_replace ('Basic ', '', $auth);
        $auth = base64_decode ($auth);
        $auth = str_replace(':', '', $auth);
        $auth = hex2bin($auth);
        $auth = $this->encryptor->decrypt($auth);
        $json = [
            0   => $auth
        ];
        $this->response->setJSON($json);
        $this->response->send();
    }
    
}
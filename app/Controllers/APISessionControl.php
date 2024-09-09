<?php
namespace App\Controllers;


class APISessionControl extends BaseController {
    
    public function index ($control='') {
        if ($this->request->getMethod() !== 'POST') $this->generateJSON404 ();
        else {
            switch ($control) {
                default:
                    $json = [
                        'status'    => 404,
                        'message'   => 'Page not found!',
                        'go-home'   => base_url ('admin')
                    ];
                    break;
                case 'login':
                    $json = [];
                    break;
                case 'logout':
                    $json = [];
                    break;
            }
        
            $this->response->setHeader ('Content-Type', HEADER_APP_JSON);
            $this->response->setJSON ($json);
            $this->response->send ();
        }
    }
}
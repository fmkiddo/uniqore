<?php
namespace App\Controllers;


class APIDashboard extends BaseUniqoreController {
    
    public function index (): string {
        if ($this->session->get ('logintime') === NULL) $this->response->redirect (base_url ('uniqore/admin'));
        $get = $this->request->getGet ();
        if (!array_key_exists('route', $get)) ;
        else {
            $route = $get['route'];
        }
        return '';
    }
}
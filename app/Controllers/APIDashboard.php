<?php
namespace App\Controllers;

use App\Libraries\AssetType;
use App\Libraries\PageViews;

class APIDashboard extends BaseUniqoreController {
    
    private $pageViews;
    
    private function getUserName (): string {
        $payload = $this->session->get('payload');
        return $this->decrypt(hex2bin($payload[1]));
    }
    
    private function getUserInfo () {
        $payload = $this->session->get("payload");
    }
    
    private function doSignOut(): string {
        $this->session->destroy();
        $this->response->redirect (base_url ("uniqore/admin"));
        return "OK!";
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::__initComponents()
     */
    protected function __initComponents() {
        $this->helpers      = ["url", "key_generator"];
        $this->pageViews    = new PageViews ();
        $styles = [
            'assets/vendors/bootstrap-5.3.3/css/bootstrap.min.css',
            'assets/vendors/datatables-2.1.6/css/datatables.min.css',
            'assets/vendors/fontawesome-6.6.0/css/all.min.css',
            'assets/vendors/materialdesignicons-7.4.47/css/materialdesignicons.min.css',
            'assets/css/uniqore.css',
        ];
        $this->initAssets(AssetType::STYLE, $styles);
        $scripts = [
            'assets/vendors/jquery-3.7.1/jquery-3.7.1.min.js',
            'assets/vendors/bootstrap-5.3.3/js/bootstrap.min.js',
            'assets/vendors/datatables-2.1.6/js/datatables.min.js',
            'assets/vendors/fontawesome-6.6.0/js/all.min.js',
            'assets/js/uniqore.js',
        ];
        $this->initAssets(AssetType::SCRIPT, $scripts);
        parent::__initComponents();
    }
    
    public function index(): string {
        if (!$this->session->get("logintime")) $this->response->redirect(base_url("uniqore/admin"));
        
        $get = $this->request->getGet();
        
        if (count($get) > 0 && array_key_exists("route", $get)) $route = $get["route"];
        else $route = "welcome";
        
        if ($this->request->is ('post')) {
            $auth       = $this->encrypt ($this->getAuthToken ());
            $curlOpts   = [
                'auth'      => [
                    bin2hex ($auth),
                    '',
                    'basic'
                ],
                'headers'   => [
                    'Content-Type'  => HEADER_APP_JSON,
                    'Accept'        => HEADER_APP_JSON,
                    'User-Agent'    => $this->request->getUserAgent (),
                    'Address'       => $this->request->getIPAddress ()
                ],
            ];
            var_dump ($curlOpts);
        }
        
        $render     = TRUE;
        
        $retVal     = "";
        $viewPaths  = [];
        $dtsFetch   = '';
        
        if ($route !== 'sign-out') $this->pageViews->fetchPage($route, $dtsFetch, $viewPaths);
        else {
            $viewPaths  = [];
            $render     = FALSE;
            $retVal     = $this->doSignOut();
        } 
        
        if ($render) {
            $pageData = [
                'dashboard_url' => base_url('uniqore/admin/dashboard'),
                'username'      => $this->getUserName (),
                'realname'      => '',
                'dts_fetch'     => $dtsFetch,
            ];
            $retVal = $this->renderView ($viewPaths, $pageData);
        }
        return $retVal;
    }
}

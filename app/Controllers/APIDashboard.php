<?php
namespace App\Controllers;

use App\Libraries\AssetType;
use App\Libraries\PageViews;

class APIDashboard extends BaseUniqoreController {
    
    private $pageViews;
    
    private $routes = [
        'apiadmin'  => 'users',
    ];
    
    private function doSignOut(): string {
        $this->session->destroy();
        $this->response->redirect (base_url ("uniqore/admin"));
        return "OK!";
    }
    
    /**
     * 
     * @param array $post
     */
    private function formParamFormatter (string $route, array $post): array {
        switch ($route) {
            default:
                break;
            case 'users': 
                return [
                    'username'      => $post['input-newuser'],
                    'email'         => $post['input-newmail'],
                    'phone'         => str_replace ('-', '', $post['input-newphone']),
                    'password'      => $post['input-newpswd'],
                ];
        }
    }
    
    private function apiProcessor ($get): array|bool {
        if ($this->request->is ('post')) {
            $post       = $this->request->getPost ();
            $pollute    = base64_encode($this->getLoggedUUID ());
            $uuid       = $post['input-uuid'];
            $routes     = $this->routes[$get['route']];
            
            $auth       = $this->encrypt ($this->getAuthToken ());
            $curlOpts   = [
                'auth'          => [
                    bin2hex ($auth),
                    '',
                    'basic'
                ],
                'headers'       => [
                    'Content-Type'  => HEADER_APP_JSON,
                    'Accept'        => HEADER_APP_JSON,
                    'User-Agent'    => $this->request->getUserAgent ()
                ],
                'json'          => $this->formParamFormatter ($routes, $post)
            ];
            
            if ($uuid === 'none') {
                $url        = site_url ("api-uniqore/$routes?pollute=$pollute");
                $method     = 'post';
            } else {
                $url        = site_url ("api-uniqore/$routes/$uuid?pollute=$pollute");
                $method     = 'put';
            }
            
            $response   = $this->sendRequest ($url, $curlOpts, $method);
            $json       = json_decode ($response->getBody (), TRUE);
            return $json; 
        }
        return FALSE;
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
        if ($this->session->get ("logintime") === NULL) {
            $this->response->redirect (base_url ("uniqore/admin"));
            return "OK!";
        }
        
        $get = $this->request->getGet ();
        
        $res    = $this->apiProcessor ($get);
        
        if (count($get) > 0 && array_key_exists("route", $get)) $route = $get["route"];
        else $route = "welcome";
        
        $render     = TRUE;
        
        $retVal     = "";
        $viewPaths  = [];
        $dtsFetch   = '';
        
        if ($route !== 'sign-out') $this->pageViews->fetchPage ($route, $dtsFetch, $viewPaths);
        else {
            $viewPaths  = [];
            $render     = FALSE;
            $retVal     = $this->doSignOut();
        } 
        
        if ($render) {
            $pageData = [
                'dashboard_url' => site_url ('uniqore/admin/dashboard'),
                'validate_url'  => site_url ('uniqore/admin/dashboard/validate'),
                'username'      => $this->getUserName (),
                'realname'      => '',
                'dts_fetch'     => $dtsFetch,
            ];
            
            if (!$res) {
                
            }
            
            $retVal = $this->renderView ($viewPaths, $pageData);
        }
        return $retVal;
    }
    
    public function formValidator () {
        if ($this->request->is ('post')) {
            $post   = $this->request->getPost ();
            $fetch  = $post['target'];
            $valid  = TRUE;
            $rules  = [];
            
            switch ($fetch) {
                default:
                    $valid = FALSE;
                    break;
                case 'programming':
                    $rules  = [
                    ];
                case 'users':
                    $rules  = [
                        'input-newuser'     => 'required|alpha',
                        'input-newmail'     => 'required|valid_email',
                        'input-cnfmail'     => 'required|valid_email|matches[input-newmail]',
                        'input-newphone'    => 'required|regex_match[/^0[1-9]{3}-[0-9]{4}-[0-9]{2,5}$/]|max_length[20]',
                        'input-newpswd'     => 'required|password_strength',
                        'input-cnfpswd'     => 'required|matches[input-newpswd]'
                    ];
                    break;
            }
            
            if (!$valid) 
                $json = [
                    'status'    => 400,
                    'error'     => 400,
                    'messages'  => [
                        'error'     => 'Unrecognized client parameters'
                    ]
                ];
            else {
                $res    = $this->validate ($rules);
                if (!$res) 
                    $json = [
                        'status'    => 422,
                        'error'     => 422,
                        'messages'  => [
                            'error'     => 'Form validation failed!'
                        ]
                    ];
                else 
                    $json = [
                        'status'    => 200,
                        'error'     => NULL,
                        'messages'  => [
                            'success'   => 'Input validation success!'
                        ]
                    ];
            }
            $this->response->setContentType (HEADER_APP_JSON);
            $this->response->setJSON ($json);
            $this->response->send ();
        }
    }
}

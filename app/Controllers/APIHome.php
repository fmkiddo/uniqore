<?php
namespace App\Controllers;


use App\Libraries\AssetType;
use CodeIgniter\Files\File;

class APIHome extends BaseUniqoreController {
    
    private function isReady (): bool {
        $file = new File (SYS__UNIQORE_RANDAUTH_PATH);
        return file_exists ($file->getPathname ());
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::initComponents()
     */
    protected function __initComponents () {
        $this->helpers = [
            'url',
            'key_generator',
        ];
        $styles     = [
            'assets/vendors/bootstrap-5.3.3/css/bootstrap.min.css',
            'assets/vendors/fontawesome-6.6.0/css/all.min.css',
            'assets/css/uniqore.css'
        ];
        $this->initAssets(AssetType::STYLE, $styles);
        $scripts    = [
            'assets/vendors/bootstrap-5.3.3/js/bootstrap.min.js',
            'assets/vendors/fontawesome-6.6.0/js/all.min.js'
        ];
        $this->initAssets(AssetType::SCRIPT, $scripts);
        parent::__initComponents();
    }
    
    public function index (): string {
        if ($this->session->get ('logintime')) $this->response->redirect (base_url('uniqore/admin/dashboard?route=welcome'), 'get'); 
        
        if (!$this->isReady ()) $this->response->redirect (base_url ('uniqore/forge/0'), 'get');
        $good   = TRUE;
        $error  = '';
        if ($this->request->is ('post')) {
            $good   = FALSE;
            $res = $this->validate ([
                'login-uname'       => 'required',
                'login-pword'       => 'required',
            ]);
            if (!$res) $error  = 'You have to provide username and password for signing in!';
            else {
                $post           = $this->request->getPost ();
                $loginname      = $post['login-uname'];
                $encryptedAuth  = $this->encrypt ($this->getAuthToken ());
                
                if (!$encryptedAuth) $error  = 'An error has occured!';
                else {
                    $curlOptions    = [
                        'auth'          => [
                            bin2hex ($encryptedAuth),
                            '',
                            'basic'
                        ],
                        'headers'       => [
                            'Content-Type'  => HEADER_APP_JSON,
                            'Accept'        => HEADER_APP_JSON,
                            'User-Agent'    => $this->request->getUserAgent (),
                        ],
                    ];
                    $response   = $this->sendRequest (site_url ("api-uniqore/users?payload=find%23{$loginname}"), $curlOptions);
                    $json       = json_get ($response);
                    if (!is_array($json)) $error  = 'Invalid server response';
                    else 
                        if ($json['status'] !== 200) 
                            if ($json['status'] === 404) $error = "User {$loginname} not found!";
                            else $error = $json['messages']['error'];
                        else {
                            $payload    = $json['data']['payload'];
                            $payload    = $this->decrypt (hex2bin ($payload));
                            $payload    = unserialize ($payload);
                            
                            $user       = $payload[0];
                            $uuid       = $user['uid'];
                            $username   = $user['username'];
                            $password   = $user['password'];
                            
                            $passwordOK = password_verify ($post['login-pword'], $password);
                            if (!$passwordOK) $error = 'Please enter the correct username and password.';
                            else {
                                $good   = TRUE;
                                $sessionData    = [
                                    'logintime'     => time (),
                                    'ip_address'    => $this->request->getIPAddress (),
                                    'payload'       => [
                                        bin2hex ($this->encrypt ($uuid)),
                                        bin2hex ($this->encrypt ($username))
                                    ]
                                ];
                                $this->session->set ($sessionData);
                                $this->response->redirect (base_url ('uniqore/admin/dashboard?route=welcome'), 'get');
                            }
                        }
                } 
            }
        }
        
        $viewPaths = [
            'template_html',
            'template_header',
            'uniqore/login',
            'template_footer'
        ];
        
        $pageData = [
            'validity'      => $good,
            'error'         => $error,
            'app_title'     => UNIQORE_TITLE . " | Login Page | Please login to your account"
        ];
        return $this->renderView ($viewPaths, $pageData);
    }
    
    public function welcome () {
        $this->response->redirect(site_url('uniqore/admin'));
    }
}
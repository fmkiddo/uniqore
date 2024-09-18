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
    protected function __initComponents() {
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
        if ($this->session->get ('logintime')) $this->response->redirect(base_url('uniqore/admin/dashboard?route=welcome'), 'get'); 
        
        if (!$this->isReady ()) $this->response->redirect (base_url ('uniqore/forge/0'), 'get');
        $good   = TRUE;
        $error  = '';
        if ($this->request->getMethod () === 'POST') {
            $good   = FALSE;
            $this->validation->setRules ([
                'login-uname'       => 'required',
                'login-pword'       => 'required'
            ]);
            $res = $this->validation->withRequest ($this->request)->run();
            if (!$res) $error  = 'You have to provide username and password for signing in!';
            else {
                $post           = $this->request->getPost ();
                $encryptedAuth  = $this->encrypt ($this->getAuthToken ());
                if (!$encryptedAuth) 
                    $error  = 'An error has occured!';
                else {
                    $curlOptions    = [
                        'auth'      => [
                            bin2hex ($encryptedAuth),
                            '',
                            'basic'
                        ],
                        'headers'   => [
                            'Content-Type'  => HEADER_APP_JSON,
                            'Accept'        => HEADER_APP_JSON,
                            'User-Agent'    => $this->request->getUserAgent (),
                            'Address'       => $this->request->getIPAddress ()
                        ],
                        'json'      => [
                            'execute'       => 'login'
                        ]
                    ];
                    $response   = $this->sendRequest (base_url ('api-uniqore/users'), $curlOptions, 'get');
                    $getJSON    = json_decode ($response->getBody (), TRUE);
                    if ($getJSON['status'] !== 200) $error      = $getJSON['messages']['error'];
                    else {
                        $payload    = $getJSON['data']['payload'];
                        $payload    = hex2bin ($payload);
                        $payload    = $this->decrypt ($payload);
                        $payload    = unserialize ($payload);
                        
                        $userFound  = FALSE;
                        $password   = '';
                        $uuid       = '';
                        foreach ($payload as $user) 
                            if ($user['username'] === $post['login-uname'] ||
                                    $user['email'] === $post['login-uname'] ||
                                    $user['phone'] === $post['login-uname']) {
                                $userFound  = TRUE;
                                $uuid       = $user['uid'];
                                $password   = $user['password'];
                                break;
                            }
                        
                        $passwordOK = FALSE;
                        if (!$userFound) $error  = 'Please enter the correct username and password.';
                        else $passwordOK = password_verify($post['login-pword'], $password);
                        
                        if (!$passwordOK) $error = 'Please enter the correct username and password.';
                        else {
                            $good   = TRUE;
                            $sessionData    = [
                                'logintime'     => time (),
                                'ip_address'    => $this->request->getIPAddress (),
                                'payload'       => bin2hex ($this->encrypt ($uuid))
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
            'csrf_name'     => csrf_token (),
            'csrf_value'    => csrf_hash (),
            'validity'      => $good,
            'error'         => $error
        ];
        return $this->renderView ($viewPaths, $pageData);
    }
}
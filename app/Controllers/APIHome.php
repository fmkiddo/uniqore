<?php
namespace App\Controllers;


use App\Libraries\AssetType;
use App\Libraries\Forgery\Templates\UniqoreDatabaseTemplate;
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
        // TODO Auto-generated method stub
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
        if (!$this->isReady ()) $this->response->redirect (base_url ('uniqore/forge/0'), 'get');
        
        $good = TRUE;
        if ($this->request->getMethod () === 'POST') {
            $this->validation->setRules ([
                'login-uname'       => 'required',
                'login-pword'       => 'required'
            ]);
            $res = $this->validation->withRequest($this->request)->run();
            if ($res) {
                $post           = $this->request->getPost ();
                $encryptedAuth  = $this->encryptor->encrypt ($this->getAuthToken());
                $curlOptions    = [
                    'auth'      => [
                        bin2hex($encryptedAuth),
                        '',
                        'basic'
                    ],
                    'headers'   => [
                        'Content-Type'  => HEADER_APP_JSON,
                        'Accept' => HEADER_APP_JSON
                    ]
                ];
                var_dump ($curlOptions);
                $response = $this->sendRequest(base_url ('api-uniqore/users'), $curlOptions, 'get');
                var_dump (json_decode(json_decode($response->getJSON(), TRUE)));
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
            'validated'     => $good
        ];
        return $this->renderView ($viewPaths, $pageData);
    }
}
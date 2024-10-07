<?php
namespace App\Controllers;

use App\Libraries\AssetType;
use App\Libraries\PageViews;

class APIDashboard extends BaseUniqoreController {
    
    private $pageViews;
    
    private $routes = [
        'apiadmin'  => 'users',
        'api'       => 'programming',
        'clients'   => 'apiuser'
    ];
    
    private $subroutes  = [
        'account'   => 'apiuser',
        'profile'   => 'apiuserprofile',
        'config'    => 'apiuserconfig'
    ];
    
    private $titles = [
        'welcome'       => 'Welcome | Summaries',
        'apiadmin'      => 'Uniqore Administrators',
        'api'           => 'Registered and Supported API',
        'clients'       => 'Regsitered API Users',
        'documentation' => 'Uniqore Documentations'
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
            case 'programming':
                return [
                    'apicode'       => $post['input-newcode'],
                    'apiname'       => $post['input-newname'],
                    'apidscript'    => $post['input-newdscript'],
                    'apiprefix'     => $post['input-newprefix'],
                    'status'        => array_key_exists('input-newstatus', $post) ? TRUE : FALSE
                ];
            case 'users': 
                if ($post['target'] === 'password-change') {
                    $userData   = explode ('#', $post['user-data']);
                    $username   = $userData[1];
                    $email      = $userData[2];
                    $phone      = $userData[3];
                    $password   = $post['input-newpswd'];
                    $active     = boolval ($userData[4]);
                } else {
                    $username   = $post['input-newuser'];
                    $email      = $post['input-newmail'];
                    $phone      = $post['input-newphone'];
                    $password   = $post['input-newpswd'];
                    $active     = array_key_exists('input-active', $post) ? TRUE : FALSE;
                }
                return [
                    'username'      => $username,
                    'email'         => $email,
                    'phone'         => str_replace ('-', '', $phone),
                    'password'      => $password,
                    'active'        => $active
                ];
            case 'apiuser':
                return [
                    'account'   => [
                        'clientcode'        => $post['input-newccode'],
                        'clientapi'         => $post['input-newcapi'],
                        'clientstatus'      => (array_key_exists ('input-newcstatus', $post) ? TRUE : FALSE),
                    ],
                    'profile'   => [
                        'clientid'          => '',
                        'clientname'        => $post['input-newcname'],
                        'clientlname'       => $post['input-newclname'],
                        'clientaddr1'       => $post['input-newcaddr1'],
                        'clientaddr2'       => $post['input-newcaddr2'],
                        'clientphone'       => $post['input-newcphone'],
                        'clienttaxn'        => $post['input-newctax'],
                        'clientpicname'     => $post['input-newcpic'],
                        'clientpicmail'     => $post['input-newcpicmail'],
                        'clientpicphone'    => $post['input-newcpicphone'],
                    ],
                    'config'    => [
                        'clientid'          => '',
                        'clientdbname'      => $post['input-newcdbname'],
                        'clientdbuser'      => $post['input-newcdbuser'],
                        'clientdbpswd'      => $post['input-newcdbpswd'],
                        'clientdbprefix'    => $post['input-newcdbprefix']
                    ]
                ];
                break;
        }
    }
    
    private function apiProcessor ($get): array|bool {
        if ($this->request->is ('post')) {
            $post       = $this->request->getPost ();
            $pollute    = base64_encode ($this->getLoggedUUID ());
            $uuid       = $post['input-uuid'];
            
            $routes     = $this->routes[$get['route']];
            
            $auth       = $this->encrypt ($this->getAuthToken ());
            $formParam  = $this->formParamFormatter($routes, $post);
            
            if ($routes !== 'apiuser') {
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
                    'json'          => $formParam
                ];
                
                $json   = [];
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
            } else {
                $insertID       = 0;
                $retPayload     = [
                    'uuid'          => '',
                    'clientid'      => '',
                    'serial'        => ''
                ];
                
                $i  = 0;
                foreach ($formParam as $key => $param) {
                    if ($insertID !== 0) $param['clientid']  = $insertID;
                    
                    $curlOpts   = [
                        'delay'         => 2000,
                        'auth'      => [
                            bin2hex ($auth),
                            '',
                            'basic'
                        ],
                        'headers'   => [
                            'Content-Type'  => HEADER_APP_JSON,
                            'Accept'        => HEADER_APP_JSON,
                            'User-Agent'    => $this->request->getUserAgent ()
                        ],
                        'json'      => $param
                    ];
                    
                    $subroutes  = $this->subroutes[$key];
                    if ($uuid === 'none') {
                        $url    = site_url ("api-uniqore/$subroutes?pollute=$pollute");
                        $method = 'post';
                    } else {
                    };
                    
                    $response   = $this->sendRequest ($url, $curlOpts, $method);
                    $response   = json_decode ($response->getBody(), TRUE);
                    if ($response['status'] === 200) {
                        $payload    = $response['data']['payload'];
                        $payload    = unserialize ($this->decrypt (hex2bin ($payload)));
                        if ($key === 'account') {
                            $insertID               = $payload['returnid'];
                            $retPayload['uuid']     = $payload['uuid'];
                            $retPayload['clientid'] = $param['clientcode'];
                            $retPayload['serial']   = $payload['serial'];
                        }
                        $i++;
                    }
                }
                
                $json   = [];
                if ($i === 3) {
                    $hexed  = serialize ($retPayload);
                    $hexed  = bin2hex ($this->encrypt ($hexed));
                    $json   = [
                        'status'    => 200,
                        'error'     => NULL,
                        'messages'  => [
                            'success'   => 'New API user successfully registered!'
                        ],
                        'data'      => [
                            'uuid'      => time (),
                            'timestamp' => date ('Y-m-d H:i:s'),
                            'payload'   => $hexed
                        ]
                    ];
                } else 
                    $json   = [
                        'status'    => 500,
                        'error'     => 500,
                        'messages'  => [
                            'error'     => 'New API user registration operation has failed'
                        ]
                    ];
                return $json;
            }
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
            'assets/vendors/bootstrap-5.3.3/js/bootstrap.bundle.min.js',
            'assets/vendors/datatables-2.1.6/js/datatables.min.js',
            'assets/vendors/fontawesome-6.6.0/js/all.min.js',
            'assets/js/uniqore.js',
        ];
        $this->initAssets(AssetType::SCRIPT, $scripts);
        parent::__initComponents();
    }
    
    public function index (): string {
        if ($this->session->get ("logintime") === NULL) {
            $this->response->redirect (base_url ("uniqore/admin"));
            return "OK!";
        }
        
        $get = $this->request->getGet ();
        
        $res    = $this->apiProcessor ($get);
        
        if (count ($get) > 0 && array_key_exists("route", $get)) $route = $get["route"];
        else $route = "welcome";
        
        $render     = TRUE;
        
        $retVal     = "";
        $viewPaths  = [];
        $dtsFetch   = '';
        if ($route !== 'sign-out') $this->pageViews->fetchPage ($route, $dtsFetch, $viewPaths);
        else {
            $viewPaths  = [];
            $render     = FALSE;
            $retVal     = $this->doSignOut ();
        } 
        
        if ($render) {
            $pageData   = [
                'app_title'     => UNIQORE_TITLE . " | Dashboard - {$this->titles[$get["route"]]}",
                'dashboard_url' => site_url ('uniqore/admin/dashboard'),
                'validate_url'  => site_url ('uniqore/admin/dashboard/validate'),
                'generate_url'  => site_url ('uniqore/generator'),
                'username'      => $this->getUserName (),
                'realname'      => '',
                'dts_fetch'     => $dtsFetch,
                'alertShow'     => FALSE
            ];
            
            if (is_array ($res)) {
                $payload    = unserialize ($this->decrypt (hex2bin ($res['data']['payload'])));
                if ($route === 'clients') {
                    $pageData['alertShow']  = TRUE;
                    $pageData['user_code']  = $payload['clientid'];
                    $pageData['user_sn']    = $payload['serial'];
                }
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
                        'input-newcode'     => 'required|alpha',
                        'input-newname'     => 'required',
                    ];
                    break;
                case 'password-change':
                    $rules  = [
                        'input-oldpswd'     => 'required',
                        'input-newpswd'     => 'required|password_strength',
                        'input-cnfpswd'     => 'required|matches[input-newpswd]'
                    ];
                    break;
                case 'users':
                    if ($post['input-uuid'] === 'none')
                        $rules  = [
                            'input-newuser'     => 'required|alpha',
                            'input-newmail'     => 'required|valid_email',
                            'input-cnfmail'     => 'required|valid_email|matches[input-newmail]',
                            'input-newphone'    => 'required|regex_match[/^0[1-9]{3}-[0-9]{4}-[0-9]{2,5}$/]|max_length[20]',
                            'input-newpswd'     => 'required|password_strength',
                            'input-cnfpswd'     => 'required|matches[input-newpswd]',
                        ];
                    else
                        $rules  = [
                            'input-newuser'     => 'required|alpha',
                            'input-newmail'     => 'required|valid_email',
                            'input-cnfmail'     => 'required|valid_email|matches[input-newmail]',
                            'input-newphone'    => 'required|regex_match[/^0[1-9]{3}-[0-9]{4}-[0-9]{2,5}$/]|max_length[20]',
                        ];
                    break;
                case 'apiuser':
                    $rules  = [
                        'input-newcname'        => 'required',
                        'input-newccode'        => 'required',
                        'input-newcapi'         => 'required',
                        'input-newclname'       => 'required',
                        'input-newcpic'         => 'required',
                        'input-newcpicmail'     => 'required',
                        'input-newcpicphone'    => 'required',
                        'input-newcdbname'      => 'required',
                        'input-newcdbuser'      => 'required',
                        'input-newcdbpswd'      => 'required|password_strength',
                        'input-newcdbprefix'    => 'required'
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
                else {
                    if ($fetch !== 'password-change') 
                        $json = [
                            'status'    => 200,
                            'error'     => NULL,
                            'messages'  => [
                                'success'   => 'Input validation success!'
                            ]
                        ];
                    else {
                        $userData   = explode ('#', $post['user-data']);
                        $pswd       = $this->decrypt (hex2bin ($userData[5]));
                        $res        = password_verify ($post['input-oldpswd'], $pswd);
                        if (!$res)
                            $json       = [
                                'status'    => 401,
                                'error'     => 401,
                                'messages'  => [
                                    'error'     => 'Old password does not match'
                                ]
                            ];
                        else
                            $json       = [
                                'status'    => 200,
                                'error'     => NULL,
                                'messages'  => [
                                    'success'   => 'Old password verified!'
                                ]
                            ];
                    }
                }
            }
            $this->response->setContentType (HEADER_APP_JSON);
            $this->response->setJSON ($json);
            $this->response->send ();
        }
    }
}

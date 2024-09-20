<?php
namespace App\Controllers;


use App\Libraries\AssetType;
use App\Libraries\Forgery\DBForger;
use App\Libraries\Forgery\Templates\UniqoreDatabaseTemplate;
use App\Libraries\Forgery\DatabaseTemplate;
use CodeIgniter\Files\File;
use CodeIgniter\Encryption\Encryption;


class UniqoreForger extends BaseController {
    
    private function isInitiated (): bool {
        $file           = new File (SYS__UNIQORE_RANDAUTH_PATH);
        $file_exists    = file_exists($file->getPathname ());
        
        $dbConfig       = config ('Database');
        $db_configured  = strlen ($dbConfig->default['database']) > 0;
        return ($file_exists && $db_configured);
    }
    
    /**
     * 
     * @param string $secretKey
     * @param string $dbconfig
     * @param DatabaseTemplate $dbtpl
     * @return bool
     */
    private function appendEnv ($secretKey, $dbconfig, $dbtpl): bool {
        $db_conf    = explode('.', $dbconfig);
        $dbtpl->setDatabaseName ($db_conf[0]);
        $dbtpl->setDatabaseUser ($db_conf[1]);
        $dbtpl->setDatabasePassword ($db_conf[2]);
        $envContent = "

database.default.hostname   = localhost
database.default.database   = {$db_conf[0]}
database.default.username   = {$db_conf[1]}
database.default.password   = {$db_conf[2]}
database.default.DBDriver   = MySQLi
database.default.DBPrefix   = {$dbtpl->getDatabasePrefix ()}
database.default.charset    = utf8mb4
database.default.DBCOllat   = utf8mb4_general_ci
database.default.port       = 3306

encryption.driver           = Sodium
encryption.cipher           = XChaCha20-Poly1305
encryption.key              = hex2bin:{$secretKey}

";
        return write_file('../.env', $envContent, 'a');
    }
    
    private function generateRandomAuth (): bool {
        $random     = generate_token (64);
        $token      = "uniqore.{$random}";
        return write_file (SYS__UNIQORE_RANDAUTH_PATH, $token, 'wb');
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::initComponents()
     */
    
    protected function __initComponents() {
        // TODO Auto-generated method stub
        $this->helpers = [
            'url',
            'filesystem',
            'key_generator',
            'uuid'
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
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::decrypt()
     */
    protected function decrypt($encrypted): string|bool {
        return FALSE;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::encrypt()
     */
    protected function encrypt($plainText): string|bool {
        return FALSE;
    }
    
    public function index ($pageNum=0): string {
        $view   = '';
        switch ($pageNum) {
            default: 
                $this->generateJSON404 ();
                break;
            case 0:
                if ($this->isInitiated ()) $this->response->redirect (base_url ('admin'));
                
                $viewPaths  = [
                    'template_html',
                    'template_header',
                    'forger/uniqore_forgery_body_header',
                    'forger/uniqore_forgery_00',
                    'template_footer',
                ];
                $view = $this->renderView($viewPaths);
                break;
            case 1:
                if ($this->isInitiated ()) $this->response->redirect (base_url ('admin'));
                if ($this->request->getMethod() === 'GET') $this->generateJSON404 ();
                else {
                    $post   = $this->request->getPost ();
                    if (!array_key_exists('begin', $post)) {
                        $this->validation->setRules ([
                            'newsuname'     => 'required',
                            'newsumail'     => 'required|valid_email',
                            'newsuphone'    => 'required|regex_match[/^0[1-9]{3}-[0-9]{4}-[0-9]{2,5}$/]|max_length[20]',
                            'newsupswd'     => 'required|password_strength',
                            'cnfmpswd'      => 'required|matches[newsupswd]'
                        ]);
                        $res = $this->validation->withRequest ($this->request)->run ();
                        if (!$res) {
                            $viewPaths  = [
                                'template_html',
                                'template_header',
                                'forger/uniqore_forgery_body_header',
                                'forger/uniqore_forgery_01',
                                'template_footer',
                            ];
                            $pageData   = [
                                'validated'     => TRUE,
                                'newsukey'      => $post['newsukey'],
                                'newsudb'       => $post['newsudb']
                            ];
                            $view = $this->renderView($viewPaths, $pageData);
                        } else {
                            $options    = [
                                'headers'   => [
                                    'Content-Type'  => 'application/x-www-form-urlencoded',
                                    'Accept'        => HEADER_APP_JSON
                                ],
                                'form_params'       => $this->request->getPost ()
                            ];
                            $response   = $this->sendRequest (base_url ('uniqore/forge/starts'), $options, 'post');
                            $json       = json_decode($response->getBody (), TRUE);
                            if ($json['status'] !== 200) 
                                $pageData = [
                                    'forger_success'    => FALSE
                                ];
                            else 
                                $pageData = [
                                    'forger_success'    => TRUE,
                                    'redirect'          => base_url ('admin')
                                ];
                            $viewPaths = [
                                'template_html',
                                'template_header',
                                'forger/uniqore_forgery_body_header',
                                'forger/uniqore_forgery_done',
                                'template_footer',
                            ];
                            
                            $view = $this->renderView ($viewPaths, $pageData);
                            $this->response->setHeader ('Refresh', '10,url=' . base_url('uniqore/admin'));
                        }
                    } else {
                        $this->validation->setRules([
                            'begin'     => 'required|string',
                            'key'       => 'required|string',
                            'dbname'    => 'required|string',
                            'dbuser'    => 'required|string',
                            'dbpswd'    => 'required|min_length[8]|password_strength',
                            'cfpswd'    => 'required|matches[dbpswd]'
                        ]);
                        $res = $this->validation->withRequest ($this->request)->run ();
                        if (!$res) return $this->response->redirect (base_url ('admin')); 
                        else {
                            $viewPaths  = [
                                'template_html',
                                'template_header',
                                'forger/uniqore_forgery_body_header',
                                'forger/uniqore_forgery_01',
                                'template_footer',
                            ];
                            
                            $newsudb    = "{$post['dbname']}.{$post['dbuser']}.{$post['dbpswd']}";
                            
                            $pageData   = [
                                'validated'     => FALSE,
                                'newsukey'      => $post['key'],
                                'newsudb'       => $newsudb
                            ];
                            $view = $this->renderView ($viewPaths, $pageData);
                        }
                    }
                }
                break;
            case 'starts':
                if ($this->request->getMethod() === 'GET') $this->generateJSON404 ();
                else {
                    $json   = [];
                    $post   = $this->request->getPost ();
                    if (!array_key_exists ('newsuname', $post)) {
                        $json = [
                            'status'    => 500,
                            'message'   => 'Unable to process input',
                            'go-home'   => base_url ('uniqore/admin')
                        ];
                    } else {
                        $dbtpl  = new UniqoreDatabaseTemplate ();
                        if ($this->generateRandomAuth () && $this->appendEnv ($post['newsukey'], $post['newsudb'], $dbtpl)) {
                            $forger = new DBForger ($dbtpl);
                            if (!$forger->isDatabaseExists ()) {
                                $built = $forger->buildDatabase ();
                                if (!$built) {
                                    delete_files(SYS__UNIQORE_RANDAUTH_PATH);
                                    $json = [
                                        'status'    => 400,
                                        'message'   => 'Uniqore database forgery failed!',
                                        'go-home'   => base_url ('admin')
                                    ];
                                } else {
                                    $uid    = generate_random_uuid_v4 ();
                                    $phone  = str_replace ('-', '', $post['newsuphone']);
                                    $passwd = password_hash ($post['newsupswd'], PASSWORD_BCRYPT);
                                    $now    = date ('Y-m-d H:i:s');
                                    $query  = "INSERT INTO fmk_ousr (uid, username, email, phone, password, created_by, updated_at, updated_by)
                                                VALUES ('{$uid}', '{$post['newsuname']}', '{$post['newsumail']}', '{$phone}', '{$passwd}', 1, '{$now}', 1);";
                                    $res = $built->query ($query);
                                    if (!$res) $json = [
                                            'status'    => 500,
                                            'message'   => 'Unable to initiate Uniqore Database',
                                            'go-home'   => base_url ('admin')
                                        ];
                                    else $json = [
                                            'status'    => 200,
                                            'message'   => 'Uniqore database forgeries completed!',
                                            'go-home'   => base_url ('admin')
                                        ];
                                }
                                $built->close ();
                            }
                        }
                    }
                    
                    $this->response->setHeader ('Content-Type', HEADER_APP_JSON);
                    $this->response->setJSON ($json);
                    $this->response->send ();
                }
                break;
        }
        return $view;
    }
    
    public function passwordRandomize () {
        $pswd   = generate_password(32);
        $json   = [
            'status'    => 200,
            'error'     => null,
            'messages'  => [
                'success: Password successfully generated!',
                'status: OK!'
            ],
            'data'      => [
                'password'  => "{$pswd}"
            ]
        ];
        $this->response->setContentType (HEADER_APP_JSON);
        $this->response->setJSON ($json);
        $this->response->send ();
    }
    
    public function sodiumKey () {
        $key    = Encryption::createKey (SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_KEYBYTES);
        $key    = bin2hex ($key);
        $json   = [
            'status'    => 200,
            'error'     => null,
            'messages'  => [
                'success: Key successfully generated!',
                'status: OK!'
            ],
            'data'      => [
                'key'       => "{$key}"
            ]
        ];
        $this->response->setContentType (HEADER_APP_JSON);
        $this->response->setJSON ($json);
        $this->response->send ();
    }
}
<?php
namespace App\Controllers;


use App\Libraries\AssetType;
use App\Libraries\Forgery\DBForger;
use App\Libraries\Forgery\Templates\UniqoreDatabaseTemplate;


class UniqoreForger extends BaseController {
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::initComponents()
     */
    
    protected function initComponents() {
        // TODO Auto-generated method stub
        $this->helpers = [
            'url',
            'filesystem'
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
        parent::initComponents();
    }
    
    public function index ($pageNum=0): string {
        $view   = '';
        switch ($pageNum) {
            default: 
                $this->generateJSON404();
                break;
            case 0:
                $viewPaths  = [
                    'template_html',
                    'template_header',
                    'forger/uniqore_forgery_body_header',
                    'forger/uniqore_forgery_00',
                    'template_footer',
                ];
                $pageData   = [
                    'csrf_name'     => csrf_token(),
                    'csrf_data'     => csrf_hash()
                ];
                $view = $this->renderView($viewPaths, $pageData);
                break;
            case 1:
                if ($this->request->getMethod() === 'GET') $this->generateJSON404 ();
                else {
                    $post   = $this->request->getPost ();
                    if (!array_key_exists('begin', $post)) {
                        $this->validation->setRules ([
                            'newsuname'     => 'required',
                            'newsumail'     => 'required|valid_email',
                            'newsuphone'    => 'required|regex_match[/^0[1-9]{3}-[0-9]{4}-[0-9]{2,5}$/]|max_length[20]',
                            'newsupswd'     => 'required|min_length[8]',
                            'cnfmpswd'      => 'required|min_length[8]|matches[newsupswd]'
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
                                'csrf_name'     => csrf_token(),
                                'csrf_data'     => csrf_hash(),
                                'validated'     => TRUE         
                            ];
                            $view = $this->renderView($viewPaths, $pageData);
                        } else {
                            $curl       = \Config\Services::curlrequest();
                            $options    = [
                                'headers'   => [
                                    'Content-Type'  => 'application/x-www-form-urlencoded',
                                    'Accept'        => HEADER_APP_JSON
                                ],
                                'form_params'       => $this->request->getPost ()
                            ];
                            $response   = $curl->post (base_url ('uniqore/forge/starts'), $options);
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
                            $this->response->setHeader ('Refresh', '5,url=' . base_url('admin'));
                        }
                    } else {
                        $this->validation->setRules([
                            'begin' => 'required|string'
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
                            $pageData   = [
                                'csrf_name'     => csrf_token(),
                                'csrf_data'     => csrf_hash(),
                                'validated'     => FALSE
                            ];
                            $view = $this->renderView ($viewPaths, $pageData);
                        }
                    }
                }
                break;
            case 'starts':
                if ($this->request->getMethod() === 'GET') $this->generateJSON404 ();
                else {
                    $post   = $this->request->getPost ();
                    if (!array_key_exists ('newsuname', $post)) {
                        $json = [
                            'status'    => 500,
                            'message'   => 'Unable to process input',
                            'go-home'   => base_url ('admin')
                        ];
                    } else {
                        $config = config ('Database');
                        $dbuser = $config->default['username'];
                        $dbpswd = $config->default['password'];
                        $forger = new DBForger(new UniqoreDatabaseTemplate (), $dbuser, $dbpswd);
                        if (!$forger->isDatabaseExists ()) {
                            $built = $forger->buildDatabase ();
                            if (!$built) {
                                $json = [
                                    'status'    => 400,
                                    'message'   => 'Uniqore database forgery failed!',
                                    'go-home'   => base_url ('admin')
                                ];
                            } else {
                                $passwd = password_hash($post['newsupswd'], PASSWORD_BCRYPT);
                                $phone  = str_replace('-', '', $post['newsuphone']);
                                $now    = date ('Y-m-d H:i:s');
                                $query  = "INSERT INTO fmk_ousr (username, email, phone, password, created_by, updated_at, updated_by) 
                                            VALUES ('{$post['newsuname']}', '{$post['newsumail']}', '{$phone}', '{$passwd}', 1, '{$now}', 1);";
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
}
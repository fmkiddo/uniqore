<?php
namespace App\Controllers;


class APIFetcher extends BaseUniqoreController {
    
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::__initComponents()
     */
    protected function __initComponents() {
        array_push ($this->helpers, 'key_generator');
        parent::__initComponents();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::index()
     */
    public function index(): string {
        if ($this->request->is ('get')) return $this->generateJSON404 ();
        $post       = $this->request->getPost ();
        $draw       = (array_key_exists ('draw', $post)) ? $post['draw'] : 0;
        $fetcher    = $post ['fetch'];
        $searchVal  = (array_key_exists ('search', $post) ? $post['search']['value'] : '');
        $sortTarget = 0;
        $sort       = '';
        if (array_key_exists ('order', $post)) {
            $sortTarget = $this->columnTranslator ($fetcher, $post ['order'][0]['column']);
            $sort       = (!$sortTarget) ? '' : $post ['order'][0]['dir'];
        }
        
        $curlOpts   = [
            'auth'      => [
                bin2hex ($this->encrypt ($this->getAuthToken ())),
                '',
                'basic'
            ],
            'headers'   => [
                'Content-Type'  => HEADER_APP_JSON,
                'Accept'        => HEADER_APP_JSON,
                'User-Agent'    => $this->request->getUserAgent (),
            ]
        ];
        
        $uuid   = base64_encode ($this->getLoggedUUID ());
        $load   = "find%23{$searchVal}&colsort={$sortTarget}&typesort={$sort}";
        $url    = site_url ("api-uniqore/{$fetcher}?payload={$load}&pollute=$uuid");
        
        $response   = $this->sendRequest($url, $curlOpts);
        $json       = json_get ($response);
        
        if ($json['status'] !== 200) $theData    = [];
        else {
            $payload    = $json['data']['payload'];
            $payload    = unserialize ($this->decrypt (hex2bin ($payload)));
            $theData    = [];
            if (count ($payload) > 0) 
                if (!array_key_exists ('opttype', $post)) $this->dataFormatter ($fetcher, $payload, $theData);
                else $this->optionFormatter ($fetcher, $payload, $theData);
        }
        
        $fetched    = [
            'draw'              => $draw,
            'recordsTotal'      => count ($theData),
            'recordsFiltered'   => count ($theData),
            'data'              => $theData
        ];
        
        $this->response->setContentType (HEADER_APP_JSON);
        $this->response->setJSON($fetched);
        $this->response->send ();
        return '';
    }
    
    public function dataGenerator () {
        if ($this->request->is ('get')) return $this->generateJSON404 (); 
        $post   = $this->request->getPost ();
        $json   = [];

        if (array_key_exists('event', $post)) {
            $clientName = $post['input-newcname'];
            if (! strlen (trim ($clientName)))
                $json   = [
                    'status'    => 400,
                    'error'     => 400,
                    'messages'  => [
                        'error'     => 'Please specify client name first before trying generate data'
                    ]
                ];
            else {
                $clientName     = explode (' ', $clientName);
                switch ($post['event']) {
                    default:
                        $json   = [
                            'status'    => 400,
                            'error'     => 400,
                            'messages'  => [
                                'error'     => 'Bad Request: invalid request payload'
                            ]
                        ];
                        break;
                    case 'generate-ccode':
                        if (count ($clientName) === 1) $low = substr (strtolower ($clientName[0]), 0, 6);
                        else $low = substr (strtolower ($clientName[0]), 0, 3) . substr (strtolower ($clientName[1]), 0, 3);
                        $random = generate_token (16);
                        $result = "{$low}_{$random}";
                        $json   = [
                            'status'    => 200,
                            'error'     => NULL,
                            'messages'  => [
                                'success'   => 'OK! Generated'
                            ],
                            'data'      => [
                                'uuid'      => time (),
                                'timestamp' => date ('Y-m-d H:i:s'),
                                'payload'   => $result
                            ]
                        ];
                        break;
                    case 'generate-dbname':
                    case 'generate-dbuser':
                        $api    = array_key_exists ('input-newcapi', $post) ? $post['input-newcapi'] : NULL;
                        if ($api === NULL)
                            $json   = [
                                'status'    => 400,
                                'error'     => NULL,
                                'messages'  => [
                                    'error'     => 'Please select client API before trying generate data'
                                ]
                            ];
                        else {
                            if (count ($clientName) === 1) $low = substr (strtolower ($clientName[0]), 0, 6);
                            else $low = substr (strtolower ($clientName[0]), 0, 3) . substr (strtolower ($clientName[1]), 0, 2);
                            $random =   generate_token (UNIQORE_RANDOM_CLIENT);
                            $result = "{$api}_$low$random";
                            $json   = [
                                'status'    => 200,
                                'error'     => NULL,
                                'messages'  => [
                                    'success'   => 'OK! Generated'
                                ],
                                'data'      => [
                                    'uuid'      => time (),
                                    'timestamp' => date ('Y-m-d H:i:s'),
                                    'payload'   => $result
                                ]
                            ];
                        }
                        break;
                    case 'generate-dbpswd':
                        $longStrongPassword = generate_password (UNQIORE_RANDOM_DBPSWD);
                        $json   = [
                            'status'    => 200,
                            'error'     => NULL,
                            'messages'  => [
                                'success'   => 'OK! Generated'
                            ],
                            'data'      => [
                                'uuid'      => time (),
                                'timestamp' => date ('Y-m-d H:i:s'),
                                'payload'   => $longStrongPassword
                            ]
                        ];
                        break;
                }
            }
        }
        
        $this->response->setHeader ('Content-Type', HEADER_APP_JSON);
        $this->response->setBody (json_encode ($json));
        $this->response->send ();
    }
    
    private function optionFormatter ($fetcher, $payload, &$theData) {
        switch ($fetcher) {
            case 'programming':
                foreach ($payload as $api) {
                    $data   = [
                        'fetch'         => $fetcher,
                        'api'           => base64_encode ($api['uid']),
                        'apicode'       => $api['api_code'],
                        'apiname'       => $api['api_name'],
                        'apidscript'    => $api['api_dscript'], 
                        'apiprefix'     => $api['api_prefix'],
                    ];
                    array_push ($theData, $data);
                }
                break;
        }
    }
    
    private function dataFormatter ($fetcher, $payload, &$theData) {
        switch ($fetcher) {
            default:
                break;
            case 'users':
                $i  = 1;
                foreach ($payload as $user) {
                    $username   = $user['username'];
                    $email      = $user['email'];
                    $phone      = $user['phone'];
                    $phone      = sprintf ('%s-%s-%s', substr ($phone, 0, 4), substr ($phone, 4, 4), substr ($phone, 8, 4));
                    $password   = bin2hex ($this->encrypt ($user['password']));
                    $status     = $user['active'];
                    
                    $viewData   = [
                        'uuid'      => base64_encode ($user['uid']),
                        'username'  => $username,
                        'email'     => $email,
                        'phone'     => $phone,
                        'password'  => $password,
                        'status'    => $status ? 'true' : 'false'
                    ];
                    
                    $row        = [
                        $i,
                        $username,
                        $email,
                        $phone,
                        ($status ? 'active' : 'inactive'),
                        view ('uniqore/dropdowns/user-dropdown', $viewData)
                    ];
                    array_push ($theData, $row);
                    $i++;
                }
                break;
            case 'programming':
                $i  = 1;
                foreach ($payload as $api) {
                    $code       = $api['api_code'];
                    $name       = $api['api_name'];
                    $dscript    = $api['api_dscript'];
                    $status     = $api['status'];
                    
                    $viewData   = [
                        'uuid'      => base64_encode ($api['uid']),
                        'code'      => $code,
                        'name'      => $name,
                        'dscript'   => $dscript,
                        'prefix'    => $api['api_prefix'],
                        'status'    => $status ? 'true' : 'false'
                    ];
                    
                    $row        = [
                        $i,
                        $code,
                        $name,
                        $dscript,
                        ($status ? 'active' : 'inactive'),
                        view ('uniqore/dropdowns/api-dropdown', $viewData)
                    ];
                    array_push ($theData, $row);
                    $i++;
                }
                break;
            case 'apiuser':
                $i = 1;
                foreach ($payload as $client) {
                    $clientcode     = $client['client_code'];
                    $clientdata     = $client['client_data'];
                    $clientprofile  = $client['client_info'];
                    $clientapi      = $client['client_api'];
                    $status         = $clientdata['status'];
                    $row    = [
                        $i,
                        $clientcode,
                        $clientprofile['legal_name'],
                        $clientapi['name'],
                        ($status ? 'active' : 'inactive'),
                        ''
                    ];
                    array_push ($theData, $row);
                    $i++;
                }
                break;
        }
    }
    
    private function columnTranslator ($fetcher, $col) {
        if ($col == 0) return FALSE;
        switch ($fetcher) {
            default:
                $cols   = [
                    'wew'
                ];
                break;
            case 'users':
                $cols   = [
                    'username', 'email', 'phone'
                ];
                break;
            case 'programming':
                $cols   = [
                    'api_code', 'api_name', 'api_dscript', 'status'
                ];
                break;
            case 'apiuser':
                $cols   = [
                    'client_code', 'client_name', 'client_apicode', 'status'
                ];
                break;
        }
        return $cols[$col-1];
    }
}
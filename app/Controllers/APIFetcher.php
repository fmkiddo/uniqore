<?php
namespace App\Controllers;


class APIFetcher extends BaseUniqoreController {
    
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::__initComponents()
     */
    protected function __initComponents() {
        parent::__initComponents();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::index()
     */
    public function index(): string {
        $post       = $this->request->getPost ();
        $fetcher    = $post ['fetch'];
        $searchVal  = $post ['search']['value'];
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
        
        if ($json['status'] !== 200) {
            $theData    = [];
        } else {
            $payload    = $json['data']['payload'];
            $payload    = unserialize ($this->decrypt (hex2bin ($payload)));
            $theData    = [];
            $this->dataFormatter ($fetcher, $payload, $theData);
        }
        
        $fetched    = [
            'draw'              => $post['draw'],
            'recordsTotal'      => count ($theData),
            'recordsFiltered'   => count ($theData),
            'data'              => $theData
        ];
        
        $this->response->setContentType (HEADER_APP_JSON);
        $this->response->setJSON($fetched);
        $this->response->send ();
        return '';
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
                    $uuid       = $api['uid'];
                    $code       = $api['api_code'];
                    $name       = $api['api_name'];
                    $dscript    = $api['api_dscript'];
                    $status     = $api['status'];
                    
                    $viewData   = [
                        'uuid'      => $uuid,
                        'code'      => $code,
                        'name'      => $name,
                        'dscript'   => $dscript,
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
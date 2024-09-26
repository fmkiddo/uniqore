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
    public function index (): string {
        $post       = $this->request->getPost ();
        $fetcher    = base64_decode ($post ['fetch'], TRUE);
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
        
        $response   = $this->sendRequest ($url, $curlOpts);
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
                $i = 1;
                foreach ($payload as $user) {
                    $uuid   = base64_encode ($user['uid']);
                    $uname  = $user['username'];
                    $email  = $user['email'];
                    $status = $user['active'];
                    $phone  = $user['phone'];
                    $phone  = substr ($phone, 0, 4) . '-' . substr ($phone, 4, 4) . '-' . substr ($phone, 8); 
                    $status = $user['active'] ? 'true' : 'false';
                    if ($status) $link   = "<a class=\"dropdown-item\" id=\"action\" href=\"#deactivate-user\">Deactivate</a>";
                    else $link   = "<a class=\"dropdown-item\" id=\"action\" href=\"#activate-user\">Active</a>";
                    $row    = [
                        $i,
                        $uname,
                        $email,
                        $phone,
                        ($status ? 'active' : 'inactive'),
                        "<div class=\"dropend\">
                            <div id=\"data\" class=\"d-none\">
                                <input type=\"hidden\" id=\"uuid\" value=\"{$uuid}\" />
                                <input type=\"hidden\" id=\"username\" value=\"{$uname}\" />
                                <input type=\"hidden\" id=\"email\" value=\"{$email}\" />
                                <input type=\"hidden\" id=\"phone\" value=\"{$phone}\" />
                                <input type=\"hidden\" id=\"status\" value=\"{$status}\" />
                            </div>
                            <a class=\"dropdown-toggle\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\">
                                More <i class=\"mdi mdi-right-arrow\"></i>
                            </a>
                            <ul class=\"dropdown-menu\">
                                <li>
                                    <a class=\"dropdown-item\" href=\"#\">Deactivate</a>
                                </li>
                                <li>
                                    <a class=\"dropdown-item\" href=\"#modal-changepassword\" id=\"pswdchange\" data-bs-toggle=\"modal\">Change Password</a>
                                </li>
                                <li class=\"dropdown-divider\"></li>
                                <li>
                                    <a class=\"dropdown-item\" id=\"edit-data\" href=\"#modal-form\" data-bs-toggle=\"modal\">Edit</a>
                                </li>
                            </ul> 
                        </div>",
                    ];
                    $i++;
                    array_push ($theData, $row);
                }
                break;
            case 'programming':
                $i = 1;
                foreach ($payload as $api) {
                    $row    = [
                        "<span class=\"text-center\" data-uuid=\"{$api['uid']}\">{$i}</span>",
                        $api['api_code'],
                        $api['api_name'],
                        $api['api_dscript'],
                        ($api['active'] ? 'active' : 'inactive'),
                        "<div class=\"dropdown dropend\">
                            <a class=\"dropdown-toggle\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\">
                                More <i class=\"mdi mdi-right-arrow\"></i>
                            </a>
                            <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-divider\"></li>
                                <li>
                                    <a class=\"dropdown-item\" href=\"#modal-form\" data-bs-toggle=\"modal\">Edit</a>
                                </li>
                            </ul>
                        </div>"
                    ];
                    $i++;
                    array_push ($theData, $row);
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
        if ($col >= count ($cols)) return FALSE;
        return $cols[$col-1];
    }
}
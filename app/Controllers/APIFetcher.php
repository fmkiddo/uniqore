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
        $searchReg  = $post ['search']['regex'];
        
        $curlOpts   = [
            'auth'      => [
                bin2hex ($this->encrypt ($this->getAuthToken ())),
                '',
                'basic'
            ],
            'headers'   => [
                'Content-Type'  => HEADER_APP_JSON,
                'Accept'        => HEADER_APP_JSON
            ]
        ];
        
        $url = site_url ("api-uniqore/{$fetcher}?payload=find%23{$searchVal}");
        
        $response   = $this->sendRequest($url, $curlOpts);
        $json       = json_get ($response);
        
        if ($json['status'] !== 200) {
            $theData    = [];
        } else {
            $payload    = $json['data']['payload'];
            $payload    = unserialize ($this->decrypt (hex2bin ($payload)));
            $theData    = [];
            $this->dataFormatter($fetcher, $payload, $theData);
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
                    $row    = [
                        "<span class=\"text-center\" data-uuid=\"{$user['uid']}\">{$i}</span>",
                        $user['username'],
                        $user['email'],
                        $user['phone'],
                        "<a href=\"#\" class=\"info-box\">More Info <span class=\"mdi mdi-menu-right\"></span></a>"
                    ];
                    $i++;
                    array_push ($theData, $row);
                }
                break;
        }
    }
}
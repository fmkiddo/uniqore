<?php
namespace App\Controllers\Uniqore;


use App\Controllers\BaseUniqoreAPIController;
use CodeIgniter\HTTP\ResponseInterface;

class ApiUser extends BaseUniqoreAPIController {
    
    protected $modelName    = 'App\Models\Uniqore\ClientModel';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doCreate()
     */
    protected function doCreate(array $json, $userid = 0): array|ResponseInterface {
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doUpdate()
     */
    protected function doUpdate($id, array $json, $userid = 0): array|ResponseInterface {
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::findWithFilter()
     */
    protected function findWithFilter ($get) {
        $payload    = explode ('#', $get['payload']);
        $filter     = $payload[1];
        $sortType   = (!array_key_exists ('typesort', $get)) ? '' : $get['typesort'];
        $sortCol    = (!array_key_exists ('colsort', $get)) ? '' : $get['colsort'];
        
        if (strlen (trim ($filter))) {
            $match   = [
                'ocac.uid'          => $filter,
                'ocac.client_code'  => $filter,
                'cac1.client_name'  => $filter,
                'oapi.api_name'     => $filter,
                'cac1.client_lname' => $filter,
                'oapi.status'       => $filter
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ($sortCol, $sortType);
        
        return $this->model
                ->select ('ocac.uid, ocac.client_code, ocac.client_passcode, ocac.client_keycode, cac1.client_name, ocac.client_apicode, 
                    oapi.api_name, oapi.api_dscript, cac1.client_lname, cac1.address1, cac1.address2, cac1.tax_no, cac1.pic_name, 
                    cac1.pic_mail, cac1.pic_phone, cac2.db_name, cac2.db_user, cac2.db_prefix')
                ->join ('cac1', 'cac1.client_id=ocac.id', 'left')
                ->join ('cac2', 'cac2.client_id=ocac.id', 'left')
                ->join ('oapi', 'oapi.api_code=ocac.client_apicode', 'left')
                ->findAll ();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
        $returnCount    = count ($queryResult);
        $payload        = [];
        
        if (!$returnCount) {
            $encrypted      = $this->encrypt (serialize ($payload));
            if ($encrypted)
                $json           = [
                    'status'        => 200,
                    'error'         => NULL,
                    'messages'      => [
                        'success'       => 'Server returned empty row or data not found!'
                    ],
                    'data'          => [
                        'uuid'          => time (),
                        'timestamp'     => date ('Y-m-d H:i:s'),
                        'payload'       => bin2hex ($encrypted)
                    ]
                ];
            else {
                $json   = [
                    'status'    => 500,
                    'error'     => 500,
                    'messages'  => [
                        'error'     => 'Internal server error has occured!'
                    ]
                ];
                log_message('error', 'Error: Server failed to generate API Response. Cause: Encryption Error!');
                return $this->failServerError ('Cannot generate response data!', 500);
            }
        } else {
            foreach ($queryResult as $data)
                array_push ($payload, [
                    'uid'               => $data->uid,
                    'client_code'       => $data->client_code,
                    'client_data'       => [
                        'passcode'          => $data->client_passcode,
                        'keycode'           => $data->client_keycode,
                        'status'            => $data->status,
                    ],
                    'client_info'       => [
                        'name'              => $data->client_name,
                        'legal_name'        => $data->client_lname,
                        'address1'          => $data->address1,
                        'address2'          => $data->address2,
                        'phone'             => $data->client_phone,
                        'tax_no'            => $data->tax_no,
                        'pic'               => [
                            'name'              => $data->pic_name,
                            'email'             => $data->pic_mail,
                            'phone'             => $data->pic_phone,
                        ]
                    ],
                    'client_config'     => [
                        'dbname'            => $data->db_name,
                        'dbuser'            => $data->db_user,
                        'dbpswd'
                    ],
                    'created_at'        => $data->created_at,
                    'created_by'        => $data->created_by,
                    'updated_at'        => $data->updated_at,
                    'updated_by'        => $data->updated_by
                ]);
                
            $encrypted  = $this->encrypt (serialize ($payload));
            if ($encrypted)
                $json           = [
                    'status'        => 200,
                    'error'         => NULL,
                    'messages'      => [
                        'success'       => 'OK!'
                    ],
                    'data'          => [
                        'uuid'          => time (),
                        'timestamp'     => date ('Y-m-d H:i:s'),
                        'payload'       => bin2hex ($encrypted)
                    ]
                ];
            else {
                $json   = [
                    'status'    => 500,
                    'error'     => 500,
                    'messages'  => [
                        'error'     => 'Internal server error has occured!'
                    ]
                ];
                log_message('error', 'Error: Server failed to generate API Response. Cause: Encryption Error!');
                return $this->failServerError ('Cannot generate response data!', 500);
            }
        }
        return $json;
    }
}
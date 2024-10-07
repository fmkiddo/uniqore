<?php
namespace App\Controllers\Uniqore;


use App\Controllers\BaseUniqoreAPIController;
use CodeIgniter\HTTP\ResponseInterface;

class ApiUserProfile extends BaseUniqoreAPIController {
    
    protected $modelName    = 'App\Models\Uniqore\ClientDetail';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doCreate()
     */
    protected function doCreate (array $json, $userid = 0): array|ResponseInterface {
        /**$payload    = [
            'returnid'  => 1
        ];
        return [
            'status'    => 200,
            'error'     => NULL,
            'messages'  => [
                'success'   => 'API Call success'
            ],
            'data'      => [
                'uuid'      => time (),
                'timestamp' => date ('Y-m-d H:i:s'),
                'payload'   => bin2hex ($this->encrypt ($payload))
            ]
        ];**/
        $insertParams   = [
            'client_id'     => $json['clientid'],
            'client_name'   => $json['clientname'],
            'client_lname'  => $json['clientlname'],
            'address1'      => $json['clientaddr1'],
            'address2'      => $json['clientaddr2'],
            'client_phone'  => $json['clientphone'],
            'tax_no'        => $json['clienttaxn'],
            'pic_name'      => $json['clientpicname'],
            'pic_mail'      => $json['clientpicmail'],
            'pic_phone'     => $json['clientpicphone'],
            'created_by'    => $userid,
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid
        ];
        $this->model->insert ($insertParams);
        $insertID   = $this->model->getInsertID ();
        if (!$insertID)
            $retJSON    = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to register new API client or user'
                ]
            ];
        else {
            $payload    = [
                'returnid'  => $insertID,
            ];
            
            $retJSON    = [
                'status'    => 200,
                'error'     => NULL,
                'messages'  => [
                    'success'   => 'New API client or user successfully registered to system'
                ],
                'data'      => [
                    'uuid'      => time (),
                    'timestamp' => date ('Y-m-d H:i:s'),
                    'payload'   => bin2hex ($this->encrypt (serialize($payload)))
                ]
            ];
        }
        return $retJSON;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doUpdate()
     */
    protected function doUpdate ($id, array $json, $userid = 0): array|ResponseInterface {
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
    }

    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::findWithFilter()
     */
    protected function findWithFilter ($get) {
    }

    
}

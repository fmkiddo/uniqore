<?php
namespace App\Controllers\Uniqore;


use App\Controllers\BaseUniqoreAPIController;
use CodeIgniter\HTTP\ResponseInterface;

class Programming extends BaseUniqoreAPIController {
    
    
    protected $modelName    = 'App\Models\Uniqore\ApiModel';
    
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doCreate()
     */
    protected function doCreate(array $json, $userid = 0): array|ResponseInterface {
        $insertParams   = [
            'uid'           => generate_random_uuid_v4 (),
            'api_code'      => $json['apicode'],
            'api_name'      => $json['apiname'],
            'api_dscript'   => $json['apidscript'],
            'status'        => $json['status'],
            'created_by'    => $userid,
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid
        ];
        
        $this->model->insert ($insertParams);
        $insertID = $this->model->getInsertID ();
        if (!$insertID)
            $retJSON    = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to register new API'
                ]
            ];
        else {
            $payload    = [
                'returnid'  => $insertID
            ];
            $retJSON    = [
                'status'    => 200,
                'error'     => NULL,
                'messages'  => [
                    'success'   => 'New API successfully registered to system'
                ],
                'data'      => [
                    'uuid'      => time (),
                    'timestamp' => date ('Y-m-d H:i:s'),
                    'payload'   => bin2hex ($this->encrypt (serialize($payload)))
                ]
            ];
            return $retJSON;
        }
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
                'uid'           => $filter,
                'api_code'      => $filter,
                'api_name'      => $filter,
                'api_dscript'   => $filter,
                'status'        => $filter
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ($sortCol, $sortType);
        
        return $this->model->findAll ();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
        $apis  = [];
        foreach ($queryResult as $data)
            array_push ($apis, [
                'uid'           => $data->uid,
                'api_code'      => $data->api_code,
                'api_name'      => $data->api_name,
                'api_dscript'   => $data->api_dscript,
                'status'        => $data->status,
                'created_at'    => $data->created_at,
                'created_by'    => $data->created_by,
                'updated_at'    => $data->updated_at,
                'updated_by'    => $data->updated_by
            ]);
            
        $rowsData   = count ($apis);
        if ($rowsData === 0) {
            $json   = [
                'status'    => 404,
                'error'     => 404,
                'messages'  => [
                    'error'     => 'Server returned empty row or data not found!'
                ]
            ];
        } else {
            $serializedData = serialize ($apis);
            $encrypted      = $this->encrypt ($serializedData);
            if (! $encrypted) {
                $json   = [
                    'status'    => 500,
                    'error'     => 500,
                    'messages'  => [
                        'error'     => 'Internal server error has occured!'
                    ]
                ];
                log_message('error', 'Error: Server failed to generate API Response. Cause: Encryption Error!');
                return $this->failServerError ('Cannot generate response data!', 500);
            } else {
                $hexed  = bin2hex ($encrypted);
                $json   = [
                    'status'    => 200,
                    'error'     => NULL,
                    'messages'  => [
                        'success'   => 'OK!',
                    ],
                    'data'      => [
                        'uuid'      => time (),
                        'timestamp' => date ('Y-m-d H:i:s'),
                        'payload'   => $hexed
                    ]
                ];
            }
        }
        return $json;
    }
}
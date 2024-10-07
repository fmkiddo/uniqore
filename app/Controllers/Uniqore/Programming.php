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
        $uuid           = generate_random_uuid_v4 ();
        $insertParams   = [
            'uid'           => $uuid,
            'api_code'      => $json['apicode'],
            'api_name'      => $json['apiname'],
            'api_dscript'   => $json['apidscript'],
            'api_prefix'    => $json['apiprefix'],
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
                'returnid'  => $insertID,
                'uuid'      => $uuid
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
        }
        return $retJSON;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doUpdate()
     */
    protected function doUpdate($id, array $json, $userid = 0): array|ResponseInterface {
        $updateParams   = [
            'api_name'      => $json['apiname'],
            'api_dscript'   => $json['apidscript'],
            'api_prefix'    => $json['apiprefix'],
            'status'        => boolval ($json['status']),
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid
        ];
        $this->model->set ($updateParams)
                ->where ('uid', $id)
                ->update ();
        
        $affectedRows   = $this->model->affectedRows ();
        $payload        = [
            'affectedrows'  => $affectedRows
        ];
        return [
            'status'    => 200,
            'error'     => NULL,
            'messages'  => [
                'success'   => 'OK!'
            ],
            'data'      => [
                'uuid'      => time (),
                'timestamp' => date ('Y-m-d H:i:s'),
                'payload'   => bin2hex ($this->encrypt (serialize ($payload)))
            ]
        ];
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
                'api_prefix'    => $filter,
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
                    'uid'           => $data->uid,
                    'api_code'      => $data->api_code,
                    'api_name'      => $data->api_name,
                    'api_dscript'   => $data->api_dscript,
                    'api_prefix'    => $data->api_prefix,
                    'status'        => $data->status,
                    'created_at'    => $data->created_at,
                    'created_by'    => $data->created_by,
                    'updated_at'    => $data->updated_at,
                    'updated_by'    => $data->updated_by
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
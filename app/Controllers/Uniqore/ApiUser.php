<?php
namespace App\Controllers\Uniqore;


use App\Controllers\BaseUniqoreAPIController;
use CodeIgniter\HTTP\ResponseInterface;

class ApiUser extends BaseUniqoreAPIController {
    
    protected $modelName    = 'App\Models\Uniqore\ClientModel';
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::findWithFilter()
     */
    protected function findWithFilter($get) {
        $payload    = explode ('#', $get['payload']);
        $filter     = $payload[1];
        $sortType   = (!array_key_exists ('typesort', $get)) ? '' : $get['typesort'];
        $sortCol    = (!array_key_exists ('colsort', $get)) ? '' : $get['colsort'];
        
        if (strlen (trim ($filter))) {
            $match   = [
                'uid'               => $filter,
                'client_code'       => $filter,
                'client_name'       => $filter,
                'client_apicode'    => $filter,
                'status'            => $filter
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
    protected function responseFormatter($queryResult): array {
        $clients  = [];
        foreach ($queryResult as $data)
            array_push ($clients, [
                'uid'               => $data->uid,
                'client_code'       => $data->api_code,
                'client_name'       => $data->api_name,
                'client_apicode'    => $data->api_dscript,
                'status'            => $data->status,
                'created_at'        => $data->created_at,
                'created_by'        => $data->created_by,
                'updated_at'        => $data->updated_at,
                'updated_by'        => $data->updated_by
            ]);
            
        $rowsData   = count ($clients);
        if ($rowsData === 0) {
            $json   = [
                'status'    => 404,
                'error'     => 404,
                'messages'  => [
                    'error'     => 'Server returned empty row or data not found!'
                ]
            ];
        } else {
            $serializedData = serialize ($clients);
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
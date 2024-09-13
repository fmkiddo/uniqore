<?php
namespace App\Controllers;


use CodeIgniter\Files\File;

abstract class BaseUniqoreAPIController extends BaseRESTfulController {
    
    
    private function readAuthFile (): array {
        $randAuthFile   = new File (SYS__UNIQORE_RANDAUTH_PATH);
        $contents       = file_get_contents ($randAuthFile->getPathname());
        return explode('.', $contents);
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseRESTfulController::validateRequestAuthorization()
     */
    protected function validateRequestAuthorization(): bool {
        return FALSE;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::create()
     */
    public function create () {
        // TODO Auto-generated method stub
        return parent::create ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::index()
     */
    public function index () {
        // TODO Auto-generated method stub
        return parent::index ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::new()
     */
    public function new () {
        // TODO Auto-generated method stub
        return parent::new ();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::edit()
     */
    public function edit ($id = null) {
        // TODO Auto-generated method stub
        return parent::edit ();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::update()
     */
    public function update($id = null) {
        // TODO Auto-generated method stub
        return parent::update ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::delete()
     */
    public function delete($id = null) {
        // TODO Auto-generated method stub
        return parent::delete ();
    }
    
    /**
     * {@inheritDoc}
     * @see \CodeIgniter\RESTful\ResourceController::show()
     */
    public function show($id = null) {
        // TODO Auto-generated method stub
        return parent::show ();
    }
}
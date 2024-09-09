<?php
namespace App\Controllers;


use App\Libraries\AssetType;

class APIHome extends BaseController {
    
    
    private function isDBUniqoreExisted (): bool {
        $rootDB     = \Config\Database::connect (SYS__DATABASE_ROOTC);
        $databases  = $rootDB->query ("SHOW DATABASES;")->getResult ();
        foreach ($databases as $database)
            if ($database->Database === SYS__DATABASE_NAME) return TRUE;
        return FALSE;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::initComponents()
     */
    protected function initComponents() {
        // TODO Auto-generated method stub
        $this->helpers = [
            'url',
            'filesystem'
        ];
        $styles     = [
            'assets/vendors/bootstrap-5.3.3/css/bootstrap.min.css',
            'assets/vendors/fontawesome-6.6.0/css/all.min.css',
            'assets/css/uniqore.css'
        ];
        $this->initAssets(AssetType::STYLE, $styles);
        $scripts    = [
            'assets/vendors/bootstrap-5.3.3/js/bootstrap.min.js',
            'assets/vendors/fontawesome-6.6.0/js/all.min.js'
        ];
        $this->initAssets(AssetType::SCRIPT, $scripts);
        parent::initComponents();
    }
    
    public function index (): string {
        if (!$this->isDBUniqoreExisted()) $this->response->redirect (base_url ('uniqore/forge/0'), 'get');
        
        $viewPaths = [
            'template_html',
            'template_header',
            'uniqore/login',
            'template_footer'
        ];
        $pageData = [
            'csrf_name'     => csrf_token (),
            'csrf_value'    => csrf_hash ()
        ];
        
        return $this->renderView ($viewPaths, $pageData);
    }
}
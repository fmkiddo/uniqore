<?php
namespace App\Controllers;


use App\Libraries\AssetType;

class APIDashboard extends BaseUniqoreController {
    
    private function getUserInfo () {
        
    }
    
    private function doSignOut (): string {
        $this->session->destroy ();
        $this->response->redirect (base_url ('uniqore/admin'));
        return "OK!";
    }
    
    private function showWelcome (): string {
        return '';
    }
    
    private function showAdministrators (): string {
        return '';
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::__initComponents()
     */
    protected function __initComponents() {
        $this->helpers = [
            'url',
            'key_generator',
        ];
        $styles     = [
            'assets/vendors/bootstrap-5.3.3/css/bootstrap.min.css',
            'assets/vendors/fontawesome-6.6.0/css/all.min.css',
            'assets/vendors/materialdesignicons-7.4.47/css/materialdesignicons.min.css',
            'assets/css/uniqore.css'
        ];
        $this->initAssets(AssetType::STYLE, $styles);
        $scripts    = [
            'assets/vendors/bootstrap-5.3.3/js/bootstrap.min.js',
            'assets/vendors/fontawesome-6.6.0/js/all.min.js',
            'assets/vendors/materialdesignicons-7.4.47/scripts/verify.js',
        ];
        $this->initAssets(AssetType::SCRIPT, $scripts);
        parent::__initComponents ();
    }
    
    
    public function index (): string {
        if (!$this->session->get ('logintime')) $this->response->redirect (base_url ('uniqore/admin'));
        $get = $this->request->getGet ();
        
        if (count ($get) > 0 && array_key_exists('route', $get)) $route = $get['route'];
        else $route = 'welcome';
        
        $render = TRUE;
        $contentPage    = '';
        
        $retVal = '';
        
        switch ($route) {
            default:
                $contentPage    = 'uniqore/welcome';
                break;
            case 'sign-out':
                $render         = FALSE;
                $retVal         = $this->doSignOut ();
                break;
            case 'apiadmin':
                $contentPage    = 'uniqore/users';
                break;
        }
        
        if ($render) {
            $viewPaths  = [
                'template_html',
                'uniqore/tpl_dashboard_header',
                $contentPage,
                'uniqore/tpl_dashboard_footer',
                'template_footer'
            ];
            $pageData   = [
                'base_url'      => base_url (),
                'dashboard_url' => base_url ('uniqore/admin/dashboard'),
                'year'          => date ('Y'),
            ];
            $retVal     = $this->renderView($viewPaths, $pageData);
        }
        return $retVal;
    }
}
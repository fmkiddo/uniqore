<?php
namespace App\Libraries;


class PageViews {
    
    private $routers  = [
        'welcome'       => [
            'fetch'         => 'summaries',
            'viewPaths'     => [
                'template_html',
                'uniqore/tpl_dashboard_header',
                'uniqore/welcome',
                'uniqore/tpl_dashboard_footer',
                'template_footer',
            ]
        ],
        'api'           => [
            'fetch'         => 'programming',
            'viewPaths'     => [
                'template_html',
                'uniqore/tpl_dashboard_header',
                'uniqore/tpl_dashboard_footer',
                'template_footer',
            ]
        ],
        'clients'           => [
            'fetch'         => 'apiuser',
            'viewPaths'     => [
                'template_html',
                'uniqore/tpl_dashboard_header',
                'uniqore/tpl_dashboard_footer',
                'template_footer',
            ]
        ],
        'apiadmin'      => [
            'fetch'         => 'users',
            'viewPaths'     => [
                'template_html',
                'uniqore/tpl_dashboard_header',
                'uniqore/users',
                'uniqore/forms/form_users',
                'uniqore/tpl_dashboard_footer',
                'template_footer',
            ]
        ],
        'documentation'           => [
            'fetch'         => 'docs',
            'viewPaths'     => [
                'template_html',
                'uniqore/tpl_dashboard_header',
                'uniqore/tpl_dashboard_footer',
                'template_footer',
            ]
        ],
    ];
    
    public function fetchPage ($routes, &$fetch, &$viewPaths): bool {
        if (! array_key_exists ($routes, $this->routers)) throw new \Exception ("PageView routes not found!");
        else {
            $fetch      = $this->routers[$routes]['fetch'];
            $viewPaths  = $this->routers[$routes]['viewPaths'];
            return TRUE;
        }
    }
}
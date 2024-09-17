<?php
namespace App\Libraries\Forgery\Templates;


use App\Libraries\Forgery\Database;
use App\Libraries\Forgery\Templates\Uniqore\OUSR;
use App\Libraries\Forgery\Templates\Uniqore\OAPI;
use App\Libraries\Forgery\Templates\Uniqore\USR1;
use App\Libraries\Forgery\Templates\Uniqore\OCAC;
use App\Libraries\Forgery\Templates\Uniqore\CAC1;
use App\Libraries\Forgery\Templates\Uniqore\CAC2;
use App\Libraries\Forgery\Templates\Uniqore\OPRT;


class UniqoreDatabaseTemplate extends Database {
    
    protected function __initDatabaseTemplate () {
        $this->dbprefix = 'fmk_';
        $this->tables   = [
            new OUSR (),
            new USR1 (),
            new OAPI (),
            new OCAC (),
            new CAC1 (),
            new CAC2 (),
            new OPRT (FALSE),
        ];
    }
}
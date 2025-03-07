<?php
namespace App\Libraries\Forgery\Templates;


use App\Libraries\Forgery\Database;
use App\Libraries\Forgery\Templates\Osam\OUGR;
use App\Libraries\Forgery\Templates\Osam\OUSR;
use App\Libraries\Forgery\Templates\Osam\USR1;
use App\Libraries\Forgery\Templates\Osam\USR2;
use App\Libraries\Forgery\Templates\Osam\USR3;
use App\Libraries\Forgery\Templates\Osam\OALG;
use App\Libraries\Forgery\Templates\Osam\OLCT;
use App\Libraries\Forgery\Templates\Osam\OSBL;
use App\Libraries\Forgery\Templates\Osam\ITA1;
use App\Libraries\Forgery\Templates\Osam\ITA2;
use App\Libraries\Forgery\Templates\Osam\OITA;
use App\Libraries\Forgery\Templates\Osam\OACI;
use App\Libraries\Forgery\Templates\Osam\OAST;
use App\Libraries\Forgery\Templates\Osam\OCTA;
use App\Libraries\Forgery\Templates\Osam\OARV;
use App\Libraries\Forgery\Templates\Osam\ACI1;
use App\Libraries\Forgery\Templates\Osam\ARV1;
use App\Libraries\Forgery\Templates\Osam\CTA1;
use App\Libraries\Forgery\Templates\Osam\OMVI;
use App\Libraries\Forgery\Templates\Osam\OMVO;
use App\Libraries\Forgery\Templates\Osam\OMVR;
use App\Libraries\Forgery\Templates\Osam\OCFG;
use App\Libraries\Forgery\Templates\Osam\MVI1;
use App\Libraries\Forgery\Templates\Osam\MVO1;
use App\Libraries\Forgery\Templates\Osam\UGR1;
use App\Libraries\Forgery\Templates\Osam\OFPT;
use App\Libraries\Forgery\Templates\Osam\ORQS;
use App\Libraries\Forgery\Templates\Osam\OFAP;
use App\Libraries\Forgery\Templates\Osam\FAP1;
use App\Libraries\Forgery\Templates\Osam\FAP2;

class OsamDatabaseTemplate extends Database {
    
    protected function __initDatabaseTemplate () {
        $this->tables   = [
            new ACI1 (),
            new ARV1 (),
            new CTA1 (),
            new FAP1 (),
            new FAP2 (),
            new ITA1 (),
            new ITA2 (),
            new MVI1 (),
            new MVO1 (),
            new OACI (),
            new OALG (FALSE),
            new OARV (),
            new OAST (),
            new OCFG (),
            new OCTA (),
            new OFAP (),
            new OFPT (),
            new OITA (),
            new OLCT (),
            new OMVI (),
            new OMVO (),
            new OMVR (),
            new ORQS (),
            new OSBL (),
            new OUGR (),
            new OUSR (),
            new UGR1 (),
            new USR1 (),
            new USR2 (),
            new USR3 ()
        ];
    }
}
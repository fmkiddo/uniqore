<?php
namespace App\Controllers\Osam;


abstract class BaseAssetMutation extends OsamBaseResourceController {
    
    protected $codeString   = 'CCC';
    protected $codeNumber   = 'CC';
    protected $codeSingle   = 'C';
    
    protected function getLocationID ($locationUUID) {
        $builder    = $this->model->builder ('olct');
        $result     = $builder->select ('*')->where ('uuid', $locationUUID)->get ()->getResult ();
        if (!count ($result)) return FALSE;
        return $result[0]->id;
    }
    
    protected function getConfiguration ($tagname='') {
        $builder    = $this->model->builder ('ocfg');
        $builder->select ('*');
        if (!($tagname === '')) $builder->where ('tag_name', $tagname);
        return $builder->get ()->getResult ();
    }
    
    protected function getNumberingFormat () {
        $config = $this->getConfiguration ('numbering-format');
        if (!count ($config)) return '{code}{separator}{year}{month}{separator}{serial}';
        return $config[0]->tag_value;
    }
    
    protected function getNumberingFormatCode () {
        $config = $this->getConfiguration ('numbering-format-code');
        if (!count ($config)) return 'CCC';
        return $config[0]->tag_value;
    }
    
    protected function getNumberingFormatYear () {
        $config = $this->getConfiguration ('numbering-format-year');
        if (!count ($config)) return 'YYYY';
        return $config[0]->tag_value;
    }
    
    protected function getNumberingFormatMonth () {
        $config = $this->getConfiguration ('numbering-format-month');
        if (!count ($config)) return 'MM';
        return $config[0]->tag_value;
    }
    
    protected function getNumberingFormatSerial () {
        $config = $this->getConfiguration ('numbering-format-serial');
        if (!count ($config)) return 'XXXXXX';
        return $config[0]->tag_value;
    }
    
    protected function getNumberingFormatSeparator () {
        $config = $this->getConfiguration ('numbering-separator');
        if (!count ($config)) return '-';
        return $config[0]->tag_value;
    }
    
    protected function getNumberingPeriode () {
        $config = $this->getConfiguration ('periode');
        if (!count ($config)) return 'yearly';
        return $config[0]->tag_value;
    }
    
    protected function getNumberingCodeString () {
        $config = $this->getConfiguration ('transfer-out-string');
        if (!count ($config)) return $this->codeString;
        return $config[0]->tag_value;
    }
    
    protected function getNumberingCodeNumber () {
        $config = $this->getConfiguration ('transfer-out-num');
        if (!count ($config)) return $this->codeNumber;
        return $config[0]->tag_value;
    }
    
    protected function getNumberingCodeSingle () {
        $config = $this->getConfiguration ('transfer-out-single');
        if (!count ($config)) return $this->codeSingle;
        return $config[0]->tag_value;
    }
    
    /**
     * 
     * @param integer|string $lastID
     * @return string
     */
    protected function generateDocumentNumber ($lastID=1) {
        $docNumFormat   = $this->getNumberingFormat ();
        $separator      = $this->getNumberingFormatSeparator ();
        $codeFormat     = $this->getNumberingFormatCode ();
        $yearFormat     = $this->getNumberingFormatYear ();
        $monthFormat    = $this->getNumberingFormatMonth ();
        $serialFormat   = $this->getNumberingFormatSerial ();
        
        $cCount         = substr_count ($codeFormat, 'C');
        $yCount         = substr_count ($yearFormat, 'Y');
        $mCount         = substr_count ($monthFormat, 'M');
        $xCount         = substr_count ($serialFormat, 'X');
        
        if (is_int ($lastID)) $nextID = 1;
        else {
            $tformat        = str_replace ('{separator}', $separator, $docNumFormat);
            $formatExplode  = explode ($separator, $tformat);
            $lastExplode    = explode ($separator, $lastID);
            if (count ($lastExplode) != count ($formatExplode)) $nextID = 1;
            else $nextID = intval ($lastExplode[2]) + 1;
        }
        
        $nextLength = strlen ($nextID);
        
        $code   = '';
        if ($cCount > 0)
            switch ($cCount) {
                default:
                    $code   = $this->getNumberingCodeString ();
                    break;
                case 1:
                    $code   = $this->getNumberingCodeSingle ();
                    break;
                case 2:
                    $code   = $this->getNumberingCodeNumber ();
                    break;
            }
        
        $year   = '';
        if ($yCount > 0) $year  = ($yCount < 4) ? date ('y') : date ('Y');
        
        $month  = '';
        if ($mCount > 0) {
            $month  = date ('m');
            
            $prefix = '';
            if ($mCount > 2)
                for ($i=0; $i<$mCount-1; $i++) $prefix .= '0';
                
            $month  = $prefix . $month;
        }
        
        $serial = '';
        if ($xCount > 0) {;
        for ($i=0; $i<($xCount-$nextLength); $i++) $serial .= '0';
            $serial .= $nextID;
        }
        
        $docNumFormat   = str_replace ('{code}', $code, $docNumFormat);
        $docNumFormat   = str_replace ('{year}', $year, $docNumFormat);
        $docNumFormat   = str_replace ('{month}', $month, $docNumFormat);
        $docNumFormat   = str_replace ('{serial}', $serial, $docNumFormat);
        $docNumFormat   = str_replace ('{separator}', $separator, $docNumFormat);
        
        return $docNumFormat;
    }
}
<?php

namespace Sugarcrm\ProfessorM;

class PackageGenerator
{

    public function shouldIncludeFileInZip($fileRelative)
    {
        if(preg_match('/.*custom[\/\\\]{1,1}application[\/\\\]{1,1}Ext[\/\\\]{1,1}.*/', $fileRelative) or
            //preg_match('/.*custom\/modules\/.+\/Ext\/.*', $fileRelative)){
            preg_match('/.*custom[\/\\\]{1,1}modules[\/\\\]{1,1}.+[\/\\\]{1,1}Ext[\/\\\]{1,1}.*/', $fileRelative)){
            return false;
        }
        return true;
    }
}
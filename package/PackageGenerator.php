<?php

namespace Sugarcrm\ProfessorM;

use function getcwd;

class PackageGenerator
{
    protected $cwd;

    public function __construct(){
        $this -> cwd = getcwd();
    }

    /*
     * $cwd defaults to the current working directory so you should only need to use this function if you are testing
     */
    public function setCwd($pathOfWorkingDirectory){
        $this -> cwd = $pathOfWorkingDirectory;
    }

    public function shouldIncludeFileInZip($fileRelative)
    {
        /*
         * We want to exclude files in the following directories:
         *    custom/application/Ext
         *    custom/modules/.../Ext
         * The regular expressions allow for file paths with forward or backward slashes */

        if(preg_match('/.*custom[\/\\\]application[\/\\\]Ext[\/\\\].*/', $fileRelative) or
            preg_match('/.*custom[\/\\\]modules[\/\\\].+[\/\\\]Ext[\/\\\].*/', $fileRelative)){
            return false;
        }
        return true;
    }

    /*
     * Get the version that should be used for the zip.  If a version
     * is not passed as a param, the function checks for a file named
     * "version" and gets the version out of the file.
     */
    public function getVersion($versionPassedToScript){
        if (empty($versionPassedToScript)) {
            $pathToVersionFile = $this -> cwd . "/version";
            if (file_exists($pathToVersionFile)) {
                return file_get_contents($pathToVersionFile);
            }
        }
        return $versionPassedToScript;
    }

    /*
     * Returns the relative file path for the zip file that will be created.
     * @throws \Exception if $version is empty.
     * Will make a releases directory if one does not already exists.
     */
    public function getZipFilePath($version, $packageID, $command){
        if (empty($version)){
            throw new \Exception("Use $command [version]\n");
        }

        $id = "{$packageID}-{$version}";

        $directory = "releases";
        if(!is_dir($this -> cwd . "/" . $directory)){
            mkdir($this -> cwd . "/" . $directory);
        }

        $zipFile = $directory . "/sugarcrm-{$id}.zip";
        return $zipFile;
    }
}

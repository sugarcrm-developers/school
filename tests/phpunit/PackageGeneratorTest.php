<?php
// Copyright 2018 SugarCRM Inc.  Licensed by SugarCRM under the Apache 2.0 license.
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Sugarcrm\ProfessorM\PackageGenerator;
use org\bovigo\vfs\vfsStream;

/**
 * Class PackageGeneratorTest
 */
class PackageGeneratorTest extends TestCase
{

    protected function tearDown()
    {
        //delete the releases directory if it exists
        if(is_dir('releases')){
            array_map('unlink', glob ('releases/*.*'));
            rmdir ('releases');
        }

    }

    /*
     * Creates the virtual file system and associated arrays (filesToInclude and filesToExclude)
     * for multiple files.
     *
     * Files to be included:
     *  [*] src/language/application/en_us.lang.php
     *  [*] src/icons/default/images/PR_Professors.gif
     *  [*] src/icons/default/images/CreatePR_Professors.gif
     *
     * Files to be excluded:
     * [*] src/custom/application/Ext/test.php
     * [*] src/custom/modules/test/Ext/excludeme.php
     */
    private function getTestVariablesForMultipleFiles(){

        $root = vfsStream::setup();
        $srcDirectory = vfsStream::newDirectory("src") -> at($root);
        $languageDirectory = vfsStream::newDirectory("language") -> at($srcDirectory);
        $applicationUnderLanguageDirectory = vfsStream::newDirectory("application") -> at($languageDirectory);
        $iconsDirectory = vfsStream::newDirectory("icons") -> at($srcDirectory);
        $defaultDirectory = vfsStream::newDirectory("default") -> at($iconsDirectory);
        $imagesDirectory = vfsStream::newDirectory("images") -> at($defaultDirectory);
        $customDirectory = vfsStream::newDirectory("custom") -> at($srcDirectory);
        $applicationDirectory = vfsStream::newDirectory("application") -> at($customDirectory);
        $ExtDirectory = vfsStream::newDirectory("Ext") -> at($applicationDirectory);
        $modulesDirectory = vfsStream::newDirectory("modules") -> at($customDirectory);
        $testDirectory = vfsStream::newDirectory("test") -> at($modulesDirectory);
        $ExtUnderTestDirectory = vfsStream::newDirectory("Ext") -> at($testDirectory);

        vfsStream::newFile("en_us.lang.php") -> at($applicationUnderLanguageDirectory);
        vfsStream::newFile("PR_Professors.gif") -> at($imagesDirectory);
        vfsStream::newFile("CreatePR_Professors.gif") -> at($imagesDirectory);
        vfsStream::newFile("test.php") -> at($ExtDirectory);
        vfsStream::newFile("excludeme.php") -> at($ExtUnderTestDirectory);

        $filesToInclude = array();
        $fileEnUs = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "language" . DIRECTORY_SEPARATOR . "application"
                . DIRECTORY_SEPARATOR . "en_us.lang.php",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "language"
                . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "en_us.lang.php"
        );
        $filePRProfessors = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default"
                . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "PR_Professors.gif",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "icons"
                . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "PR_Professors.gif"
        );
        $fileCreatePRProfessors = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default"
                . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "CreatePR_Professors.gif",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "icons"
                . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "CreatePR_Professors.gif"
        );
        array_push($filesToInclude, $fileEnUs);
        array_push($filesToInclude, $filePRProfessors);
        array_push($filesToInclude, $fileCreatePRProfessors);

        $filesToExclude = array();
        $fileTest = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "application"
                . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "test.php",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "custom"
                . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "test.php"
        );
        $fileExcludeme = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "modules"
                . DIRECTORY_SEPARATOR . "test" . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "excludeme.php",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "custom"
                . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "test" . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "excludeme.php"
        );
        array_push($filesToExclude, $fileTest);
        array_push($filesToExclude, $fileExcludeme);

        return array(
            'root' => $root,
            'filesToInclude' => $filesToInclude,
            'filesToExclude' => $filesToExclude
        );
    }

    /*
     * Creates the virtual file system and associated arrays (filesToInclude and filesToExclude)
     * for a single file that should be included in the zip.
     *
     * Files to be included:
     *  [*] src/myfile.php
     *
     * Files to be excluded:
     * [none]
     */
    private function getTestVariablesForSingleFileToInclude(){
        $root = vfsStream::setup();
        $srcDirectory = vfsStream::newDirectory("src") -> at($root);
        vfsStream::newFile("myfile.php") -> at($srcDirectory);

        $filesToInclude = array();
        $file = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "myfile.php",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "myfile.php"
        );
        array_push($filesToInclude, $file);

        return array(
            'root' => $root,
            'filesToInclude' => $filesToInclude,
            'filesToExclude' => array()
        );
    }

    /*
     * Creates the virtual file system and associated arrays (filesToInclude and filesToExclude)
     * for a single file that should NOT be included in the zip.
     *
     * Files to be included:
     *  [none]
     *
     * Files to be excluded:
     * [*] src/custom/application/Ext/test.php
     */
    private function getTestVariablesForSingleFileToExclude(){
        $root = vfsStream::setup();
        $srcDirectory = vfsStream::newDirectory("src") -> at($root);
        $customDirectory = vfsStream::newDirectory("custom") -> at($srcDirectory);
        $applicationDirectory = vfsStream::newDirectory("application") -> at($customDirectory);
        $ExtDirectory = vfsStream::newDirectory("Ext") -> at($applicationDirectory);
        vfsStream::newFile("test.php") -> at($ExtDirectory);


        $filesToExclude = array();
        $fileTest = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "application"
                . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "test.php",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "custom"
                . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "test.php"
        );
        array_push($filesToExclude, $fileTest);

        return array(
            'root' => $root,
            'filesToInclude' => array(),
            'filesToExclude' => $filesToExclude
        );
    }

    /*
     * Creates the virtual file system and associated arrays (filesToInclude, filesToExclude, filesToExcludeWindows)
     * for a single file that should NOT be included in the standard zip but should BE included in the Windows Zip
     * when the length of the Windows path is 38 or larger
     *
     * Files to be included:
     *  [none]
     *
     * Files to be excluded:
     *  [none]
     *
     * Files to be excluded on Windows:
     * [*] /src/modules/Opportunities/clients/base/views/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php
     */
    private function getTestVariablesForSingleFileToExcludeOnWindows(){
        $root = vfsStream::setup();
        $srcDirectory = vfsStream::newDirectory("src") -> at($root);
        $modulesDirectory = vfsStream::newDirectory("modules") -> at($srcDirectory);
        $opportunitiesDirectory = vfsStream::newDirectory("Opportunities") -> at($modulesDirectory);
        $clientsDirectory = vfsStream::newDirectory("clients") -> at($opportunitiesDirectory);
        $baseDirectory = vfsStream::newDirectory("base") -> at($clientsDirectory);
        $viewsDirectory = vfsStream::newDirectory("views") -> at($baseDirectory);
        $subpanelDirectory = vfsStream::newDirectory("subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link") -> at($viewsDirectory);
        vfsStream::newFile("subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php") -> at($subpanelDirectory);

        $filesToExcludeWindows = array();
        $fileSubpanel = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "Opportunities"
                . DIRECTORY_SEPARATOR . "clients" . DIRECTORY_SEPARATOR . "base" . DIRECTORY_SEPARATOR
                . "views" . DIRECTORY_SEPARATOR . "subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link"
                . DIRECTORY_SEPARATOR . "subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "Opportunities"
                . DIRECTORY_SEPARATOR . "clients" . DIRECTORY_SEPARATOR . "base" . DIRECTORY_SEPARATOR
                . "views" . DIRECTORY_SEPARATOR . "subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link"
                . DIRECTORY_SEPARATOR . "subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php",
        );
        array_push($filesToExcludeWindows, $fileSubpanel);

        return array(
            'root' => $root,
            'filesToInclude' => array(),
            'filesToExclude' => array(),
            'filesToExcludeWindows' => $filesToExcludeWindows
        );
    }

    /*
     * Creates the virtual file system and associated arrays (filesToInclude, filesToExclude, filesToExcludeWindows)
     * for a single file that should NOT be included in the standard zip but should BE included in the Windows Zip
     * when the length of the Windows path is 38 or larger
     *
     * Files to be included:
     *  [*] src/language/application/en_us.lang.php
     *  [*] src/icons/default/images/PR_Professors.gif
     *  [*] src/icons/default/images/CreatePR_Professors.gif
     *
     * Files to be excluded:
     * [*] src/custom/application/Ext/test.php
     * [*] src/custom/modules/test/Ext/excludeme.php
     *
     * Files to be excluded on Windows:
     * [*] /src/modules/Opportunities/clients/base/views/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php
     * [*] /src/modules/Opportunities/clients/base/views/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link/another-really-super-duper-crazy-long-filename-that-windows-simply-cannot-handle.php
     */
    private function getTestVariablesForMultipleFileToExcludeOnWindows(){
        $root = vfsStream::setup();
        $srcDirectory = vfsStream::newDirectory("src") -> at($root);
        $languageDirectory = vfsStream::newDirectory("language") -> at($srcDirectory);
        $applicationUnderLanguageDirectory = vfsStream::newDirectory("application") -> at($languageDirectory);
        $iconsDirectory = vfsStream::newDirectory("icons") -> at($srcDirectory);
        $defaultDirectory = vfsStream::newDirectory("default") -> at($iconsDirectory);
        $imagesDirectory = vfsStream::newDirectory("images") -> at($defaultDirectory);
        $customDirectory = vfsStream::newDirectory("custom") -> at($srcDirectory);
        $applicationDirectory = vfsStream::newDirectory("application") -> at($customDirectory);
        $ExtDirectory = vfsStream::newDirectory("Ext") -> at($applicationDirectory);
        $modulesDirectory = vfsStream::newDirectory("modules") -> at($customDirectory);
        $testDirectory = vfsStream::newDirectory("test") -> at($modulesDirectory);
        $ExtUnderTestDirectory = vfsStream::newDirectory("Ext") -> at($testDirectory);
        $modulesDirectory = vfsStream::newDirectory("modules") -> at($srcDirectory);
        $opportunitiesDirectory = vfsStream::newDirectory("Opportunities") -> at($modulesDirectory);
        $clientsDirectory = vfsStream::newDirectory("clients") -> at($opportunitiesDirectory);
        $baseDirectory = vfsStream::newDirectory("base") -> at($clientsDirectory);
        $viewsDirectory = vfsStream::newDirectory("views") -> at($baseDirectory);
        $subpanelDirectory = vfsStream::newDirectory("subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link") -> at($viewsDirectory);

        vfsStream::newFile("en_us.lang.php") -> at($applicationUnderLanguageDirectory);
        vfsStream::newFile("PR_Professors.gif") -> at($imagesDirectory);
        vfsStream::newFile("CreatePR_Professors.gif") -> at($imagesDirectory);
        vfsStream::newFile("test.php") -> at($ExtDirectory);
        vfsStream::newFile("excludeme.php") -> at($ExtUnderTestDirectory);
        vfsStream::newFile("subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php") -> at($subpanelDirectory);
        vfsStream::newFile("another-really-super-duper-crazy-long-filename-that-windows-simply-cannot-handle.php") -> at($subpanelDirectory);

        $filesToInclude = array();
        $fileEnUs = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "language" . DIRECTORY_SEPARATOR . "application"
                . DIRECTORY_SEPARATOR . "en_us.lang.php",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "language"
                . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "en_us.lang.php"
        );
        $filePRProfessors = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default"
                . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "PR_Professors.gif",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "icons"
                . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "PR_Professors.gif"
        );
        $fileCreatePRProfessors = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default"
                . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "CreatePR_Professors.gif",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "icons"
                . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "CreatePR_Professors.gif"
        );
        array_push($filesToInclude, $fileEnUs);
        array_push($filesToInclude, $filePRProfessors);
        array_push($filesToInclude, $fileCreatePRProfessors);

        $filesToExclude = array();
        $fileTest = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "application"
                . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "test.php",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "custom"
                . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "test.php"
        );
        $fileExcludeme = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "modules"
                . DIRECTORY_SEPARATOR . "test" . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "excludeme.php",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "custom"
                . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "test" . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "excludeme.php"
        );
        array_push($filesToExclude, $fileTest);
        array_push($filesToExclude, $fileExcludeme);

        $filesToExcludeWindows = array();
        $fileSubpanel = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "Opportunities"
                . DIRECTORY_SEPARATOR . "clients" . DIRECTORY_SEPARATOR . "base" . DIRECTORY_SEPARATOR
                . "views" . DIRECTORY_SEPARATOR . "subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link"
                . DIRECTORY_SEPARATOR . "subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "Opportunities"
                . DIRECTORY_SEPARATOR . "clients" . DIRECTORY_SEPARATOR . "base" . DIRECTORY_SEPARATOR
                . "views" . DIRECTORY_SEPARATOR . "subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link"
                . DIRECTORY_SEPARATOR . "subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php",
        );
        $fileAnother = array(
            "fileRelative" => "src" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "Opportunities"
                . DIRECTORY_SEPARATOR . "clients" . DIRECTORY_SEPARATOR . "base" . DIRECTORY_SEPARATOR
                . "views" . DIRECTORY_SEPARATOR . "subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link"
                . DIRECTORY_SEPARATOR . "subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php",
            "fileReal" =>  "vfs://root" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "Opportunities"
                . DIRECTORY_SEPARATOR . "clients" . DIRECTORY_SEPARATOR . "base" . DIRECTORY_SEPARATOR
                . "views" . DIRECTORY_SEPARATOR . "subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link"
                . DIRECTORY_SEPARATOR . "another-really-super-duper-crazy-long-filename-that-windows-simply-cannot-handle.php",
        );
        array_push($filesToExcludeWindows, $fileSubpanel);
        array_push($filesToExcludeWindows, $fileAnother);

        return array(
            'root' => $root,
            'filesToInclude' => $filesToInclude,
            'filesToExclude' => $filesToExclude,
            'filesToExcludeWindows' => $filesToExcludeWindows
        );
    }



    /*
     * Returns sample installdefs that have beans and language
     */
    private function getSampleInstalldefs(){
        return array(
            'beans' =>
                array (
                    array (
                        'module' => 'PR_Professors',
                        'class' => 'PR_Professors',
                        'path' => 'modules/PR_Professors/PR_Professors.php',
                        'tab' => true,
                    ),
                ),
            'language' => array (
                array (
                    'from' => 'language/application/en_us.lang.php',
                    'to_module' => 'application',
                    'language' => 'en_us',
                ),
            )
        );
    }

    public function testShouldIncludeFileInZipValidFileMac(){
        $pg = new PackageGenerator();
        $this->assertTrue($pg->shouldIncludeFileInZip("src/custom/Extension/modules/Accounts/Ext/WirelessLayoutdefs/pr_professors_accounts_Accounts.php", false));
    }

    public function testShouldIncludeFileInZipValidFileWindows(){
        $pg = new PackageGenerator();
        $this->assertTrue($pg->shouldIncludeFileInZip("src\\custom\\Extension\\modules\\Accounts\\Ext\\WirelessLayoutdefs\\pr_professors_accounts_Accounts.php", false));
    }

    public function testShouldIncludeFileInZipFileInCustomApplicationExtMac(){
        $pg = new PackageGenerator();
        $this->assertFalse($pg->shouldIncludeFileInZip("src/custom/application/Ext/test.php", false));
    }

    public function testShouldIncludeFileInZipFileInCustomApplicationExtWindows(){
        $pg = new PackageGenerator();
        $this->assertFalse($pg->shouldIncludeFileInZip("src\\custom\\application\\Ext\\test.php", false));
    }

    public function testShouldIncludeFileInZipFileInCustomModulesModuleNameExtMac(){
        $pg = new PackageGenerator();
        $this->assertFalse($pg->shouldIncludeFileInZip("src/custom/modules/test/Ext/excludeme.php", false));
    }

    public function testShouldIncludeFileInZipFileInCustomModulesModuleNameExtWindows(){
        $pg = new PackageGenerator();
        $this->assertFalse($pg->shouldIncludeFileInZip("src\\custom\\modules\\test\\Ext\\excludeme.php", false));
    }


    public function testShouldIncludeFileInZipTestFileProductionBuild(){
        $pg = new PackageGenerator();
        $this->assertFalse($pg->shouldIncludeFileInZip(
            "src/custom/tests/School/unit-php/modules/Campaigns/clients/base/api/OnlineApplicationsAPITest.php",
            true));
    }

    public function testShouldIncludeFileInZipTestFileStandardBuild(){
        $pg = new PackageGenerator();
        $this->assertTrue($pg->shouldIncludeFileInZip(
            "src/custom/tests/School/unit-php/modules/Campaigns/clients/base/api/OnlineApplicationsAPITest.php",
            false));
    }

    public function testShouldIncludeFileInWindowsZip(){
        $pg = new PackageGenerator();
        $this->assertTrue($pg->shouldIncludeFileInWindowsZip(
            "src/modules/Opportunities/clients/base/views/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php",
            36));
    }

    public function testShouldIncludeFileInWindowsZipLongPath(){
        $pg = new PackageGenerator();
        $this->assertFalse($pg->shouldIncludeFileInWindowsZip(
            "src/modules/Opportunities/clients/base/views/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php",
            37));
    }

    public function testGetVersionNoDecimals(){
        $pg = new PackageGenerator();
        $this -> assertEquals(1, $pg -> getVersion(1));
    }

    public function testGetVersionDecimals(){
        $pg = new PackageGenerator();
        $this -> assertEquals("1.2.3", $pg -> getVersion("1.2.3"));
    }

    public function testGetVersionFromFile(){
        $root = vfsStream::setup();
        vfsStream::newFile("version") -> at($root) -> withContent("1.2.3");

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());
        $this -> assertEquals("1.2.3", $pg -> getVersion(""));
    }

    public function testGetVersionFromParamWhenVersionFileIsAvail(){
        $root = vfsStream::setup();
        vfsStream::newFile("version") -> at($root) -> withContent("1.2.3");

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());
        $this -> assertEquals("1.5", $pg -> getVersion("1.5"));
    }

    public function testGetZipFilePathValidParamsReleasesDirectoryDoesNotExist(){
        $pg = new PackageGenerator();

        $this -> assertFalse(is_dir("releases"));

        $this -> assertEquals("releases" . DIRECTORY_SEPARATOR . "sugarcrm-ProfessorM-1.5.zip",
            $pg -> getZipFilePath("1.5", "ProfessorM", "./pack.php"));

        $this -> assertTrue(is_dir("releases"));

    }

    public function testGetZipFilePathValidParamsReleasesDirectoryAlreadyExists(){
        $root = vfsStream::setup();
        vfsStream::newDirectory("releases") -> at($root);

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $this -> assertTrue($root -> hasChild("releases"));

        $this -> assertEquals("releases" . DIRECTORY_SEPARATOR . "sugarcrm-ProfessorM-1.5.zip",
            $pg -> getZipFilePath("1.5", "ProfessorM", "./pack.php"));

        $this -> assertTrue($root -> hasChild("releases"));

    }

    public function testGetZipFilePathEmptyVersion(){
        $pg = new PackageGenerator();
        $this -> expectException(Exception::class);

        $pg -> getZipFilePath("", "ProfessorM", "./pack.php");

    }

    public function testGetZipFileNameStandard(){
        $pg = new PackageGenerator();
        $this -> assertEquals("1.0-standard", $pg -> getZipFileNamePostfix("1.0", false, false));
    }

    public function testGetZipFileNameProd(){
        $pg = new PackageGenerator();
        $this -> assertEquals("1.0-production", $pg -> getZipFileNamePostfix("1.0", true, false));
    }

    public function testGetZipFileNameWindowsNonProd(){
        $pg = new PackageGenerator();
        $this -> assertEquals("1.0-windows", $pg -> getZipFileNamePostfix("1.0", false, true));
    }

    public function testGetZipFileNameWindowsProd(){
        $pg = new PackageGenerator();
        $this -> assertEquals("1.0-windows-production", $pg -> getZipFileNamePostfix("1.0", true, true));
    }

    public function testGetFileArraysForZipSingleFileToInclude(){
        $testVariables = $this -> getTestVariablesForSingleFileToInclude();
        $root = $testVariables['root'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $fileArrays = $pg -> getFileArraysForZip("src", false, false, null);
        $filesToInclude = $fileArrays["filesToInclude"];
        $filesToExclude = $fileArrays["filesToExclude"];
        $filesToExcludeWindows = $fileArrays["filesToExcludeWindows"];
        $this -> assertEquals(1, count($filesToInclude));
        $this -> assertEquals($testVariables["filesToInclude"], $filesToInclude);
        $this -> assertEquals(0, count($filesToExclude));
        $this -> assertEquals(0, count($filesToExcludeWindows));
    }

    public function testGetFileArraysForZipSingleFileToExclude(){
        $testVariables = $this -> getTestVariablesForSingleFileToExclude();
        $root = $testVariables['root'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $fileArrays = $pg -> getFileArraysForZip("src", false, false, null);
        $filesToInclude = $fileArrays["filesToInclude"];
        $filesToExclude = $fileArrays["filesToExclude"];
        $filesToExcludeWindows = $fileArrays["filesToExcludeWindows"];
        $this -> assertEquals(0, count($filesToInclude));
        $this -> assertEquals(1, count($filesToExclude));
        $this -> assertEquals($testVariables["filesToExclude"], $filesToExclude);
        $this -> assertEquals(0, count($filesToExcludeWindows));
    }

    public function testGetFileArraysForZipSingleFileToExcludeWindows(){
        $testVariables = $this -> getTestVariablesForSingleFileToExcludeOnWindows();
        $root = $testVariables['root'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $fileArrays = $pg -> getFileArraysForZip("src", false, true, 38);
        $filesToInclude = $fileArrays["filesToInclude"];
        $filesToExclude = $fileArrays["filesToExclude"];
        $filesToExcludeWindows = $fileArrays["filesToExcludeWindows"];
        $this -> assertEquals(0, count($filesToInclude));
        $this -> assertEquals(0, count($filesToExclude));
        $this -> assertEquals(1, count($filesToExcludeWindows));
        $this -> assertEquals($testVariables["filesToExcludeWindows"], $filesToExcludeWindows);
    }

    public function testGetFileArraysForZipMultipleFiles(){
        $testVariables = $this -> getTestVariablesForMultipleFiles();
        $root = $testVariables['root'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $fileArrays = $pg -> getFileArraysForZip("src", false, false, null);
        $filesToInclude = $fileArrays["filesToInclude"];
        $filesToExclude = $fileArrays["filesToExclude"];
        $filesToExcludeWindows = $fileArrays["filesToExcludeWindows"];

        $this -> assertEquals(3, count($filesToInclude));
        $this -> assertEquals($testVariables["filesToInclude"], $filesToInclude);
        $this -> assertEquals(2, count($filesToExclude));
        $this -> assertEquals($testVariables["filesToExclude"], $filesToExclude);
        $this -> assertEquals(0, count($filesToExcludeWindows));
    }

    public function testOpenZipValidParams(){
        $pg = new PackageGenerator();

        $zip = $pg -> openZip("1", "profM", "pack.php");
        $this -> assertContains('Creating releases' . DIRECTORY_SEPARATOR . 'sugarcrm-profM-1.zip', $this -> getActualOutput());
        $this -> assertEquals(0, $zip -> numFiles);
    }

    public function testOpenZipFileAlreadyExists(){
        mkdir("releases");
        fopen("releases/sugarcrm-profM-1.zip", "w");

        $pg = new PackageGenerator();

        $this -> expectException(Exception::class);
        $pg -> openZip("1", "profM", "pack.php");
    }

    /*
     * addFile does not work with the urls beginning with "vfs://" so this test does NOT
     * actually test that files were added to the zip.  Instead it tests the output of the
     * function is correct
     */
    public function testAddFilesToZipOneFile(){
        $testVariables = $this -> getTestVariablesForSingleFileToInclude();
        $root = $testVariables['root'];
        $filesToInclude = $testVariables['filesToInclude'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $zip = $pg -> openZip("1", "profM", "pack.php");

        $zip = $pg -> addFilesToZip($zip, $filesToInclude);

        $this -> assertContains("[*] src" . DIRECTORY_SEPARATOR . "myfile.php", $this -> getActualOutput());
    }

    /*
     * addFile does not work with the urls beginning with "vfs://" so this test does NOT
     * actually test that files were added to the zip.  Instead it tests the output of the
     * function is correct
     */
    public function testAddFilesToZipNoFiles(){
        $root = vfsStream::setup();

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $zip = $pg -> openZip("1", "profM", "pack.php");

        $filesToInclude = array();
        $outputBeforeAddingFiles = $this -> getActualOutput();

        $zip = $pg -> addFilesToZip($zip, $filesToInclude);

        $outputAfterAddingFiles = $this -> getActualOutput();

        $this -> assertEquals($outputBeforeAddingFiles, $outputAfterAddingFiles);
    }

    /*
     * addFile does not work with the urls beginning with "vfs://" so this test does NOT
     * actually test that files were added to the zip.  Instead it tests the output of the
     * function is correct
     */
    public function testAddFilesToZipMultipleFiles(){
        $testVariables = $this -> getTestVariablesForMultipleFiles();
        $root = $testVariables['root'];
        $filesToInclude = $testVariables['filesToInclude'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $zip = $pg -> openZip("1", "profM", "pack.php");

        $zip = $pg -> addFilesToZip($zip, $filesToInclude);

        $this -> assertContains("[*] src" . DIRECTORY_SEPARATOR . "language" . DIRECTORY_SEPARATOR . "application"
            . DIRECTORY_SEPARATOR . "en_us.lang.php", $this -> getActualOutput());
        $this -> assertContains("[*] src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default"
            . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "PR_Professors.gif", $this -> getActualOutput());
        $this -> assertContains("[*] src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default"
            . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "CreatePR_Professors.gif", $this -> getActualOutput());
    }

    /*
     * addFile does not work with the urls beginning with "vfs://" so this test does NOT
     * actually test that files were added to the zip.  Instead it tests the output of the
     * function is correct
     */
    public function testAddFilesToWindowsManualInstallZipOneFile(){
        $testVariables = $this -> getTestVariablesForSingleFileToExcludeOnWindows();
        $root = $testVariables['root'];
        $filesToInclude = $testVariables['filesToExcludeWindows'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $zip = $pg -> openZip("1", "profM", "pack.php");

        $zip = $pg -> addFilesToWindowsManualInstallZip($zip, $filesToInclude, "src");

        $this -> assertContains(
            "[*] 0/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php\n",
            $this -> getActualOutput());

        $this -> assertContains(
            "ProfMForWindowsReadme.txt",
            $this -> getActualOutput());
    }

    /*
     * addFile does not work with the urls beginning with "vfs://" so this test does NOT
     * actually test that files were added to the zip.  Instead it tests the output of the
     * function is correct
    */
    public function testAddFilesToWindowsManualInstallZipNoFiles(){
        $root = vfsStream::setup();

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $zip = $pg -> openZip("1", "profM", "pack.php");

        $filesToInclude = array();

        $zip = $pg -> addFilesToWindowsManualInstallZip($zip, $filesToInclude, "src");

        $this -> assertContains(
            "ProfMForWindowsReadme.txt",
            $this -> getActualOutput());
    }

    /*
     * addFile does not work with the urls beginning with "vfs://" so this test does NOT
     * actually test that files were added to the zip.  Instead it tests the output of the
     * function is correct
     */
    public function testAddFilesToWindowsManualInstallZipMultipleFile(){
        $testVariables = $this -> getTestVariablesForMultipleFileToExcludeOnWindows();
        $root = $testVariables['root'];
        $filesToInclude = $testVariables['filesToExcludeWindows'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $zip = $pg -> openZip("1", "profM", "pack.php");

        $zip = $pg -> addFilesToWindowsManualInstallZip($zip, $filesToInclude, "src");

        $this -> assertContains(
            "[*] 0/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php\n",
            $this -> getActualOutput());

        $this -> assertContains(
            "[*] 1/another-really-super-duper-crazy-long-filename-that-windows-simply-cannot-handle.php\n",
            $this -> getActualOutput());

        $this -> assertContains(
            "ProfMForWindowsReadme.txt",
            $this -> getActualOutput());
    }

    public function testAddFilesToInstallDefsOneFile(){
        $installdefs = $this->getSampleInstalldefs();

        $testVariables = $this -> getTestVariablesForSingleFileToInclude();
        $root = $testVariables['root'];
        $filesToInclude = $testVariables['filesToInclude'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $installdefs = $pg -> addFilesToInstalldefs($filesToInclude, $installdefs, "src");

        $this -> assertEquals(1, count($installdefs['copy']));
        $this -> assertEquals('<basepath>/src' . DIRECTORY_SEPARATOR . 'myfile.php',
            $installdefs['copy'][0]['from']);
        $this -> assertEquals('myfile.php', $installdefs['copy'][0]['to']);
    }

    public function testAddFilesToInstallDefsNoFiles(){
        $installdefs = $this->getSampleInstalldefs();

        $root = vfsStream::setup();

        $filesToInclude = array();

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $installdefs = $pg -> addFilesToInstalldefs($filesToInclude, $installdefs, "src");

        $this -> assertArrayNotHasKey('copy', $installdefs);
    }

    public function testAddFilesToInstallDefsMultipleFiles(){
        $installdefs = $this->getSampleInstalldefs();

        $testVariables = $this->getTestVariablesForMultipleFiles();
        $root = $testVariables['root'];
        $filesToInclude = $testVariables['filesToInclude'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $installdefs = $pg -> addFilesToInstalldefs($filesToInclude, $installdefs, "src");

        $this -> assertEquals(3, count($installdefs['copy']));
        $this -> assertEquals('<basepath>/src' . DIRECTORY_SEPARATOR . 'language' .
            DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'en_us.lang.php', $installdefs['copy'][0]['from']);
        $this -> assertEquals('language' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR .
            'en_us.lang.php', $installdefs['copy'][0]['to']);
        $this -> assertEquals('<basepath>/src' . DIRECTORY_SEPARATOR . 'icons'
            . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR
            . 'PR_Professors.gif', $installdefs['copy'][1]['from']);
        $this -> assertEquals('icons' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR .
            'images' . DIRECTORY_SEPARATOR . 'PR_Professors.gif', $installdefs['copy'][1]['to']);
        $this -> assertEquals('<basepath>/src' . DIRECTORY_SEPARATOR . 'icons'
            . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR .
            'CreatePR_Professors.gif', $installdefs['copy'][2]['from']);
        $this -> assertEquals('icons' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'images'
            . DIRECTORY_SEPARATOR . 'CreatePR_Professors.gif', $installdefs['copy'][2]['to']);
    }

    /*
     * There is not a good way to test if a zip file is closed so this just checks
     * that the output of the function is correct.
     */
    public function testCloseZip(){
        $root = vfsStream::setup();

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $zip = $pg -> openZip("1", "profM", "pack.php");

        $pg -> closeZip($zip);
        $this -> assertContains('Done creating sugarcrm-profM-1.zip', $this -> getActualOutput());
    }

    public function testEchoExcludedFilesWithSingleFileToExclude(){
        $testVariables = $this->getTestVariablesForSingleFileToExclude();
        $filesToExclude = $testVariables['filesToExclude'];

        $pg = new PackageGenerator();

        $pg -> echoExcludedFiles($filesToExclude);
        $output = $this->getActualOutput();
        $this -> assertContains('The following files were excluded from the zip:',
            $output);
        $this -> assertContains('[*] src' . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR .
            'application' . DIRECTORY_SEPARATOR . 'Ext' . DIRECTORY_SEPARATOR . 'test.php',
            $output);
    }

    /**
     * Unit test to verify that .DS_Store and .git subdirectories are excluded from MLP.
     * They will fail if we try to install in OD instances due to package scanner.
     */
    public function testExcludesDSStoreGitDirectories(){
        $pg = new PackageGenerator();
        $fullPathDSStore = "src" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR. ".DS_Store";
        $this->assertFalse($pg->shouldIncludeFileInZip($fullPathDSStore, false), "Should exclude " . $fullPathDSStore);
        $this->assertFalse($pg->shouldIncludeFileInZip("src" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . ".git", false), "Should exclude .git in path");
        $this->assertFalse($pg->shouldIncludeFileInZip(".DS_Store", false), "Should exclude .DS_Store");
        $this->assertFalse($pg->shouldIncludeFileInZip(".git", false), "Should exclude .git");
    }

    public function testEchoExcludedFilesWithNoFilesToExclude(){
        $filesToExclude = array();

        $pg = new PackageGenerator();

        $pg -> echoExcludedFiles($filesToExclude);
        $this -> assertEquals("", $this -> getActualOutput());
    }

    public function testEchoExcludedFilesWithMultipleFilesToExclude(){
        $testVariables = $this->getTestVariablesForMultipleFiles();
        $filesToExclude = $testVariables['filesToExclude'];

        $pg = new PackageGenerator();

        $pg -> echoExcludedFiles($filesToExclude);
        $output = $this->getActualOutput();
        $this -> assertContains('The following files were excluded from the zip:',
            $output);
        $this -> assertContains('[*] src' . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'application'
            . DIRECTORY_SEPARATOR . 'Ext' . DIRECTORY_SEPARATOR . 'test.php',
            $output);
        $this -> assertContains('[*] src' . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'modules'
            . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'Ext' . DIRECTORY_SEPARATOR . 'excludeme.php',
            $output);
    }

    public function testGenerateManifestValidParams(){
        $manifest = array(
            'id' => 'profM',
            'name' => 'Professor M');

        $installdefs = $this->getSampleInstalldefs();

        $pg = new PackageGenerator();

        $zip = $pg -> openZip("1", "profM", "pack.php");

        $zip = $pg -> generateManifest($manifest, $installdefs, $zip);

        $zip->open('releases/sugarcrm-profM-1.zip');
        $generatedManifest = $zip -> getFromName('manifest.php');

        $expectedManifest =
            "<?php\n" .
            "\$manifest = array (\n" .
            "  'id' => 'profM',\n" .
            "  'name' => 'Professor M',\n" .
            ");\n" .
            "\$installdefs = array (\n" .
            "  'beans' => \n" .
            "  array (\n" .
            "    0 => \n" .
            "    array (\n" .
            "      'module' => 'PR_Professors',\n" .
            "      'class' => 'PR_Professors',\n" .
            "      'path' => 'modules/PR_Professors/PR_Professors.php',\n" .
            "      'tab' => true,\n" .
            "    ),\n" .
            "  ),\n" .
            "  'language' => \n" .
            "  array (\n" .
            "    0 => \n" .
            "    array (\n" .
            "      'from' => 'language/application/en_us.lang.php',\n" .
            "      'to_module' => 'application',\n" .
            "      'language' => 'en_us',\n" .
            "    ),\n" .
            "  ),\n" .
            ");\n";

        $this -> assertEquals($expectedManifest, $generatedManifest);
    }

    /*
     * Things get really hairy when we're going back and forth between using the virtual file system and the
     * real file system. Since we have other tests that test the individual pieces, we'll just check that the
     * output of generating the zip is correct.
     */
    public function testGenerateZipMultipleFiles(){
        $testVariables = $this -> getTestVariablesForMultipleFiles();
        $root = $testVariables['root'];

        $manifest = array(
            'id' => 'profM',
            'name' => 'Professor M');

        $installdefs = $this->getSampleInstalldefs();

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $zip = $pg -> generateZip("1", "profM", "pack.php", "src", $manifest,
            $installdefs, false, false, null);

        $expectedOutput =
            "Creating releases" . DIRECTORY_SEPARATOR . "sugarcrm-profM-1-standard.zip ... \n" .
            " [*] src" . DIRECTORY_SEPARATOR . "language" . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR
                . "en_us.lang.php\n" .
            " [*] src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR
                . "images" . DIRECTORY_SEPARATOR . "PR_Professors.gif\n" .
            " [*] src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR .
                "images" . DIRECTORY_SEPARATOR . "CreatePR_Professors.gif\n" .
            "Done creating sugarcrm-profM-1-standard.zip\n\n" .

            "The following files were excluded from the zip: \n" .
            " [*] src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR .
                "Ext" . DIRECTORY_SEPARATOR . "test.php\n" .
            " [*] src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR .
                "test" . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "excludeme.php\n";

        $this -> assertEquals($expectedOutput, $this -> getActualOutput());
    }

    /*
     * Things get really hairy when we're going back and forth between using the virtual file system and the
     * real file system. Since we have other tests that test the individual pieces, we'll just check that the
     * output of generating the zip is correct.
     */
    public function testGenerateZipForWindowsMultipleFiles(){
        $testVariables = $this -> getTestVariablesForMultipleFileToExcludeOnWindows();
        $root = $testVariables['root'];

        $manifest = array(
            'id' => 'profM',
            'name' => 'Professor M');

        $installdefs = $this->getSampleInstalldefs();

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $zip = $pg -> generateZip("1", "profM", "pack.php", "src", $manifest,
            $installdefs, false, true, 38);

        $expectedOutput =
            "Creating releases" . DIRECTORY_SEPARATOR . "sugarcrm-profM-1-windows.zip ... \n" .
            " [*] src" . DIRECTORY_SEPARATOR . "language" . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR
                . "en_us.lang.php\n" .
            " [*] src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR
                . "images" . DIRECTORY_SEPARATOR . "PR_Professors.gif\n" .
            " [*] src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR .
                "images" . DIRECTORY_SEPARATOR . "CreatePR_Professors.gif\n" .
            "Done creating sugarcrm-profM-1-windows.zip\n\n" .

            "The following files were excluded from the zip: \n" .
            " [*] src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR .
                "Ext" . DIRECTORY_SEPARATOR . "test.php\n" .
            " [*] src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR .
                "test" . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "excludeme.php\n" .

            "Creating releases" . DIRECTORY_SEPARATOR . "sugarcrm-profM-1-windows-manual-install.zip ... \n" .
            " [*] 0/subpanel-for-pmse_bpmprocessdefinition-opportunities_locked_fields_link.php\n" .
            " [*] 1/another-really-super-duper-crazy-long-filename-that-windows-simply-cannot-handle.php\n" .
            " [*] ProfMForWindowsReadme.txt\n" .
            "Done creating sugarcrm-profM-1-windows-manual-install.zip\n\n";

        $this -> assertEquals($expectedOutput, $this -> getActualOutput());
    }
}

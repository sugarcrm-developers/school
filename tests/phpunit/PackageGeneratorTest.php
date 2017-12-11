<?php
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
        $this->assertTrue($pg->shouldIncludeFileInZip("src/custom/Extension/modules/Accounts/Ext/WirelessLayoutdefs/pr_professors_accounts_Accounts.php"));
    }

    public function testShouldIncludeFileInZipValidFileWindows(){
        $pg = new PackageGenerator();
        $this->assertTrue($pg->shouldIncludeFileInZip("src\\custom\\Extension\\modules\\Accounts\\Ext\\WirelessLayoutdefs\\pr_professors_accounts_Accounts.php"));
    }

    public function testShouldIncludeFileInZipFileInCustomApplicationExtMac(){
        $pg = new PackageGenerator();
        $this->assertFalse($pg->shouldIncludeFileInZip("src/custom/application/Ext/test.php"));
    }

    public function testShouldIncludeFileInZipFileInCustomApplicationExtWindows(){
        $pg = new PackageGenerator();
        $this->assertFalse($pg->shouldIncludeFileInZip("src\\custom\\application\\Ext\\test.php"));
    }

    public function testShouldIncludeFileInZipFileInCustomModulesModuleNameExtMac(){
        $pg = new PackageGenerator();
        $this->assertFalse($pg->shouldIncludeFileInZip("src/custom/modules/test/Ext/excludeme.php"));
    }

    public function testShouldIncludeFileInZipFileInCustomModulesModuleNameExtWindows(){
        $pg = new PackageGenerator();
        $this->assertFalse($pg->shouldIncludeFileInZip("src\\custom\\modules\\test\\Ext\\excludeme.php"));
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

    public function testGetFileArraysForZipSingleFileToInclude(){
        $testVariables = $this -> getTestVariablesForSingleFileToInclude();
        $root = $testVariables['root'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $fileArrays = $pg -> getFileArraysForZip("src");
        $filesToInclude = $fileArrays["filesToInclude"];
        $filesToExclude = $fileArrays["filesToExclude"];
        $this -> assertEquals(1, count($filesToInclude));
        $this -> assertEquals("src" . DIRECTORY_SEPARATOR . "myfile.php", $filesToInclude[0]["fileRelative"]);
        $this -> assertEquals("vfs://root" . DIRECTORY_SEPARATOR
            . "src" . DIRECTORY_SEPARATOR . "myfile.php", $filesToInclude[0]["fileReal"]);
        $this -> assertEquals(0, count($filesToExclude));
    }

    public function testGetFileArraysForZipSingleFileToExclude(){
        $testVariables = $this -> getTestVariablesForSingleFileToExclude();
        $root = $testVariables['root'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $fileArrays = $pg -> getFileArraysForZip("src");
        $filesToInclude = $fileArrays["filesToInclude"];
        $filesToExclude = $fileArrays["filesToExclude"];
        $this -> assertEquals(0, count($filesToInclude));
        $this -> assertEquals(1, count($filesToExclude));
        $this -> assertEquals("src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "application"
            . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "test.php", $filesToExclude[0]["fileRelative"]);
        $this -> assertEquals("vfs://root" . DIRECTORY_SEPARATOR
            . "src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR .
            "Ext" . DIRECTORY_SEPARATOR . "test.php", $filesToExclude[0]["fileReal"]);
    }

    public function testGetFileArraysForZipMultipleFiles(){
        $testVariables = $this -> getTestVariablesForMultipleFiles();
        $root = $testVariables['root'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $fileArrays = $pg -> getFileArraysForZip("src");
        $filesToInclude = $fileArrays["filesToInclude"];
        $filesToExclude = $fileArrays["filesToExclude"];

        $this -> assertEquals(3, count($filesToInclude));
        $this -> assertEquals("src" . DIRECTORY_SEPARATOR . "language" . DIRECTORY_SEPARATOR . "application"
            . DIRECTORY_SEPARATOR . "en_us.lang.php", $filesToInclude[0]["fileRelative"]);
        $this -> assertEquals("vfs://root" . DIRECTORY_SEPARATOR
            . "src" . DIRECTORY_SEPARATOR . "language" . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR .
            "en_us.lang.php", $filesToInclude[0]["fileReal"]);
        $this -> assertEquals("src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default" .
            DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "PR_Professors.gif", $filesToInclude[1]["fileRelative"]);
        $this -> assertEquals("vfs://root" . DIRECTORY_SEPARATOR
            . "src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR . "images"
            . DIRECTORY_SEPARATOR . "PR_Professors.gif", $filesToInclude[1]["fileReal"]);
        $this -> assertEquals("src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default"
            . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "CreatePR_Professors.gif", $filesToInclude[2]["fileRelative"]);
        $this -> assertEquals("vfs://root" . DIRECTORY_SEPARATOR
            . "src" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR . "images"
            . DIRECTORY_SEPARATOR . "CreatePR_Professors.gif", $filesToInclude[2]["fileReal"]);

        $this -> assertEquals(2, count($filesToExclude));
        $this -> assertEquals("src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "application"
            . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "test.php", $filesToExclude[0]["fileRelative"]);
        $this -> assertEquals("vfs://root" . DIRECTORY_SEPARATOR
            . "src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "Ext"
            . DIRECTORY_SEPARATOR . "test.php", $filesToExclude[0]["fileReal"]);
        $this -> assertEquals("src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "modules" .
            DIRECTORY_SEPARATOR . "test" . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "excludeme.php",
            $filesToExclude[1]["fileRelative"]);
        $this -> assertEquals("vfs://root" . DIRECTORY_SEPARATOR
            . "src" . DIRECTORY_SEPARATOR . "custom"
            . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "test" . DIRECTORY_SEPARATOR . "Ext"
            . DIRECTORY_SEPARATOR . "excludeme.php", $filesToExclude[1]["fileReal"]);
    }

    public function testOpenZipValidParams(){
        $pg = new PackageGenerator();

        $zip = $pg -> openZip("1", "profM", "pack.php");
        $this -> assertContains('Creating releases/sugarcrm-profM-1.zip', $this -> getActualOutput());
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

    public function testAddFilesToInstallDefsOneFile(){
        $installdefs = $this->getSampleInstalldefs();

        $testVariables = $this -> getTestVariablesForSingleFileToInclude();
        $root = $testVariables['root'];
        $filesToInclude = $testVariables['filesToInclude'];

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $installdefs = $pg -> addFilesToInstalldefs($filesToInclude, $installdefs, "src");

        $this -> assertEquals(1, count($installdefs['copy']));
        $this -> assertEquals('<basepath>/src/myfile.php', $installdefs['copy'][0]['from']);
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
        $this -> assertEquals('<basepath>/src/language/application/en_us.lang.php', $installdefs['copy'][0]['from']);
        $this -> assertEquals('language/application/en_us.lang.php', $installdefs['copy'][0]['to']);
        $this -> assertEquals('<basepath>/src/icons/default/images/PR_Professors.gif', $installdefs['copy'][1]['from']);
        $this -> assertEquals('icons/default/images/PR_Professors.gif', $installdefs['copy'][1]['to']);
        $this -> assertEquals('<basepath>/src/icons/default/images/CreatePR_Professors.gif', $installdefs['copy'][2]['from']);
        $this -> assertEquals('icons/default/images/CreatePR_Professors.gif', $installdefs['copy'][2]['to']);
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
        $this -> assertContains('[*] src/custom/application/Ext/test.php',
            $output);
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
        $this -> assertContains('[*] src/custom/application/Ext/test.php',
            $output);
        $this -> assertContains('[*] src/custom/modules/test/Ext/excludeme.php',
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

        $zip = $pg -> generateZip("1", "profM", "pack.php", "src", $manifest, $installdefs);

        $expectedOutput =
            "Creating releases/sugarcrm-profM-1.zip ... \n" .
            " [*] src/language/application/en_us.lang.php\n" .
            " [*] src/icons/default/images/PR_Professors.gif\n" .
            " [*] src/icons/default/images/CreatePR_Professors.gif\n" .
            "Done creating sugarcrm-profM-1.zip\n\n" .

            "The following files were excluded from the zip: \n" .
            " [*] src/custom/application/Ext/test.php\n" .
            " [*] src/custom/modules/test/Ext/excludeme.php\n";

        $this -> assertEquals($expectedOutput, $this -> getActualOutput());
    }
}

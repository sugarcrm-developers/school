<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Sugarcrm\ProfessorM\PackageGenerator;
use org\bovigo\vfs\vfsStream;

class PackageGeneratorTest extends TestCase
{

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
        $root = vfsStream::setup();

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $this -> assertFalse($root -> hasChild("releases"));

        $this -> assertEquals("releases" . DIRECTORY_SEPARATOR . "sugarcrm-ProfessorM-1.5.zip",
            $pg -> getZipFilePath("1.5", "ProfessorM", "./pack.php"));

        $this -> assertTrue($root -> hasChild("releases"));

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
        $root = vfsStream::setup();
        $srcDirectory = vfsStream::newDirectory("src") -> at($root);
        vfsStream::newFile("myfile.php") -> at($srcDirectory);

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
        $root = vfsStream::setup();
        $srcDirectory = vfsStream::newDirectory("src") -> at($root);
        $customDirectory = vfsStream::newDirectory("custom") -> at($srcDirectory);
        $applicationDirectory = vfsStream::newDirectory("application") -> at($customDirectory);
        $ExtDirectory = vfsStream::newDirectory("Ext") -> at($applicationDirectory);
        vfsStream::newFile("myfile.php") -> at($ExtDirectory);

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $fileArrays = $pg -> getFileArraysForZip("src");
        $filesToInclude = $fileArrays["filesToInclude"];
        $filesToExclude = $fileArrays["filesToExclude"];
        $this -> assertEquals(0, count($filesToInclude));
        $this -> assertEquals(1, count($filesToExclude));
        $this -> assertEquals("src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "application"
            . DIRECTORY_SEPARATOR . "Ext" . DIRECTORY_SEPARATOR . "myfile.php", $filesToExclude[0]["fileRelative"]);
        $this -> assertEquals("vfs://root" . DIRECTORY_SEPARATOR
            . "src" . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR .
            "Ext" . DIRECTORY_SEPARATOR . "myfile.php", $filesToExclude[0]["fileReal"]);
    }

    public function testGetFileArrayForZipMultipleFiles(){
        /*
         * Files to be included:
         *  [*] src/language/application/en_us.lang.php
         *  [*] src/icons/default/images/PR_Professors.gif
         *  [*] src/icons/default/images/CreatePR_Professors.gif
         *
         * Files to be excluded:
         * [*] src/custom/application/Ext/test.php
         * [*] src/custom/modules/test/Ext/excludeme.php
         */

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

}

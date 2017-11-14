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

        $this -> assertEquals("releases/sugarcrm-ProfessorM-1.5.zip",
            $pg -> getZipFilePath("1.5", "ProfessorM", "./pack.php"));

        $this -> assertTrue($root -> hasChild("releases"));

    }

    public function testGetZipFilePathValidParamsReleasesDirectoryAlreadyExists(){
        $root = vfsStream::setup();
        vfsStream::newDirectory("releases") -> at($root);

        $pg = new PackageGenerator();
        $pg -> setCwd($root -> url());

        $this -> assertTrue($root -> hasChild("releases"));

        $this -> assertEquals("releases/sugarcrm-ProfessorM-1.5.zip",
            $pg -> getZipFilePath("1.5", "ProfessorM", "./pack.php"));

        $this -> assertTrue($root -> hasChild("releases"));

    }

    public function testGetZipFilePathEmptyVersion(){
        $pg = new PackageGenerator();
        $this -> expectException(Exception::class);

        $pg -> getZipFilePath("", "ProfessorM", "./pack.php");

    }

}

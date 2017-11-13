<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Sugarcrm\ProfessorM\PackageGenerator;

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

}

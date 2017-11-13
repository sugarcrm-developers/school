<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Sugarcrm\ProfessorM\PackageGenerator;

class PackageGeneratorTest extends TestCase
{

    public function testPack(){
        $pack = new PackageGenerator();
        $this->assertFalse($pack->shouldExcludeFile());
    }

}

<?php
declare(strict_types=1);
include('package/pack.php');

use PHPUnit\Framework\TestCase;

final class PackTest extends TestCase
{
    public function testExclude(): void
    {
        $pack = new Pack();
        $this->assertFalse($pack->shouldExcludeFile());
    }

}

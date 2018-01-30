<?php
declare(strict_types=1);

use Sugarcrm\Sugarcrm\custom\gradebook_fake\RecordManager;
use PHPUnit\Framework\TestCase;

class RecordManagerTest extends TestCase
{
    public function testRecordManagerValidParams(){
        $rm = new RecordManager();
        $this->assertTrue($rm->createStudentRecord('lauren@example.com', 'Lauren', 'Sample'));
    }

    public function testRecordManagerForceException(){
        $rm = new RecordManager();
        $this -> expectException(Exception::class);
        $rm->createStudentRecord('forceerror@example.com', 'Lauren', 'Sample');
    }
}

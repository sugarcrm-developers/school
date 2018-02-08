<?php
declare(strict_types=1);

use Sugarcrm\Sugarcrm\custom\gradebook_fake\RecordManager;
use PHPUnit\Framework\TestCase;

/**
 * Class RecordManagerTest
 * Tests the RecordManager for the GradebookFake app
 */
class RecordManagerTest extends TestCase
{
    /**
     * Check that when valid params are sent to createStudentRecord, true is returned
     */
    public function testRecordManagerValidParams(){
        $rm = new RecordManager();
        $this->assertTrue($rm->createStudentRecord('lauren@example.com', 'Lauren', 'Sample'));
    }

    /**
     * Check that when the email address forceerror@example.com is used as a param for createStudentRecord, an
     * exception is thrown
     */
    public function testRecordManagerForceException(){
        $rm = new RecordManager();
        $this -> expectException(Exception::class);
        $rm->createStudentRecord('forceerror@example.com', 'Lauren', 'Sample');
    }
}

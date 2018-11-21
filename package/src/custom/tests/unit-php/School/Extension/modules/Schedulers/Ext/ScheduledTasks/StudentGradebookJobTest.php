<?php

namespace Sugarcrm\SugarcrmTestsUnit\modules\Schedulers\Ext\ScheduledTasks;

use Sugarcrm\SugarcrmTestsUnit\TestMockHelper;
use Sugarcrm\Sugarcrm\Util\Uuid;

require_once 'custom/Extension/modules/Schedulers/Ext/ScheduledTasks/StudentGradebookJob.php';
/**
 * @coversDefaultClass \StudentGradebookJob
 */
class StudentGradebookJobTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var a partial mock of a Student (Contact) with an id, first name, last name, and email
     */
    private $student;

    /**
     * @var a partial mock of SchedulersJob
     */
    private $job;

    /**
     * @var a partial mock of RecordManager
     */
    private $rm;

    /**
     * @var a partial mock of StudentGradebookJob
     */
    private $sgJob;

    protected function setUp()
    {
        parent::setUp();

        // Create a new Student (Contact) with an id, first name, last name, and email
        $this->student = TestMockHelper::createPartialMock($this, '\\Contact', []);
        $this->student->id = Uuid::uuid1();
        $this->student->first_name = 'John';
        $this->student->last_name = 'Doe';

        $this->emailAddress = TestMockHelper::createMock($this, '\\SugarEmailAddress');
        $this->student->emailAddress = $this->emailAddress;
        $this->emailAddress->method('getPrimaryAddress')->willReturn('jdoe@example.com');

        // Create a new SchedulersJob and set the job's data to the student's id
        $this->job = TestMockHelper::createPartialMock($this, '\\SchedulersJob', ['succeedJob', 'failJob']);
        $this->job->data = $this->student->id;

        // Create a mock of the Gradebook's RecordManager
        $this->rm = TestMockHelper::createPartialMock($this, '\\Sugarcrm\\Sugarcrm\\custom\\gradebook_fake\\RecordManager', ['createStudentRecord']);

        // Create a mock of the StudentGradebookJob. getContactBean will return the mock student we created.
        // getRecordManager returns the mock RecordManager we created
        $this->sgJob = TestMockHelper::createPartialMock($this, '\\StudentGradebookJob', ['getContactBean', 'getRecordManager']);
        $this->sgJob->method('getContactBean')->willReturn($this->student);
        $this->sgJob->method('getRecordManager')->willReturn($this->rm);

        // Set the job to be our mock job
        $this->sgJob->setJob($this->job);
    }

    /**
     * @covers ::run
     */
    public function testRun_JobSucceeds()
    {
        $this->job->expects($this->once())->method('succeedJob');
        $this->job->expects($this->never())->method('failJob');

        $this->rm->expects($this->once())->method('createStudentRecord')->willReturn(true);

        $actual = $this->sgJob->run($this->job->data);

        $this->assertTrue($actual);
    }

    /**
     * @covers ::run
     */
    public function testRun_RecordManagerReturnsFalse_JobFails()
    {
        $this->job->expects($this->never())->method('succeedJob');
        $this->job->expects($this->once())->method('failJob')->with("Record not successfully created in GradebookFake");

        $this->rm->expects($this->once())->method('createStudentRecord')->willReturn(false);

        $actual = $this->sgJob->run($this->job->data);

        $this->assertFalse($actual);
    }

    /**
     * @covers ::run
     */
    public function testRun_RecordManagerThrowsException_JobFails()
    {
        $exceptionMessage = "An exception occurred";

        $this->job->expects($this->never())->method('succeedJob');
        $this->job->expects($this->once())->method('failJob')->with($exceptionMessage);

        $this->rm->expects($this->once())->method('createStudentRecord')->willThrowException(new \Exception($exceptionMessage));

        $actual = $this->sgJob->run($this->job->data);

        $this->assertFalse($actual);
    }

    /**
     * @covers ::run
     */
    public function testRun_NoData_JobFails()
    {
        $this->job->expects($this->never())->method('succeedJob');
        $this->job->expects($this->once())->method('failJob')->with("Job had no data");

        $this->rm->expects($this->never())->method('createStudentRecord');

        $actual = $this->sgJob->run('');

        $this->assertFalse($actual);
    }

}

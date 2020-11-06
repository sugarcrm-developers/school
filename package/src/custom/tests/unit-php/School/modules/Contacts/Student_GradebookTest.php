<?php

use Sugarcrm\SugarcrmTestsUnit\TestMockHelper;
use Sugarcrm\Sugarcrm\Util\Uuid;

require_once 'custom/modules/Contacts/Students_Gradebook.php';

/**
 * @coversDefaultClass \Students_Gradebook
 */
class Student_GradebookTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var a partial mock of SchedulersJob
     */
    private $job;

    /**
     * @var a partial mock of a Sugar Job Queue
     */
    private $queue;

    /**
     * A partial mock of Students_Gradebook
     */
    private $sg;

    /**
     * @var a partial mock of a Student (Contact) with an id, first name, last name, and email
     */
    private $student;

    protected function setUp() :void
    {
        parent::setUp();
        $GLOBALS['current_user'] = $this->createPartialMock('\\User', []);
        $GLOBALS['current_user']->id = Uuid::uuid1();

        $this->job = TestMockHelper::createPartialMock($this, '\\SchedulersJob', []);

        $this->queue = TestMockHelper::createPartialMock($this, '\\SugarJobQueue', ['submitJob']);

        $this->sg = TestMockHelper::createPartialMock($this, '\\Students_Gradebook', ['getSchedulersJob', 'getSugarJobQueue']);
        $this->sg->method('getSchedulersJob')->willReturn($this->job);
        $this->sg->method('getSugarJobQueue')->willReturn($this->queue);

        $this->student = TestMockHelper::createPartialMock($this, '\\Contact', []);
        $this->student->id = Uuid::uuid1();
        $this->student->first_name = 'John';
        $this->student->last_name = 'Doe';
        $this->student->email1 = 'jdoe@example.com';
    }

    protected function tearDown()
    {
        unset($GLOBALS['current_user']);
        parent::tearDown();
    }

    /**
     * @covers ::addStudentToGradebook
     */
    public function testAddStudentToGradebook_CreatesTheJob()
    {
        $this->queue->expects($this->once())->method('submitJob')->with($this->equalTo($this->job));

        $this->sg->addStudentToGradebook($this->student, 'after_save', ['isUpdate' => false]);

        $this->assertSame('Add New Student to Gradebook Job', $this->job->name);
        $this->assertSame($this->student->id, $this->job->data);
        $this->assertSame('class::StudentGradebookJob', $this->job->target);
        $this->assertSame($GLOBALS['current_user']->id, $this->job->assigned_user_id);
    }

    /**
     * @covers ::addStudentToGradebook
     */
    public function testAddStudentToGradebook_DoesNotCreateTheJob_NotAfterSaveEvent()
    {
        $this->queue->expects($this->never())->method('submitJob');
        $this->sg->addStudentToGradebook($this->student, 'after_relationship_add', ['isUpdate' => false]);
    }

    /**
     * @covers ::addStudentToGradebook
     */
    public function testAddStudentToGradebook_DoesNotCreateTheJob_IsUpdateEvent()
    {
        $this->queue->expects($this->never())->method('submitJob');
        $this->sg->addStudentToGradebook($this->student, 'after_save', ['isUpdate' => true]);
    }

}

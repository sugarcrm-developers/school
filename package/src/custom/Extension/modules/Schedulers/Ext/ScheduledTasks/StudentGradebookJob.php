<?php

use Sugarcrm\Sugarcrm\custom\gradebook_fake\RecordManager;

class StudentGradebookJob implements RunnableSchedulerJob
{

    /**
     * @var SchedulersJob
     */
    protected $job;
    /**
     * @param SchedulersJob $job
     */
    public function setJob(SchedulersJob $job)
    {
        $this->job = $job;
    }

    /**
     * This function defines the job that adds a new student to the GradebookFake app
     * @param string $data The id for the new Student (contact) record
     * @return bool true if a record was successfully created in the GradebookFake app
     */
    public function run($data)
    {
        if (!empty($data)) {
            $bean = $this->getContactBean($data);

            try {
                //Call the external GradebookFake app to create a new record in it
                $rm = $this->getRecordManager();
                $success = $rm->createStudentRecord($bean->emailAddress->getPrimaryAddress($bean), $bean->first_name,
                    $bean->last_name);
                if ($success) {
                    $this->job->succeedJob();
                    return true;
                } else {
                    $this->job->failJob("Record not successfully created in GradebookFake");
                    return false;
                }
            } catch (Exception $e) {
                $this->job->failJob($e->getMessage());

                return false;
            }

        }

        $this->job->failJob("Job had no data");
        return false;
    }

    /**
     * Get the Contact (Student) bean for the given id
     * @param $id The id for which you want to retrieve a Contact (Student) bean
     * @return null|SugarBean
     */
    protected function getContactBean($id)
    {
        return BeanFactory::retrieveBean('Contacts', $id);
    }

    /**
     * Get the Record Manager for the GradebookFake app
     * @return RecordManager The Record Manager for the GradebookFake app
     */
    protected function getRecordManager()
    {
        return new RecordManager();
    }

}

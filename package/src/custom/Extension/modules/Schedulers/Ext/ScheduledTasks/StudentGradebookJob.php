<?php

use Sugarcrm\Sugarcrm\custom\gradebook_fake\RecordManager;

class StudentGradebookJob implements RunnableSchedulerJob {

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
     * @param $job Information about the job. $job->data should be the id for the new Student (contact) record
     * @return bool true if a record was successfully created in the GradebookFake app
     */
    public function run($data)
    {
        if (!empty($data))
        {
            $bean = $this->getContactBean($data);

            try{
                //Call the external GradebookFake app to create a new record in it
                $rm = $this->getRecordManager();
                $success = $rm->createStudentRecord($bean->email1, $bean->first_name, $bean->last_name);
                if ($success){
                    $this->job->succeedJob();
                    return true;
                } else{
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

    protected function getContactBean($id){
        return BeanFactory::getBean('Contacts', $id);
    }

    protected function getRecordManager()
    {
        return new RecordManager();
    }

}

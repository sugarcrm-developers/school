<?php

require_once 'include/SugarQueue/SugarJobQueue.php';

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/**
 * Class Students_Gradebook
 * Handles creating a job for the Sugar Job Queue that adds a new student to the GradebookFake app
 */
class Students_Gradebook
{
    /**
     * Creates a job that calls a function to add a new student to the GradebookFake app.  Adds the job to the Sugar
     * Job Queue. The job will be executed the next time the Queue is run.
     * @param $bean The bean for the Student (Contact) record
     * @param $event The current event
     * @param $arguments Additional information related to the event
     */
    public function addStudentToGradebook(&$bean, $event, $arguments)
    {
        if ($event !== 'after_save') {
            return;
        }

        //Check if this is a new student record or just an update to an existing record
        if($arguments['isUpdate']){
            return;
        }

        $job = $this->defineJob($bean);

        $this->scheduleJob($job);
    }

    /**
     * Define the job that adds a new student to the GradebookFake app
     * @param SugarBean $bean The Student (Contact) bean
     * @return SchedulersJob The job that was defined
     */
    protected function defineJob(\SugarBean $bean)
    {
        //create the new job
        $job = $this->getSchedulersJob();
        //job name
        $job->name = "Add New Student to Gradebook Job";
        //data we are passing to the job
        $job->data = $bean->id;
        //function to call
        $job->target = "class::StudentGradebookJob";
        //set the user the job runs as
        $job->assigned_user_id = $GLOBALS['current_user']->id;

        return $job;
    }

    /**
     * Schedule the job to run by submitting it to the Sugar Job Queue
     * @param SchedulersJob $job The job to submit
     * @return string Response from submitting the job
     */
    protected function scheduleJob(\SchedulersJob $job)
    {
        $jq = $this->getSugarJobQueue();
        return $jq->submitJob($job);
    }

    protected function getSchedulersJob()
    {
        return new SchedulersJob();
    }

    protected function getSugarJobQueue()
    {
        return new SugarJobQueue();
    }
}

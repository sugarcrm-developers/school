<?php

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
    function AddStudentToGradebook(&$bean, $event, $arguments)
    {
        //Check if this is a new student record or just an update to an existing record
        if(!$arguments['isUpdate']){

            require_once('include/SugarQueue/SugarJobQueue.php');

            //create the new job
            $job = new SchedulersJob();
            //job name
            $job->name = "Add New Student to Gradebook Job";
            //data we are passing to the job
            $job->data = $bean->id;
            //function to call
            $job->target = "function::AddStudentToGradebookJob";

            global $current_user;
            //set the user the job runs as
            $job->assigned_user_id = $current_user->id;

            //push into the queue to run
            $jq = new SugarJobQueue();
            $jobid = $jq->submitJob($job);
        }

    }
}

<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class Students_Gradebook
{
    function AddStudentToGradebook(&$bean, $event, $arguments)
    {
        //If this is a new student record, create a new job that adds the student to the GradebookFake app
        // and put the job on the Sugar Job Queue to be executed the next time the Queue is run
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

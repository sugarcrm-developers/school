<?php

use Sugarcrm\Sugarcrm\custom\gradebook_fake\RecordManager;

/**
 * This function defines the job that adds a new student to the GradebookFake app
 * @param $job Information about the job. $job->data should be the id for the new Student (contact) record
 * @return bool true if a record was successfully created in the GradebookFake app
 */
function AddStudentToGradebookJob($job)
{
    if (!empty($job->data))
    {
        $bean = BeanFactory::getBean('Contacts', $job->data);

        //Call the external GradebookFake app to create a new record in it
        $rm = new RecordManager();
        return $rm->createStudentRecord($bean->email1, $bean->first_name, $bean->last_name);
    }

    return false;
}

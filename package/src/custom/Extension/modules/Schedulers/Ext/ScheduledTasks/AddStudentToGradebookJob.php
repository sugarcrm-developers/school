<?php

use Sugarcrm\Sugarcrm\custom\gradebook_fake\RecordManager;


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

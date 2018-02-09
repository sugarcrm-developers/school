<?php

namespace Sugarcrm\Sugarcrm\custom\gradebook_fake;

/**
 * Class RecordManager
 * @package GradebookFake
 * This class manages the records for GradebookFake.
 * No records are actually saved!
 */
class RecordManager
{
    /**
     * This function pretends to create a student record in GradebookFake
     * @param $email
     * @param $firstName
     * @param $lastName
     * @return bool true if the record was created in GradebookFake
     * @throws \Exception if the email address forceerror@example.com is used
     */
    public function createStudentRecord($email, $firstName, $lastName)
    {
        if ($email === 'forceerror@example.com'){
            throw new \Exception("An error was forced because the email address forceerror@example.com was used.");
        }
        return true;
    }
}

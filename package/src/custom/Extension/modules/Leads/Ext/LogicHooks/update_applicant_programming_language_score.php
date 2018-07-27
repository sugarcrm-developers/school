<?php

$hook_array['before_save'][] = Array(
    //Processing index. For sorting the array.
    1,

    //Label. A string value to identify the hook.
    'Update the applicant Programming Language Score field before save',

    //The PHP file where your class is located.
    'custom/modules/Leads/ApplicantProgrammingScore.php',

    //The class the method is in.
    'ApplicantProgrammingScore',

    //The method to call.
    'updateProgrammingScore'
);

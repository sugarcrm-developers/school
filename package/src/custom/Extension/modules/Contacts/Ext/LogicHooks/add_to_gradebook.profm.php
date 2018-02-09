<?php

$hook_array['after_save'][] = Array(
    //Processing index. For sorting the array.
    1,

    //Label. A string value to identify the hook.
    'after_save add student to gradebook',

    //The PHP file where the class is located.
    'custom/modules/Contacts/Students_Gradebook.php',

    //The class the method is in.
    'Students_Gradebook',

    //The method to call.
    'AddStudentToGradebook'
);

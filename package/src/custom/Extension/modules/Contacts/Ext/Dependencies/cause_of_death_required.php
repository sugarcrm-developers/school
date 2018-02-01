<?php
// Copyright 2018 SugarCRM Inc.  Licensed by SugarCRM under the Apache 2.0 license.
/**
 * We use a SetRequired action to make Cause of Death a required field when Student is deceased.
 */
$dependencies['Contacts']['cause_of_death_required'] = array(
    'hooks' => array("edit"),
    'trigger' => 'true',
    'triggerFields' => array('vitals_c'),  // Triggered only when vitals_c changes
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired', //If formula is true, then we make target field required
            'params' => array(
                'target' => 'cause_of_death_c',
                'value' => 'equal($vitals_c, "deceased")'
            )
        ),
    ),
    //Actions fire if the trigger is false. Optional.
    'notActions' => array(),
);

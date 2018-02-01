<?php
// Copyright 2018 SugarCRM Inc.  Licensed by SugarCRM under the Apache 2.0 license.
/**
 * Show death panel when Student is Dead. :-(
 */
$dependencies['Contacts']['show_death_panel'] = array(
    'hooks' => array("edit"),
    'trigger' => 'equal($vitals_c, "deceased")', // Formula to determine if we run actions (true) or notActions (false)
    'triggerFields' => array('vitals_c'),
    'onload' => true,
    'actions' => array(  // Shows panel when deceased
        array(
            'name' => 'SetPanelVisibility', // Action that can set visibility on target panel
            'params' => array(
                'target' => 'death_panel',
                'value' => 'true'
            )
        ),
    ),
    'notActions' => array( // Hides panel when not deceased
        array(
            'name' => 'SetPanelVisibility',
            'params' => array(
                'target' => 'death_panel',
                'value' => 'false'
            )
        ),
    ),
);

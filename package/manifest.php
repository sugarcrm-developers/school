<?php

$manifest = array (
  'built_in_version' => '7.9.0.0',
  'acceptable_sugar_versions' =>
  array (
     '7.9.0.0',
     '7.9.1.0',
     '7.9.0.1',
     '7.9.2.0', // Added at Uncon
     '7.10.0.0',  // Added at Uncon
  ),
  'acceptable_sugar_flavors' =>
  array (
     'PRO',  // Added at Uncon
     'ENT',
     'ULT',
  ),
  'readme' => '',
  'key' => 'SUGR',
  'author' => 'SugarCRM',
  'description' => 'Professor M School for Gifted Coders',
  'icon' => '',
  'is_uninstallable' => true,
  'name' => 'ProfessorM',
  'type' => 'module',
  'version' => 1,
  'remove_tables' => 'prompt',
);


$installdefs = array (
  'id' => 'ProfessorM',
  'beans' =>
  array (

    array (
      'module' => 'PR_Professors',
      'class' => 'PR_Professors',
      'path' => 'modules/PR_Professors/PR_Professors.php',
      'tab' => true,
    ),
  ),


  'copy' =>
  array (





  ),
  'roles' =>
  array (
  ),
);

<?php
/**
 * Copyright 2018 SugarCRM Inc.  Licensed by SugarCRM under the Apache 2.0 license.
 */
if (! defined('sugarEntry') || ! sugarEntry) die('Not A Valid Entry Point');
require_once("modules/Administration/QuickRepairAndRebuild.php");
$randc = new RepairAndClear();

// Clear the theme cache so the Prof M logo will be displayed on the Login screen and the left corner of the footer
$randc->clearThemeCache();

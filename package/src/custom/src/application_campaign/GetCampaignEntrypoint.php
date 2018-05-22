<?php
// Copyright 2018 SugarCRM Inc.  Licensed by SugarCRM under the Apache 2.0 license.
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

use Sugarcrm\Sugarcrm\custom\application_campaign\ApplicationCampaignManager;

$manager = new ApplicationCampaignManager();

echo $manager->getOnlineApplicationsCampaignId();

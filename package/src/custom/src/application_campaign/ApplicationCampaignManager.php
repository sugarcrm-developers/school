<?php
// Copyright 2018 SugarCRM Inc.  Licensed by SugarCRM under the Apache 2.0 license.
namespace Sugarcrm\Sugarcrm\custom\application_campaign;

/**
 * Class ApplicationCampaignManager
 *
 * Manages the Online Applications Campaign
 *
 * @package Sugarcrm\Sugarcrm\custom\application_campaign
 */
class ApplicationCampaignManager
{


    /**
     * Get the most recently updated Campaign with the name 'Online Applications'
     *
     * Used with `getOnlineApplicationsCampaign` public custom entry point
     *
     * @return the id of the Online Applications Campaign
     */
    public function getOnlineApplicationsCampaignId(): string
    {
        // Get the most recently updated Campaign with the name 'Online Applications'
        $q = $this->getSugarQuery();
        $q->select(array('id', 'name'));
        $q->from($this->getNewCampaignBean(), array('team_security' => false));
        $q->where()->equals('name', 'Online Applications');
        $q->orderBy('date_modified', 'desc');
        $q->limit(1);

        $results = $q->execute();

        if(sizeof($results) != 1 || !$results[0]['id']){
            throw new \Exception("Unable to find ID for the Campaign named Online Applications");
        }
        return $results[0]['id'];
    }

    /**
     * Get Sugar Query.
     * This function exists so we can mock the SugarQuery in automated tests.
     *
     * @return \SugarQuery
     */
    protected function getSugarQuery()
    {
        return new \SugarQuery();
    }

    /**
     * Get a new Campaign Bean.
     * This function exists so we can mock the Campaigns Bean in automated tests.
     *
     * @return null|\SugarBean
     */
    protected function getNewCampaignBean()
    {
        return \BeanFactory::newBean('Campaigns');
    }

}

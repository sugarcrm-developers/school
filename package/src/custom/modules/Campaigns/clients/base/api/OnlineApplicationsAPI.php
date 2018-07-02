<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class OnlineApplicationsAPI extends SugarApi
{
    /**
     * Registers a custom API endpoint at /Campaigns/GetOnlineApplicationsCampaignId.
     * The endpoint returns ID of the most recently updated Campaign with the name 'Online Applications'
     * @return array The definition of the custom endpoint
     */
    public function registerApiRest()
    {
        return array(
            //GET
            'onlineApplicationsGet' => array(

                //request type
                'reqType' => 'GET',

                //set authentication
                'noLoginRequired' => true,

                //endpoint path
                'path' => array('Campaigns', 'getOnlineApplicationsCampaignId'),

                //method to call
                'method' => 'getOnlineApplicationsCampaignId',

                //short help string to be displayed in the help documentation
                'shortHelp' => 'Get the ID of the most recently updated Campaign with the name \'Online Applications\'',

                //long help to be displayed in the help documentation
                'longHelp' => 'custom/modules/Campaigns/clients/base/api/help/GetOnlineApplicationsCampaignId.html',
            ),
        );
    }

    /**
     * Get the ID of the most recently updated Campaign with the name 'Online Applications'
     *
     * @return an array containing the ID of the most recently updated Campaign with the name 'Online Applications'
     */
    public function getOnlineApplicationsCampaignId()
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
            throw new \SugarApiExceptionError("Unable to find ID for the Campaign named Online Applications");
        }

        return array('id' => $results[0]['id']);
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

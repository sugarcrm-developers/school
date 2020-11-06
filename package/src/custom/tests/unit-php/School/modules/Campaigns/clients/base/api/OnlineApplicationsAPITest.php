<?php

use Sugarcrm\SugarcrmTestsUnit\TestMockHelper;

require_once 'custom/modules/Campaigns/clients/base/api/OnlineApplicationsAPI.php';

/**
 * @coversDefaultClass \OnlineApplicationsAPI
 */
class OnlineApplicationsAPITest extends \PHPUnit\Framework\TestCase
{

    /**
     * A partial mock of OnlineApplicationsAPI
     */
    private $api;

    /**
     * @var a partial mock of a SugarQuery
     */
    protected $sugarQuery;

    protected function setUp() :void
    {
        parent::setUp();

        $this->where = $this->createMock('SugarQuery_Builder_Where');
        $this->sugarQuery = $this->createMock('SugarQuery');
        $this->sugarQuery->method('where')->willReturn($this->where);

        $this->campaignBean = $this->createMock('SugarBean');

        $this->api = TestMockHelper::createPartialMock($this, 'OnlineApplicationsAPI',
            ['getSugarQuery', 'getNewCampaignBean']);
        $this->api->method('getSugarQuery')->willReturn($this->sugarQuery);
        $this->api->method('getNewCampaignBean')->willReturn($this->campaignBean);
    }

    /**
     * @covers ::getOnlineApplicationsCampaignId
     */
    public function testGetOnlineApplicationsCampaignIdWithResults()
    {
        $this->campaignId = "57f5a666-70c3-11e8-bbb3-34363bc46900";
        $this->queryResults = array(
            0 => array(
                "id" => $this->campaignId,
                "name" => "Online Applications",
                "campaigns__date_modified" => "2018-06-15 17:41:32"
            )
        );
        $this->sugarQuery->method('execute')->willReturn($this->queryResults);

        $this->sugarQuery->expects($this->once())->method('execute');
        $this->assertEquals(array('id' => $this->campaignId), $this->api->getOnlineApplicationsCampaignId());
    }

    /**
     * @covers ::getOnlineApplicationsCampaignId
     */
    public function testGetOnlineApplicationsCampaignIdWithNoResults()
    {
        $this->queryResults = array();
        $this->sugarQuery->method('execute')->willReturn($this->queryResults);

        $this->sugarQuery->expects($this->once())->method('execute');
        $this->expectException(Exception::class, "Unable to find ID for the Campaign named Online Applications");
        $this->api->getOnlineApplicationsCampaignId();
    }

}

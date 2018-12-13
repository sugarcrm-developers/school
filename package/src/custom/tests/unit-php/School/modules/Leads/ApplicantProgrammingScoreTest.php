<?php

use Sugarcrm\SugarcrmTestsUnit\TestMockHelper;
use Sugarcrm\Sugarcrm\custom\Security\Subject\ApplicantProgrammingScore as ApsSubject;

use Sugarcrm\Sugarcrm\Security\Context;

require_once 'custom/modules/Leads/ApplicantProgrammingScore.php';

/**
 * @coversDefaultClass \ApplicantProgrammingScore
 */
class ApplicantProgrammingScoreTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @covers ::updateProgrammingScore
     */
    public function testUpdateProgrammingScore(){
        $aps = new ApplicantProgrammingScore();

        $applicant = TestMockHelper::createMock($this, '\\Lead');
        $applicant->programminglanguages_c="^php^,^javascript^";

        $applicant->expects($this->once())->method('updateCalculatedFields');
        $this->assertNull($applicant->programming_score_c);

        $aps->updateProgrammingScore($applicant, null, null);
        $this->assertEquals(30, $applicant->programming_score_c);
    }

    /**
     * @covers ::updateProgrammingScore
     */
    public function testUpdateProgrammingScoreSecuritySubject(){
        $aps = TestMockHelper::createMock($this, ApplicantProgrammingScore::class);
        $applicant = TestMockHelper::createMock($this, '\\Lead');

        $applicant->expects($this->once())->method('commitAuditedStateChanges');

        $context = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $aps->method('getSecurityContext')->willReturn($context);
//        $context->expects($this->once())->method('activateSubject')->with($this->isInstanceOf(ApsSubject::class));
//        $context->expects($this->once())->method('setAttribute')->with($this->equalTo("aps_calc_version"), $aps->getApsCalcVersion());
//        $context->expects($this->once())->method('deactivateSubject')->with($this->isInstanceOf(ApsSubject::class));

        $aps->updateProgrammingScore($applicant, null, null);

    }


    /**
     * @covers ::getProgrammingScore
     */
    public function testGetProgrammingScoreEmptyArray()
    {
        $aps = new ApplicantProgrammingScore();
        $this->assertEquals(0, $aps -> getProgrammingScore([]));
    }

    /**
     * @covers ::getProgrammingScore
     */
    public function testGetProgrammingScorePHP()
    {
        $aps = new ApplicantProgrammingScore();
        $this->assertEquals(15, $aps -> getProgrammingScore(['^php^']));
    }

    /**
     * @covers ::getProgrammingScore
     */
    public function testGetProgrammingScoreJava()
    {
        $aps = new ApplicantProgrammingScore();
        $this->assertEquals(5, $aps -> getProgrammingScore(['^java^']));
    }

    /**
     * @covers ::getProgrammingScore
     */
    public function testGetProgrammingScoreAllLangugages()
    {
        $aps = new ApplicantProgrammingScore();
        $this->assertEquals(60, $aps -> getProgrammingScore(['^php^', '^javascript^', '^net^', '^java^', '^c^', '^go^', '^python^', '^ruby^']));
    }

    /**
     * @covers ::getProgrammingScore
     */
    public function testGetProgrammingScoreUnknownLangugages()
    {
        $levels = \LoggerManager::getLoggerLevels();
        $levels = array_keys($levels);
        $GLOBALS['log'] = $this->createPartialMock(\stdClass::class, $levels);
        $GLOBALS['log']->expects($this->once())->method('warn');

        $aps = new ApplicantProgrammingScore();
        $this->assertEquals(5, $aps -> getProgrammingScore(['^unknown^', '^java^']));
    }

}

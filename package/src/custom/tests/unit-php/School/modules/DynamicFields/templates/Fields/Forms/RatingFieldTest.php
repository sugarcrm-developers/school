<?php

use Sugarcrm\SugarcrmTestsUnit\TestMockHelper;

require_once 'custom/modules/DynamicFields/templates/Fields/Forms/Ratingfield.php';

/**
 * @coversDefaultClass \Ratingfield
 */
class RatingFieldTest extends \PHPUnit\Framework\TestCase {

    // The default color for the Rating field
    private $defaultColor;

    protected function setUp() :void
    {
        parent::setUp();

        $this->defaultColor = '#ffd203';
    }

    /**
     * @covers ::get_body
     */
    public function testGetBody(){

        $template = 'template for Rating.tpl';
        $ss =  TestMockHelper::createMock($this, '\\Sugar_Smarty');
        $ss->method('fetch')->willReturn($template);

        $vardef = array(
            "duplicate_merge_dom_value" => 0,
            "labelValue" => "Application Rating",
            "calculated" => 1,
            "formula" => "add(multiply(\$gpa_c,10),\$programming_score_c)",
            "enforced" => 1,
            "dependency" => null,
            "required" => null,
            "source" => "custom_fields",
            "name" => "rating_c",
            "vname" => "LBL_RATING",
            "type" => "Ratingfield",
            "massupdate" => null,
            "no_default" => null,
            "comments" => null,
            "help" => null,
            "importable" => false,
            "duplicate_merge" => "disabled",
            "audited" => null,
            "reportable" => 1,
            "unified_search" => null,
            "merge_filter" => "disabled",
            "pii" => null,
            "size" => 20,
            "color" => "#f258ff",
            "dbType" => "varchar",
            "id" => "Leadsrating_c",
            "custom_module" => "Leads"
        );

        $ss->expects($this->once())->method('assign')->with('COLOR', '#f258ff');

        $this->assertEquals($template, get_body($ss, $vardef));
    }

    /**
     * @covers ::getColor
     */
    public function testGetColorWhenColorIsSpecified(){

        $vardef = array(
            "color" => "#f258ff"
        );
        $this->assertEquals("#f258ff", getColor($vardef));
    }

    /**
     * @covers ::getColor
     */
    public function testGetColorWhenColorIsEmptyString(){

        $vardef = array(
            "color" => ''
        );
        $this->assertEquals($this->defaultColor, getColor($vardef));
    }

    /**
     * @covers ::getColor
     */
    public function testGetColorEmptyVardefs(){

        $vardef = array();
        $this->assertEquals($this->defaultColor, getColor($vardef));
    }
}

<?php

use Sugarcrm\SugarcrmTestsUnit\TestMockHelper;

require_once 'custom/modules/DynamicFields/templates/Fields/TemplateRatingfield.php';

/**
 * @coversDefaultClass \TemplateRatingfield
 */
class TemplateRatingFieldTest extends \PHPUnit\Framework\TestCase {

    /**
     * @covers ::get_field_def
     */
    public function testGetFieldDefDefaults(){
        $trf = new TemplateRatingfield();
        $def = $trf->get_field_def();
        $this->assertEquals(NULL, $def['color']);
        $this->assertEquals('varchar', $def['dbType']);
    }

    /**
     * @covers ::get_field_def
     */
    public function testGetFieldDefColorSet(){
        $trf = new TemplateRatingfield();
        $trf->color="#ffffff";
        $def = $trf->get_field_def();
        $this->assertEquals("#ffffff", $def['color']);
        $this->assertEquals('varchar', $def['dbType']);
    }

    /**
     * @covers ::get_field_def
     */
    public function testGetFieldDefExt1Set(){
        $trf = new TemplateRatingfield();
        $trf->ext1="#000000";
        $def = $trf->get_field_def();
        $this->assertEquals("#000000", $def['color']);
        $this->assertEquals('varchar', $def['dbType']);
    }

    /**
     * @covers ::get_field_def
     */
    public function testGetFieldDefColorAndExt1Set(){
        $trf = new TemplateRatingfield();
        $trf->color="#ffffff";
        $trf->ext1="#000000";
        $def = $trf->get_field_def();
        $this->assertEquals("#ffffff", $def['color']);
        $this->assertEquals('varchar', $def['dbType']);
    }

}

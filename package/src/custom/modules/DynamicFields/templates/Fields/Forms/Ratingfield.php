<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('custom/modules/DynamicFields/templates/Fields/TemplateRatingfield.php');

/**
 * Implement get_body function to correctly populate the template for the ModuleBuilder/Studio
 * Add field page.
 *
 * @param Sugar_Smarty $ss
 * @param array $vardef
 *
 */
function get_body(&$ss, $vardef)
{
    $ss->assign('COLOR', getColor($vardef));

    return $ss->fetch('custom/modules/DynamicFields/templates/Fields/Forms/Rating.tpl');
}

/**
 * Get the color for the stars in the Rating field.  Will return the color stored in $vardef array if it exists.
 * Otherwise, will return the default color.
 * @param array $vardef
 * @return string The color for the stars in the Rating field
 */
function getColor($vardef)
{
    if (isset($vardef['color']) && !empty($vardef['color'])) {
        $color = $vardef['color'];
    } else {
        $color = '#ffd203';
    }
    return $color;
}

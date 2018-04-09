<?php

# Script taken from https://github.com/sugarcrm/IPRestrictionManager/blob/master/.travis/cliSilentInstall.php

$_REQUEST = array('goto' => 'SilentInstall', 'cli' => 'true');
require('install.php');

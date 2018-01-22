#!/usr/bin/env php

# Run this script with the following options:
# ./pack.php -v versionNameOrNumber
# ./pack.php -v versionNameOrNumber -w lengthOfWindowsSugarDirectoryPath
#
# -w lengthOfWindowsSugarDirectoryPath: This is an optional flag that can be used to generate a build specifically for
# Windows.  Input the length of the Sugar Directory Path of the Windows installation.  The script will generate two zips:
#     1. sugarcrm-ProfessorM-versionNameOrNumber-windows.zip: the module loadable package to be installed
#     2. sugarcrm-ProfessorM-hello-versionNameOrNumber-manual-install.zip: a zip containing files to be manually installed


<?php
// Copyright 2017 SugarCRM Inc.  Licensed by SugarCRM under the Apache 2.0 license.
require("../vendor/autoload.php");

use Sugarcrm\ProfessorM\PackageGenerator;


/*
 * Set these variables!
 */

$packageID = "ProfessorM";
$packageLabel = "Professor M School for Gifted Coders";
$supportedVersionRegex = '7\\..*$';

/*
 * Determine the version of the zip
 */
$pg = new PackageGenerator;

$options = getopt("v:w:");
if (!array_key_exists('v', $options)){
    die("Indicate version number by running script with -v. Example: ./pack.php -v 1.0\n");
}
$version = $options['v'];

/*
 * Prepare the manifest and installdefs
 */

$manifest = array(
    'id' => $packageID,
    'name' => $packageLabel,
    'description' => $packageLabel,
    'version' => $version,
    'author' => 'SugarCRM, Inc.',
    'is_uninstallable' => 'true',
    'published_date' => date("Y-m-d H:i:s"),
    'type' => 'module',
    'acceptable_sugar_versions' => array(
        'exact_matches' => array(
        ),
        'regex_matches' => array(
            $supportedVersionRegex,
        ),
    ),
);
$installdefs = array(
    'beans' =>
        array (
            array (
                'module' => 'PR_Professors',
                'class' => 'PR_Professors',
                'path' => 'modules/PR_Professors/PR_Professors.php',
                'tab' => true,
            ),
        ),
    'language' => array (
        array (
            'from' => 'language/application/en_us.lang.php',
            'to_module' => 'application',
            'language' => 'en_us',
        ),
    ),
    'image_dir' => 'icons',
    'layoutdefs' =>
        array (
            array (
                'additional_fields' =>
                    array (
                        'Leads' => 'pr_professors_leads_name',
                    ),
            ),
        ),
    'id' => $packageID,
    'relationships' =>
        array (
            array (
                'meta_data' => 'metadata/pr_professors_accountsMetaData.php',
            ),
            array (
                'meta_data' => 'metadata/pr_professors_leadsMetaData.php',
            ),
            array (
                'meta_data' => 'metadata/pr_professors_contactsMetaData.php',
            ),
        ),
    'custom_fields' =>
        array (
            'Accountsratesg_c' =>
                array (
                    'id' => 'Accountsratesg_c',
                    'name' => 'ratesg_c',
                    'label' => 'LBL_RATESG',
                    'comments' => NULL,
                    'help' => NULL,
                    'module' => 'Accounts',
                    'type' => 'enum',
                    'max_size' => '100',
                    'require_option' => '0',
                    'default_value' => 'A Plus',
                    'date_modified' => '2017-08-08 03:35:04',
                    'deleted' => '0',
                    'audited' => '0',
                    'mass_update' => '1',
                    'duplicate_merge' => '1',
                    'reportable' => '1',
                    'importable' => 'true',
                    'ext1' => 'grading_list',
                    'ext2' => NULL,
                    'ext3' => NULL,
                    'ext4' => NULL,
                ),
            'Accountsstatus_c' =>
                array (
                    'id' => 'Accountsstatus_c',
                    'name' => 'status_c',
                    'label' => 'LBL_STATUS',
                    'comments' => NULL,
                    'help' => NULL,
                    'module' => 'Accounts',
                    'type' => 'enum',
                    'max_size' => '100',
                    'require_option' => '1',
                    'default_value' => 'Active',
                    'date_modified' => '2017-08-08 03:21:32',
                    'deleted' => '0',
                    'audited' => '0',
                    'mass_update' => '1',
                    'duplicate_merge' => '1',
                    'reportable' => '1',
                    'importable' => 'true',
                    'ext1' => 'account_status_list',
                    'ext2' => NULL,
                    'ext3' => NULL,
                    'ext4' => NULL,
                ),
            'Contactsalias_c' =>
                array (
                    'id' => 'Contactsalias_c',
                    'name' => 'alias_c',
                    'label' => 'LBL_ALIAS',
                    'comments' => NULL,
                    'help' => NULL,
                    'module' => 'Contacts',
                    'type' => 'varchar',
                    'max_size' => '255',
                    'require_option' => '0',
                    'default_value' => NULL,
                    'date_modified' => '2017-08-07 23:15:53',
                    'deleted' => '0',
                    'audited' => '0',
                    'mass_update' => '0',
                    'duplicate_merge' => '1',
                    'reportable' => '1',
                    'importable' => 'true',
                    'ext1' => NULL,
                    'ext2' => NULL,
                    'ext3' => NULL,
                    'ext4' => NULL,
                ),
            'Contactsstatus_c' =>
                array (
                    'id' => 'Contactsstatus_c',
                    'name' => 'status_c',
                    'label' => 'LBL_STATUS',
                    'comments' => NULL,
                    'help' => NULL,
                    'module' => 'Contacts',
                    'type' => 'enum',
                    'max_size' => '100',
                    'require_option' => '1',
                    'default_value' => 'Prospective Student',
                    'date_modified' => '2017-08-07 23:24:52',
                    'deleted' => '0',
                    'audited' => '0',
                    'mass_update' => '1',
                    'duplicate_merge' => '1',
                    'reportable' => '1',
                    'importable' => 'true',
                    'ext1' => 'contact_type_list',
                    'ext2' => NULL,
                    'ext3' => NULL,
                    'ext4' => NULL,
                ),
            'Leadsalias_c' =>
                array (
                    'id' => 'Leadsalias_c',
                    'name' => 'alias_c',
                    'label' => 'LBL_ALIAS',
                    'comments' => NULL,
                    'help' => NULL,
                    'module' => 'Leads',
                    'type' => 'varchar',
                    'max_size' => '255',
                    'require_option' => '0',
                    'default_value' => NULL,
                    'date_modified' => '2017-08-07 22:50:43',
                    'deleted' => '0',
                    'audited' => '1',
                    'mass_update' => '0',
                    'duplicate_merge' => '1',
                    'reportable' => '1',
                    'importable' => 'true',
                    'ext1' => NULL,
                    'ext2' => NULL,
                    'ext3' => NULL,
                    'ext4' => NULL,
                ),
        ),
);

/*
 * Make the zip
 */

try {
    $isWindowsBuild = false;
    $lengthOfWindowsSugarDirectoryPath = null;
    if (array_key_exists('w', $options)){
        $isWindowsBuild = true;
        $lengthOfWindowsSugarDirectoryPath = (int)$options['w'];
    }
    $zip = $pg -> generateZip($version, $packageID, $argv[0], "src", $manifest, $installdefs, $isWindowsBuild,
        $lengthOfWindowsSugarDirectoryPath);
} catch (Exception $e) {
    die($e->getMessage());
}

exit(0);

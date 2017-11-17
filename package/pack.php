#!/usr/bin/env php
<?php
// Copyright 2016 SugarCRM Inc.  Licensed by SugarCRM under the Apache 2.0 license.

use Sugarcrm\ProfessorM\PackageGenerator;

require('PackageGenerator.php');

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

$version = $pg -> getVersion($argv[1]);

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
    $zip = $pg -> openZip($version, $packageID, $argv[0]);
} catch (Exception $e) {
    die($e->getMessage());
}

$srcDirectory = "src";
$arrayOfFiles = $pg -> getFileArraysForZip($srcDirectory );
$filesToInclude = $arrayOfFiles["filesToInclude"];
$filesToExclude = $arrayOfFiles["filesToExclude"];

foreach($filesToInclude as $file) {
    echo " [*] " . $file['fileRelative'] . "\n";
    $zip->addFile($file['fileReal'], $file['fileRelative']);
    $installdefs['copy'][] = array(
        'from' => '<basepath>/' . $file['fileRelative'],
        'to' => preg_replace('/^' . $srcDirectory .'\/(.*)/', '$1', $file['fileRelative']),
    );
}

$manifestContent = sprintf(
    "<?php\n\$manifest = %s;\n\$installdefs = %s;\n",
    var_export($manifest, true),
    var_export($installdefs, true)
);
$zip->addFromString('manifest.php', $manifestContent);
$zip->close();
echo "Done creating {$zipFile}\n\n";

if (!empty($filesToExclude)){
    echo "The following files were excluded from the zip: \n";
    foreach($filesToExclude as $file) {
        echo " [*] " . $file["fileRelative"] . "\n";
    }
}
exit(0);

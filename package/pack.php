#!/usr/bin/env php
<?php
// Copyright 2016 SugarCRM Inc.  Licensed by SugarCRM under the Apache 2.0 license.


$packageID = "ProfessorM";
$packageLabel = "Professor M School for Gifted Coders";
$supportedVersionRegex = '7\\..*$';
/******************************/

if (empty($argv[1])) {
    if (file_exists("version")) {
        $version = file_get_contents("version");
    }
} else {
    $version = $argv[1];
}

if (empty($version)){
    die("Use $argv[0] [version]\n");
}

$id = "{$packageID}-{$version}";

$directory = "releases";
if(!is_dir($directory)){
    mkdir($directory);
}

$zipFile = $directory . "/sugarcrm-{$id}.zip";


if (file_exists($zipFile)) {
    die("Error:  Release $zipFile already exists, so a new zip was not created. To generate a new zip, either delete the"
        . " existing zip file or update the version number in the version file AND then run the script to build the"
        . " module again. \n");
}

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
echo "Creating {$zipFile} ... \n";
$zip = new ZipArchive();
$zip->open($zipFile, ZipArchive::CREATE);
$basePath = realpath('src/');
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ($files as $name => $file) {
    if ($file->isFile()) {
        $fileReal = $file->getRealPath();
        $fileRelative = 'src' . str_replace($basePath, '', $fileReal);
        echo " [*] $fileRelative \n";
        $zip->addFile($fileReal, $fileRelative);
        $installdefs['copy'][] = array(
            'from' => '<basepath>/' . $fileRelative,
            'to' => preg_replace('/^src\/(.*)/', '$1', $fileRelative),
        );
    }
}
$manifestContent = sprintf(
    "<?php\n\$manifest = %s;\n\$installdefs = %s;\n",
    var_export($manifest, true),
    var_export($installdefs, true)
);
$zip->addFromString('manifest.php', $manifestContent);
$zip->close();
echo "Done creating {$zipFile}\n\n";
exit(0);
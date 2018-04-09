#!/usr/bin/env php
<?php

# Script taken from https://github.com/sugarcrm/IPRestrictionManager/blob/master/.travis/cliModuleInstall.php

function output_msg($msg)
{
    $full_msg = $msg . "\n";
    echo $full_msg; //To stdout
}
function output_error($msg)
{
    $full_msg = $msg . "\n";
    fwrite(STDERR, $full_msg); //To stderr so Nannybot gets it and it's passed back to Mothership
    echo $full_msg; // Also to STDOUT in order to be received by Nannybot
    exit(1); // Added the exit code here instead of multiple "return 1" accross the code
}
function usage()
{
    $u_msg_4 = "install usage: -i /path/to/instance -z /path/to/zipfile\n";
    $u_msg_5 = "  [optional] -e /path/to/expanded/zip/dir\n";
    $u_msg_6 = 'uninstall usage: -i /path/to/instance -u "module name"';
    output_error($u_msg_4 . $u_msg_5 . $u_msg_6);
}
function get_admin_user()
{
    $user = new User();
    $user->getSystemUser();
    $user->is_admin = 1;
    return $user;
}
function unzip_package($zip_file)
{
    global $sugar_config;
    $base_tmp_upgrade_dir = rtrim($sugar_config['upload_dir'], "/") . '/upgrades/temp';
    if (!is_dir($base_tmp_upgrade_dir)) {
        mkdir($base_tmp_upgrade_dir, 0777, true);
    }
    $unzip_dir = mk_temp_dir($base_tmp_upgrade_dir);
    $zip = new ZipArchive();
    $res = $zip->open($zip_file);
    if ($res === true) {
        $zip->extractTo($unzip_dir);
        $zip->close();
    } else {
        output_error('Failed to extract Zip file ' . $zip_file);
    }
    return $unzip_dir;
}
function perform_module_install($opts)
{
    global $sugar_config, $mod_strings, $current_language, $current_user;
    if ($opts['zip_file'] !== false && !is_readable($opts['zip_file'])) {
        output_error("Could not read zip file ${opts['zip_file']}.");
    }
    if ($opts['zip_file'] !== false && !array_key_exists('e', $opts)) {
        $opts['expanded_zip'] = unzip_package($opts['zip_file']);
    }
    if (!is_dir($opts['expanded_zip'])) {
        output_error("${opts['expanded_zip']} is not a directory.");
    }
    if (!is_readable($opts['expanded_zip'] . '/manifest.php')) {
        output_error("${opts['expanded_zip']} is not a valid expanded module. No readable manifest.php file.");
    }
    require_once $opts['expanded_zip'] . '/manifest.php';
    if (!is_array($manifest)) {
        output_error('Sourced manifest but $manifest was not set.');
    }
    if (!array_key_exists('name', $manifest) || $manifest['name'] == '') {
        output_error("Manifest doesn't specify a name.");
    }
    $upload = array_key_exists('upload_dir', $sugar_config) ? $sugar_config['upload_dir'] : 'upload/';
    $local_zip_file = rtrim($opts['instance_path'], '/') . '/' . rtrim($upload, '/') . '/upgrades/module/' . basename($opts['zip_file']);
    $current_user = get_admin_user();
    // Initialize the module installer
    $modInstaller = new ModuleInstaller();
    $modInstaller->silent = true;  //shuts up the javscript progress bar
    // Disable packageScanner
    $sugar_config['moduleInstaller']['packageScan'] = false;
    // Squelch some warnings
    $GLOBALS['app_list_strings']['moduleList'] = array();
    // The REQUEST['install_file'] variable is used to create the -restore directory
    $_REQUEST['install_file'] = urlencode($upload . '/upgrades/module/' . basename($local_zip_file));
    // Check for already installed
    $new_upgrade = new UpgradeHistory();
    $new_upgrade->name = $manifest['name'];
    $new_upgrade->version = array_key_exists('version', $manifest) ? $manifest['version'] : '';
    $new_upgrade->md5sum = md5_file($opts['zip_file']);
    $installed = $new_upgrade->checkForExisting($new_upgrade);
    if ($installed !== null) {
        if ($installed->version === $new_upgrade->version && $installed->md5sum === $new_upgrade->md5sum) {
            output_msg('Already installed at this version.');
            return 0;
        } else {
            output_msg('Upgrade: Removing installed version: ' . $installed->version . ' md5sum: ' . $installed->md5sum);
            perform_module_uninstall($installed, true);
        }
    }
    //output_msg("Copying {$opts['zip_file']} into {$local_zip_file}.");
    //copy($opts['zip_file'], $local_zip_file);
    // Start installation
    output_msg("Patching ${opts['instance_path']}.");
    $modInstaller->install($opts['expanded_zip']);
    $to_serialize = array(
        'manifest' => $manifest,
        'installdefs' => isset($installdefs) ? $installdefs : array(),
        'upgrade_manifest' => isset($upgrade_manifest) ? $upgrade_manifest : array(),
    );
    output_msg('Adding UpgradeHistory object.');
    $new_upgrade->filename = $local_zip_file;
    $new_upgrade->type = array_key_exists('type', $manifest) ? $manifest['type'] : 'module';
    $new_upgrade->status = 'installed';
    $new_upgrade->author = array_key_exists('author', $manifest) ? $manifest['author'] : '';
    $new_upgrade->description = array_key_exists('description', $manifest) ? $manifest['description'] : '';
    $new_upgrade->id_name = array_key_exists('id', $installdefs) ? $installdefs['id'] : '';
    $new_upgrade->manifest = base64_encode(serialize($to_serialize));
    $new_upgrade->save();
    output_msg('Installed: version: ' . $new_upgrade->version . ' md5sum: ' . $new_upgrade->md5sum);
    return 0;
}
function perform_module_uninstall($package_record, $upgrade = false)
{
    global $current_user;
    $current_user = get_admin_user();
    if (is_file($package_record->filename)) {
        output_msg('Uninstalling ' . $package_record->filename);
        $abspath = realpath($package_record->filename);
        $unzip_dir = unzip_package($abspath);
        if (!is_dir($unzip_dir)) {
            return 1;
        }
        if (!$upgrade) {
            if (!isset($GLOBALS['mi_remove_tables'])) {
                $GLOBALS['mi_remove_tables'] = true;
            }
        }
        $modInstaller = new ModuleInstaller();
        $modInstaller->silent = true;
        $modInstaller->uninstall($unzip_dir);
        output_msg('Removing module entry.');
        $package_record->delete();
        if (defined('SUGAR_SHADOW_PATH')) {
            if (substr($abspath, 0, strlen(SUGAR_SHADOW_PATH)) === SUGAR_SHADOW_PATH) {
                @unlink(remove_file_extension($abspath) . '-manifest.php');
                @unlink($abspath);
            }
        } else {
            @unlink(remove_file_extension($package_record->filename) . '-manifest.php');
            @unlink($package_record->filenamefilename);
        }
    } else {
        output_error('Zip file was not found at ' . $package_record->filename . '. Aborting.');
    }
    return 0;
}

$opts = getopt('i:p:u:z:h');
if (!$opts || array_key_exists('h', $opts)) {
    usage();
}

$opts['expanded_zip'] = array_key_exists('e', $opts) ? $opts['e'] : false;
$opts['instance_path'] = array_key_exists('i', $opts) ? $opts['i'] : false;
$opts['uninstall_module'] = array_key_exists('u', $opts) ? $opts['u'] : false;
$opts['zip_file'] = array_key_exists('z', $opts) ? $opts['z'] : false;
if (!$opts['instance_path']) {
    usage();
}
if (($opts['zip_file'] && $opts['uninstall_module']) || (!$opts['zip_file'] && !$opts['uninstall_module'])) {
    usage();
}

// This must be called in order for the below 'require' directives to work in
// a Shadow environment.
//$shadow_root = getenv('SHADOW_ROOT');
$document_root = getenv('DOCUMENT_ROOT');

//chdir($shadow_root);
//shadow($shadow_root, $document_root, array('cache', 'upload', 'config.php', 'config_override.php'));
if (!@chdir($opts['instance_path'])) {
    output_error("Failed to chdir to ${opts['instance_path']}.");
}
if (!is_readable('config.php')) {
    output_error('Could not read config.php.');
}

// Initialize
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}
require rtrim($opts['instance_path'], '/') . '/config.php';
require_once rtrim($opts['instance_path'], '/') . '/include/entryPoint.php';
require_once rtrim($opts['instance_path'], '/') . '/ModuleInstall/ModuleInstaller.php';

if ($opts['zip_file'] !== false) {
    perform_module_install($opts);
} else {
    $name = $opts['uninstall_module'];
    $uh = new UpgradeHistory();
    $uh->name = $name;
    $module_record = $uh->checkForExisting($uh);
    if (!empty($module_record)) {
        perform_module_uninstall($module_record);
    } else {
        // Added a second try using id_name in case it matches the Name (or for the case that it can be supplied)
        $uh->id_name = $name;
        $module_record = $uh->checkForExisting($uh);
        if (!empty($module_record)) {
            perform_module_uninstall($module_record);
        } else {
            output_error('Did not find the ' . $name . ' module. Unable to uninstall.');
        }
    }
}
?>


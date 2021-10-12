#!/usr/bin/env bash

######################################################################
# Variables
######################################################################

if [[ -z "$1" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Path to where the Sugar files are stored

        For example: ./InstallSugarAndProfM.sh workspace/sugardocker/data/app/sugar"
    exit 1
fi

# The path to where the unzipped Sugar files are stored
sugarDirectory=$1


######################################################################
# Setup
######################################################################

# Copy the config file that the Install will use to the Sugar directory
cp config_si.php $sugarDirectory/config_si.php

# Copy the config override
cp config_override.php $sugarDirectory/config_override.php

# Copy the silent install script to the Sugar directory
cp cliSilentInstall.php $sugarDirectory/cliSilentInstall.php

# Copy the module installer to the Sugar directory
cp cliModuleInstall.php $sugarDirectory/cliModuleInstall.php

# Copy the Professor M module loadable package to the Sugar directory
cp ../package/releases/sugarcrm-ProfessorM-*-standard.zip $sugarDirectory/sugarcrm-ProfessorM-standard.zip

# Set permission for when sudo is required and is not required. Output of these commands will not be printed.
chmod -R 777 $sugarDirectory &> /dev/null
sudo chmod -R 777 $sugarDirectory &> /dev/null


######################################################################
# Add license key to config_si.php
######################################################################

# If the SUGAR_LICENSE_KEY environment variable is set, we will update config_si.php to include the license key
if [[ -n $SUGAR_LICENSE_KEY ]]
then
    # This regular expression searches for the following:
    #
    # array (
    #     '
    #
    # And adds a line with the license key after it
    #
    # array (
    #     'setup_license_key' => 'MySugarLicenseKey',
    #     '
    #
    echo "Updating $sugarDirectory/config_si.php to include the license key. Output and errors from this command are
suppressed to keep the license key private..."
    perl -0777 -i -pe "s#(array \(\n *')#\1setup_license_key' => '$SUGAR_LICENSE_KEY',\n    '#g" $sugarDirectory/config_si.php &> /dev/null || exit 1
else
    echo "WARNING: The SUGAR_LICENSE_KEY environment variable was not set.  Tests that require a license key (for
    example, the Postman tests) will not be able to run."
fi


######################################################################
# Install Sugar
######################################################################

echo "Installing Sugar..."
# Install Sugar using the configs in config_si.php
docker exec sugar-web1 bash -c "php cliSilentInstall.php"


######################################################################
# Install the Professor M Module Loadable Package
######################################################################

echo "Installing the Professor M module loadable package..."
docker exec sugar-web1 bash -c "php cliModuleInstall.php -i $sugarDirectory -z sugarcrm-ProfessorM-standard.zip"

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

# Update the permissions for the Sugar directory
sudo chmod -R 777 $sugarDirectory -q


######################################################################
# Install Sugar
######################################################################

echo "Installing Sugar..."
# Install Sugar using the configs in config_si.php
docker exec sugar-web1 php cliSilentInstall.php
echo "Finished installing Sugar."


######################################################################
# Install the Professor M Module Loadable Package
######################################################################

echo "Installing the Professor M module loadable package..."
docker exec sugar-web1 php cliModuleInstall.php -i . -z sugarcrm-ProfessorM-standard.zip
echo "Finished installing the Professor M module loadable package."

#!/usr/bin/env bash

# This script runs the Postman tests stored in the data directory.
# Prior to running these tests, you must have an installed copy of Sugar that has a valid license key.

######################################################################
# Variables
######################################################################

if [[ -z "$1" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Path to where the Sugar files are stored

        For example: ./InstallProfessorMPackage.sh workspace/sugardocker/data/app/sugar"
    exit 1
fi

# The path to where the unzipped Sugar files are stored
sugarDirectory=$1


######################################################################
# Setup
######################################################################

# Pull the Newman Docker container that we will use to run the tests
docker pull postman/newman_ubuntu1404

dataDirectoryPath=$(pwd)/../data

# Copy the Professor M module loadable package to the Sugar directory
cp ../package/releases/sugarcrm-ProfessorM-*-standard.zip $dataDirectoryPath/sugarcrm-ProfessorM-standard.zip

# Set permission for when sudo is required and is not required. Output of these commands will not be printed.
chmod -R 777 . &> /dev/null
sudo chmod -R 777 . &> /dev/null

# If we are currently running inside of a Docker container (for example, if we are running this script inside of Jenkins
# that is running inside a Docker container), we need to update the network in order for the tests to be able to access
# the Sugar URLs
# currentDockerContainer="$(cat /etc/hostname)"
# if [[ -n $currentDockerContainer && $currentDockerContainer != *"travis-job"* ]]
# then
    # network="sugar11_default"
    # echo "Updating the Docker network ($network) [$currentDockerContainer]..."
    # docker network connect $network $currentDockerContainer
# fi

# Store the path to the 'data' directory. If we are currently running inside of a Docker container (for example, if we
# are running this script inside of Jenkins that is running inside a Docker container), we need to directory path to be
# the path to the 'data' directory on the host machine. The $WORKSPACE_PATH environment variable should be set to the
# workspace directory on the host machine where the files in this repo are stored when this script is being run inside
# of a Docker container.
# if [[ -n $WORKSPACE_PATH ]]
# then
#     dataDirectoryPath=$WORKSPACE_PATH/data
# else
    # dataDirectoryPath=$(pwd)/../data
# fi


######################################################################
# Install the Professor M Module Loadable Package
######################################################################

echo "Installing the Professor M module loadable package..."

# Run the tests that work with all editions of Sugar
docker run -v $dataDirectoryPath:/etc/newman --network="sugar11_default" -t postman/newman_ubuntu1404 run "ProfessorM_PostmanModuleInstall.json" --environment="ProfessorM_PostmanEnvironment.json" --color off

# If the tests return 1, at least one of the tests failed, so we will exit the script with error code 1.
if [[ $? -eq 1 ]]
then
    exit 1
fi

echo "Done Installing the Professor M module loadable package..."

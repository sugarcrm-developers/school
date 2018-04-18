#!/usr/bin/env bash

# This script runs the Postman tests stored in the data directory.
# Prior to running these tests, you must have an installed copy of Sugar that has a valid license key.


######################################################################
# Variables
######################################################################

if [[ -z "$1" ]] || [[ -z "$2" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Sugar version (Example: 7.11)
        2: Path to where the Sugar files are stored

        For example: ./RunPostmanTests.sh 7.11 workspace/sugardocker/data/app/sugar"
    exit 1
fi

# The Sugar version
sugarVersion=$1

# The path to where the unzipped Sugar files are stored
sugarDirectory=$2


######################################################################
# Setup
######################################################################

# Pull the Newman Docker container that we will use to run the tests
docker pull postman/newman_ubuntu1404

# Set permission for when sudo is required and is not required. Output of these commands will not be printed.
chmod -R 777 . &> /dev/null
sudo chmod -R 777 . &> /dev/null

# If we are currently running inside of a Docker container (for example, if we are running this script inside of Jenkins
# that is running inside a Docker container), we need to update the network in order for the tests to be able to access
# the Sugar URLs
currentDockerContainer="$(cat /etc/hostname)"
if [[ -n $currentDockerContainer && $currentDockerContainer != *"travis-job"* ]]
then
    if [[ "$sugarVersion" == "7.10" || "$sugarVersion" == "7.11" ]]
    then
        network="sugar710_default"
    elif [[ "$sugarVersion" == "7.9" ]]
    then
        network="sugar79_default"
    else
        echo "Unable to identify network for Sugar version $sugarVersion"
        exit 1
    fi

    echo "Updating the Docker network ($network)..."
    docker network connect $network $currentDockerContainer
fi

# Store the path to the 'data' directory. If we are currently running inside of a Docker container (for example, if we
# are running this script inside of Jenkins that is running inside a Docker container), we need to directory path to be
# the path to the 'data' directory on the host machine. The $WORKSPACE_PATH environment variable should be set to the
# workspace directory on the host machine where the files in this repo are stored when this script is being run inside
# of a Docker container.
if [[ -n $WORKSPACE_PATH ]]
then
    dataDirectoryPath=$WORKSPACE_PATH/data
else
    dataDirectoryPath=$(pwd)/../data
fi


######################################################################
# Run the Postman tests
######################################################################

docker run -v $dataDirectoryPath:/etc/newman --net="host" -t postman/newman_ubuntu1404 run "ProfessorM_PostmanCollection.json" --environment="ProfessorM_PostmanEnvironment.json" --no-color

# If the tests return 1, at least one of the tests failed, so we will exit the script with error code 1.
if [[ $? -eq 1 ]]
then
    exit 1
fi


######################################################################
# Cleanup
######################################################################

if [[ -n $currentDockerContainer && $currentDockerContainer != *"travis-job"* ]]
then
    docker network disconnect $network $currentDockerContainer
fi

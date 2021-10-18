#!/usr/bin/env bash

# This script runs the Postman tests stored in the data directory.
# Prior to running these tests, you must have an installed copy of Sugar that has a valid license key.


######################################################################
# Variables
######################################################################

if [[ -z "$1" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Docker stack to be used

        For example: ./RunPostmanTests.sh sugar11"
    exit 1
fi

# Docker stack to be used
stackVersion=$1

######################################################################
# Setup
######################################################################

# Pull the Newman Docker container that we will use to run the tests
docker pull postman/newman_ubuntu1404

dataDirectoryPath=$(pwd)/../data

# Set permission for when sudo is required and is not required. Output of these commands will not be printed.
chmod -R 777 . &> /dev/null
sudo chmod -R 777 . &> /dev/null

######################################################################
# Run the Postman tests
######################################################################
echo "Running Professor M Postman tests..."

# Run the tests that work with all editions of Sugar
docker run -v $dataDirectoryPath:/etc/newman --network="$stackVersion_default" postman/newman_ubuntu1404 run "ProfessorM_PostmanCollection.json" --environment="ProfessorM_PostmanEnvironment.json" --color off --reporters="cli"

# If the tests return 1, at least one of the tests failed, so we will exit the script with error code 1.
if [[ $? -eq 1 ]]
then
    exit 1
fi

# Run the Advanced Workflow tests, which only work with Ent and Ult
#if [[ "$sugarEdition" == "Ent" || "$sugarEdition" == "Ult" ]]
#    then
#        docker run -v $dataDirectoryPath:/etc/newman --net="host" -t postman/newman_ubuntu1404 run "ProfessorM_PostmanCollection_AdvancedWorkflow.json" --environment="ProfessorM_PostmanEnvironment.json" --color off
#
#        if [[ $? -eq 1 ]]
#        then
#            exit 1
#        fi
#fi


echo "Done Running Professor Professor M Postman tests..."

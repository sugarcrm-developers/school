#!/usr/bin/env bash

# This script stops the appropriate Sugar Docker stack.


######################################################################
# Variables
######################################################################

if [[ -z "$1" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Sugar version (Example: 11.0)
        2: Path to where the Sugar Docker files should be stored relative to the current directory. WARNING: The
           data/app/sugar directory will be deleted and recreated.

        For example: ./StopDockerStack.sh sugar11 php74.yml workspace/sugardocker"
    exit 1
fi

# The Docker stack version to be used
stackVersion=$1

# The Docker PHP yaml version/path to be used
phpYml=$2

# The local directory associated with the $dockerGitRepo
dockerDirectory=$3

######################################################################
# Setup
######################################################################
ymlPath=$dockerDirectory/stacks/$stackVersion/$phpYml

######################################################################
# Stop the Docker Stack
######################################################################

docker-compose -f $ymlPath down

echo "Network and/or containers removed."

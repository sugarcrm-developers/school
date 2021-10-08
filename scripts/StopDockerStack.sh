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

        For example: ./StopDockerStack.sh 11.0 workspace/sugardocker"
    exit 1
fi

# The Sugar version
sugarVersion=$1

# The local directory where the Sugar Docker stacks are stored
dockerDirectory=$2


######################################################################
# Setup
######################################################################

version11="11.0"
if (( $(echo "$sugarVersion >= $version11" | bc -l) ))
then
    ymlPath=$dockerDirectory/stacks/sugar11/php74.yml
else
    echo "Unable to identify Docker Stack yml for Sugar version $sugarVersion"
    exit 1
fi

######################################################################
# Stop the Docker Stack
######################################################################

docker-compose -f $ymlPath down

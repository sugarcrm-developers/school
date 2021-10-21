#!/usr/bin/env bash

# This script clones a copy of the Sugar Docker repo and starts the appropriate stack.


######################################################################
# Variables
######################################################################

if [[ -z "$1" ]] || [[ -z "$2" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Sugar version (Example: 11.0)
        2: Path to where the Sugar Docker files should be stored relative to the current directory. WARNING: The
           data/app/sugar directory will be deleted and recreated.

        For example: ./StartDockerStack.sh sugar11 php74.yml workspace/sugardocker"
    exit 1
fi

# The Docker stack version to be used
stackVersion=$1

# The Docker PHP yaml version/path to be used
phpYml=$2

# The local directory associated with the $dockerGitRepo
dockerDirectory=$3

# The Git Repo where the Sugar Docker stacks are stored
dockerGitRepo="https://github.com/esimonetti/SugarDockerized.git"


######################################################################
# Setup
######################################################################
ymlPath=$dockerDirectory/stacks/$stackVersion/$phpYml

######################################################################
# Get latest stack from Sugar Docker repo
######################################################################

if [ -d "$dockerDirectory" ];
then
    cwd=$(pwd)
    cd $dockerDirectory
    git fetch $dockerGitRepo
    git pull $dockerGitRepo
    cd $cwd
else
    git clone $dockerGitRepo $dockerDirectory
fi

######################################################################
# Stop any previously running stack
######################################################################

echo "Forcefully taking down any networks and/or containers still running from a previous Sugar Docker stack. 'Network
not found' and 'No such container' errors can be safely ignored..."

docker-compose -f $ymlPath down

docker rm -f sugar-cron
docker rm -f sugar-web1
docker rm -f sugar-elasticsearch
docker rm -f sugar-redis
docker rm -f sugar-mysql
docker rm -f sugar-permissions

echo "Network and/or containers removed."


######################################################################
# Start the Docker Stack
######################################################################

# Special case for when this code is being run from Jenkins running on Docker.
# We need to update the Sugar Docker stack yml file to have a hard coded path to the Sugar Docker directory on the
# host machine.
if [[ -n $PATH_TO_SUGAR_DOCKER_ON_HOST ]]
then
    # This regular expression searches for the following:
    #
    # volumes:
    #          - ../../
    #
    # The "../.." are replaced with the path to the Sugar Docker Directory
    echo "Updating $ymlPath..."
    perl -0777 -i -pe "s#(volumes\:\n *- )\.\.\/\.\.#\1$PATH_TO_SUGAR_DOCKER_ON_HOST#g" $ymlPath
fi

# Start the Sugar Docker stack
echo "Starting $ymlPath..."
docker-compose -f $ymlPath up -d

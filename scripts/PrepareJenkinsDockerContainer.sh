#!/usr/bin/env bash

# This script starts the Jenkins container and installs the packages that the scripts require:
# - Docker Compose
# - Perl

if [[ -z "$1" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: The path to your Jenkins home directory on your host machine
        2: (Optional) Additional params for the docker run command.  For example, you may want to mount a volume that
           contains Sugar source zips or you may want to mount a volume that contains a local copy of the Sugar Docker
           repo (-v /Users/lschaefer/git/sugardocker:/var/sugardocker).

        For example: ./PrepareJenkinsDockerContainer.sh /Users/lschaefer/jenkins2 \"-v /Users/lschaefer/git/sugardocker:/var/sugardocker\""
fi

# The path to your Jenkins home directory on your host machine
jenkinsHome=$1

# Additional params for the docker run command
additionalParams=$2


# Start Jenkins
containerId=$(docker run -u root --rm -d -p 8080:8080 -v $jenkinsHome:/var/jenkins_home -v /var/run/docker.sock:/var/run/docker.sock $additionalParams jenkinsci/blueocean)

# Get the latest packages
docker exec $containerId bash -c "apk update"

# Install pip
docker exec $containerId bash -c "apk add py-pip"

# Upgrade pip
docker exec $containerId bash -c "pip install --upgrade pip"

# Install docker-compose
docker exec $containerId bash -c "pip install docker-compose"

# Create a symbolic link
docker exec $containerId bash -c "ln -s /usr/bin/docker-compose /usr/local/bin/docker-compose"

# Install perl
docker exec $containerId bash -c "apk add perl"

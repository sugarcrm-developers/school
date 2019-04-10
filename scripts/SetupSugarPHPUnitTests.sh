#!/usr/bin/env bash

######################################################################
# Variables
######################################################################

if [[ -z "$1" ]] || [[ -z "$2" ]] || [[ -z "$3" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Sugar name (For example: SugarEnt-7.11)
        2: Sugar edition (Ent, Ult, or Pro)
        3: Path to where the Sugar files are stored

        For example: ./SetupSugarPHPUnitTests.sh SugarEnt-7.11 Ent workspace/sugardocker/data/app/sugar"
    exit 1
fi

# The Sugar name (For example: SugarEnt-7.11)
sugarName=$1

# The Sugar edition
sugarEdition="$(echo $2 | tr '[:upper:]' '[:lower:]')"

# The path to where the unzipped Sugar files are stored
sugarDirectory=$3


######################################################################
# Copy Sugar Unit Tests to Sugar Directory
######################################################################
echo "Copying unit tests from workspace/unit-tests/$sugarEdition/ to $sugarDirectory"
cp -rf workspace/unit-tests/$sugarEdition/* $sugarDirectory


######################################################################
# Install dependencies needed to run unit tests
######################################################################

# Install git so composer will be able to pull files from git repos
docker exec sugar-web1 bash -c "apt-get update"
echo "Installing git on sugar-web1"
docker exec sugar-web1 bash -c "apt-get install -y git wget"
echo "Installing composer on sugar-web1"
docker exec sugar-web1 bash -c "wget https://raw.githubusercontent.com/composer/getcomposer.org/d3e09029468023aa4e9dcd165e9b6f43df0a9999/web/installer -O - -q | php --"
echo "Installing php dependencies (via composer)"
# Install the dependencies
docker exec sugar-web1 bash -c "php composer.phar install --profile --prefer-source --ignore-platform-reqs"

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

cp -rf workspace/unit-tests/$sugarEdition/* $sugarDirectory


######################################################################
# Install dependencies needed to run unit tests
######################################################################

# composer.json and composer.lock contain references to internal Sugar repositories. We'll remove the references
# from composer.json and completely remove the composer.lock file.

# Remove the repository matching the following pattern:
#   {
#       "packagist.org": false
#   },
perl -0777 -i -pe "s#{\n *\"packagist.org\"\: false\n *},##g" $sugarDirectory/composer.json

# Remove the repository matching the following pattern:
#   ,
#   {
#       "type": "composer",
#       "url": "https://satis.sugardev.team"
#   }
perl -0777 -i -pe "s#,\n *{\n *\"type\"\: \"composer\",\n *\"url\"\: \"https:\/\/satis.sugardev.team\"\n *}##g" $sugarDirectory/composer.json

# Remove the composer.lock file
rm $sugarDirectory/composer.lock

# Install the dependencies
docker exec sugar-web1 bash -c "composer install"

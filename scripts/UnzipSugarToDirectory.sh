#!/usr/bin/env bash

######################################################################
# Variables
######################################################################

if [[ -z "$1" ]] || [[ -z "$2" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Sugar name (For example: SugarEnt-7.11)
        2: Path to where the unzipped Sugar files should be stored

        For example: ./UnzipSugarToDirectory.sh SugarEnt-7.11 workspace/sugardocker/data/app/sugar"
    exit 1
fi

# The Sugar name (For example: SugarEnt-7.11)
sugarName=$1

# The path to where the unzipped Sugar files should be stored
sugarDirectory=$2

# The name of the zip containing the Sugar files
sugarZipFileName="$sugarName.zip"


######################################################################
# Setup
######################################################################

cd "$(dirname "$sugarDirectory")"


######################################################################
# Unzip Sugar to directory
######################################################################

# Unzip the copy of Sugar we downloaded
echo "Unzipping $sugarZipFileName..."
unzip -q $sugarZipFileName
echo "Finished unzipping Sugar"
sleep 10

# Delete the zip file as we no longer need it
rm $sugarZipFileName

# Delete the Sugar directory if it exists so we can rename the unzipped directory to the Sugar directory
rm -rf "$(basename "$sugarDirectory")";

# The unzipped copy of Sugar will have the full name like 'SugarEnt-Full-7.11.0.0-dev.1'
# Rename the unzipped copy of Sugar to the shortened file name that other scripts will be expecting
for dir in Sugar*;
    do mv -f $dir "$(basename "$sugarDirectory")";
done
echo "Sugar directory renamed to $(basename "$sugarDirectory")"

#!/usr/bin/env bash

# This script stops the appropriate Sugar Docker stack.


######################################################################
# Variables
######################################################################

if [[ -z "$1" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Sugar version (Example: 7.11)
        2: Path to where the Sugar Docker files should be stored relative to the current directory. WARNING: The
           data/app/sugar directory will be deleted and recreated.

        For example: ./StopDockerStack.sh 7.11 workspace/sugardocker"
    exit 1
fi

# The Sugar version
sugarVersion=$1

# The local directory where the Sugar Docker stacks are stored
dockerDirectory=$2


######################################################################
# Setup
######################################################################

version9="9.0"
version8="8.0"
if (( $(echo "$sugarVersion >= $version9" | bc -l) ))
then
    ymlPath=$dockerDirectory/stacks/sugar9/php73.yml
elif (( $(echo "$sugarVersion >= $version8" | bc -l) ))
then
    ymlPath=$dockerDirectory/stacks/sugar81/php71.yml
else
    echo "Unable to identify Docker Stack yml for Sugar version $sugarVersion"
    exit 1
fi

# if [[ "$sugarVersion" == "9.3" ]]
# then
#     ymlPath=$dockerDirectory/stacks/sugar9/php73.yml
# if [[ "$sugarVersion" == "9.2" ]]
# then
#     ymlPath=$dockerDirectory/stacks/sugar9/php73.yml
# elif [[ "$sugarVersion" == "9.1" ]]
# then
#     ymlPath=$dockerDirectory/stacks/sugar9/php73.yml
# elif [[ "$sugarVersion" == "9.0" ]]
# then
#     ymlPath=$dockerDirectory/stacks/sugar9/php73.yml

# elif [[ "$sugarVersion" == "8.3" ]]
# then
#     ymlPath=$dockerDirectory/stacks/sugar81/php71.yml
# elif [[ "$sugarVersion" == "8.2" ]]
# then
#     ymlPath=$dockerDirectory/stacks/sugar81/php71.yml
# elif [[ "$sugarVersion" == "8.0" ]]
# then
#     ymlPath=$dockerDirectory/stacks/sugar8/php71.yml
# else
#     echo "Unable to identify Docker Stack yml for Sugar version $sugarVersion"
#     exit 1
# fi


######################################################################
# Stop the Docker Stack
######################################################################

docker-compose -f $ymlPath down

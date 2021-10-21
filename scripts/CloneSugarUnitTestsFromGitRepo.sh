#!/usr/bin/env bash

######################################################################
# Variables
######################################################################

if [[ -z "$1" ]] || [[ -z "$2" ]] || [[ -z "$3" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Sugar version (Example: 11.0)
        2: GitHub username that has access to sugarcrm/unit-tests
        3: Personal Access Token associated with the above account
        For example: ./CloneSugarUnitTestsFromGitRepo.sh 11.0 mygithubpersonalaccesstoken"
    exit 1
fi

# The Sugar version
sugarVersion=$1

# GitHub username
gitHubUsername=$2

# GitHub password
gitHubPersonalAccessToken=$3

######################################################################
# Determine which branch to clone
######################################################################
version11="11.0"
if (( $(echo "$sugarVersion >= $version11" | bc -l) ))
then
	branch="${sugarVersion/./_}_0"
else
    echo "Unable to find Sugar unit tests for version $sugarVersion"
    exit 1
fi


######################################################################
# Clone the Sugar unit test repo
######################################################################

cd workspace
git clone https://$gitHubUsername:$gitHubPersonalAccessToken@github.com/sugarcrm-developers/unit-tests.git -b $branch

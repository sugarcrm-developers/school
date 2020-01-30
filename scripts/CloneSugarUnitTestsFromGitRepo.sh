#!/usr/bin/env bash

######################################################################
# Variables
######################################################################

if [[ -z "$1" ]] || [[ -z "$2" ]] || [[ -z "$3" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Sugar version (Example: 8.0)
        2: GitHub username that has access to sugarcrm/unit-tests
        3: Password associated with the above account
        For example: ./CloneSugarUnitTestsFromGitRepo.sh 8.0 mygithubusername mygithubpassword"
    exit 1
fi

# The Sugar version
sugarVersion=$1

# GitHub username
gitHubUsername=$2

# GitHub password
gitHubPassword=$3


######################################################################
# Determine which branch to clone
######################################################################
version9="9.0"
version8="8.0"
if (( $(echo "$sugarVersion >= $version8" | bc -l) ))
then
	branch="${sugarVersion/./_}_0"
else
    echo "Unable to find Sugar unit tests for version $sugarVersion"
    exit 1
fi

# if [[ "$sugarVersion" == "9.3" ]]
# then branch="9_3_0"

# elif [[ "$sugarVersion" == "9.2" ]]
# then branch="9_2_0"

# elif [[ "$sugarVersion" == "9.1" ]]
# then branch="9_1_0"

# elif [[ "$sugarVersion" == "9.0" ]]
# then branch="9_0_0"

# elif [[ "$sugarVersion" == "8.3" ]]
# then branch="8_3_0"

# elif [[ "$sugarVersion" == "8.2" ]]
# then branch="8_2_0"

# elif [[ "$sugarVersion" == "8.1" ]]
# then branch="8_1_0"

# elif [[ "$sugarVersion" == "8.0" ]]
# then branch="8_0_0"

# else
#     echo "Unable to find Sugar unit tests for version $sugarVersion"
#     exit 1
# fi


######################################################################
# Clone the Sugar unit test repo
######################################################################

cd workspace
git clone https://$gitHubUsername:$gitHubPassword@github.com/sugarcrm/unit-tests.git -b $branch

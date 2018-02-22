#!/usr/bin/env bash

######################################################################
# Variables
######################################################################

if [[ -z "$1" ]] || [[ -z "$2" ]] || [[ -z "$3" ]] || [[ -z "$4" ]] || [[ -z "$5" ]] || [[ -z "$6" ]] || [[ -z "$7" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Email address associated with your SugarCRM Developer Builds Community account
        2: Password associated with the above account
        3: Sugar version (Example: 7.11)
        4: Sugar edition (Options: Ult, Ent, Pro)
        5: GitHub username that has access to sugarcrm/unit-tests
        6: Password associated with the above account
        7. Path to where the Sugar Docker files should be stored relative to the current directory. WARNING: The
           data/app/sugar directory will be deleted and recreated.
        8. (Optional) Path to where Sugar source zip files are stored. If this param is not provided, the Sugar
           source zip files will be downloaded from the SugarCRM Developer Builds Community.  The Sugar source zip files
           should be named with the following pattern: Sugar$sugarEdition-$sugarVersion.zip. For example: SugarEnt-7.11.zip

        For example: ./SetupEnvAndRunPHPUnitTests.sh communityemail@example.com mycommunitypassword 7.11 Pro githubusername
        githubpassword workspace/sugardocker ../sugar_source_zips"
    exit 1
fi

# Email address associated with your SugarCRM developer community account
email=$1

# Password associated with your SugarCRM developer community account
password=$2

# The Sugar version to download
sugarVersion=$3

# The Sugar edition to download
sugarEdition=$4

# The name we will use to refer to Sugar
sugarName="Sugar$sugarEdition-$sugarVersion"

# GitHub username
gitHubUsername=$5

# GitHub password
gitHubPassword=$6

# The path to where the Sugar Docker files should be stored relative to the current directory
sugarDockerDirectory=$7

# The path to where the Sugar source files are stored
sugarDirectory="$sugarDockerDirectory/data/app/sugar"

# The path to where the existing Sugar source files are stored (optional)
sugarSourceZipsDirectory=$8


######################################################################
# Setup
######################################################################

sudo chmod -R 777 .

mkdir workspace


######################################################################
# Setup the environment for PHPUnit tests and run them
######################################################################

./StartDockerStack.sh $sugarVersion $sugarDockerDirectory || exit 1

./GetCopyOfSugar.sh $email $password $sugarName "$(dirname "$sugarDirectory")" $sugarSourceZipsDirectory || exit 1

./CloneSugarUnitTestsFromGitRepo.sh $sugarVersion $gitHubUsername $gitHubPassword || exit 1

./UnzipSugarToDirectory.sh $sugarName $sugarDirectory || exit 1

./SetupSugarPHPUnitTests.sh $sugarName $sugarEdition $sugarDirectory || exit 1

./InstallSugarAndProfM.sh $sugarDirectory || exit 1

./RunSugarPHPUnitTests.sh $sugarDirectory || exit 1

./RunProfMPHPUnitTests.sh $sugarDirectory || exit 1

./StopDockerStack.sh $sugarVersion $sugarDockerDirectory || exit 1

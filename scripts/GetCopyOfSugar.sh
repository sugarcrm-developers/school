#!/usr/bin/env bash

# This script gets a copy of Sugar from the designated directory or downloads a Sugar dev build from the Sugar Store
# or the SugarCRM Developer Builds community.
#
# Note: you must have access to the Sugar Store (https://store.sugarcrm.com/download) and/or the SugarCRM Developer
# Builds community (https://community.sugarcrm.com/community/developer/developer-builds) depending on where the build is
# stored in order for the download to be successful.


######################################################################
# Variables
######################################################################

if [[ -z "$1" ]] || [[ -z "$2" ]] || [[ -z "$3" ]] || [[ -z "$4" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Username (not e-mail address) associated with your SugarClub account
        2: Password associated with the above account
        3: Sugar name (For example: SugarEnt-11.0)
        4. The path to where the Sugar download should be stored
        5. (Optional) Path to where Sugar source zip files are stored. If this param is not provided, the Sugar
           source zip files will be downloaded from the SugarCRM Developer Builds Community.  The Sugar source zip files
           should be named with the following pattern: Sugar$sugarEdition-$sugarVersion. For example: SugarEnt-11.0

        For example: ./GetCopyOfSugar.sh sugardevelopers mypassword SugarEnt-11.0 workspace/sugardocker/data/app ../sugar_source_zips"
    exit 1
fi

# Username address associated with your SugarCRM account
username=$1

# Password associated with your SugarCRM account
password=$2

# The Sugar name (For example: SugarEnt-11.0)
sugarName=$3

# The path to where the Sugar download should be stored
sugarDirectory=$4

# Path to where existing Sugar source zip files are stored
sugarSourceZipsDirectory=$5

# The path to the cookie jar file (relative to the Sugar directory) where the cookies required for this script will be stored
cookieFile="./mycookie"


######################################################################
# Functions
######################################################################

# Check that the status code in the response matches what is expected
# $1: response from curl command
checkStatusCode(){
    status=$(echo "$1" | grep -c 'HTTP/2 200')

    if [ $status -eq 1 ]
    then
        return 
    else
        echo "Status code is not the expected $1: $status"
        exit 1
    fi
}


getFileUrlFromResponse() {
    # made 2 passes at the regex. first one grabs everything from after FileUrl":" then the second grabs everything before the next ","
    fromGrep=$(echo "$1" | grep FileUrl)
    myvar=$(sed 's/\(^.*FileUrl\"\:\"\)\(.*\)\(zip\".*\)/\2/' <<< $fromGrep)
    regexFileUrl=$(sed 's/\"\,\".*//' <<< $myvar)
     if [[ $1 =~ $regexFileUrl ]]
     then
         fileUrl=$regexFileUrl
         echo "$fileUrl"
     else
         echo "Unable to find fileUrl in response"
         exit 1
     fi
}

# Authenticate to SugarClub 
function getFileDetailsFromSugarClub() {
    token=$(echo -n "$password:$username" | base64);
    filecontentid=$1
    response="$(curl -s -v -L -c $cookieFile -b $cookieFile -H "Rest-User-Token:$token" https://sugarclub.sugarcrm.com/api.ashx/v2/media/30/files/$filecontentid.json 2>&1)"
    checkStatusCode "$response"
}

######################################################################
# Check if we have a copy of the Sugar source zip already downloaded
######################################################################

# If we already have a copy of the Sugar source zip, we'll copy it to the Sugar directory and exit the script
if [[ -d "$sugarSourceZipsDirectory" && -e "$sugarSourceZipsDirectory/$sugarName.zip" ]]
then
    echo "$sugarSourceZipsDirectory/$sugarName.zip already exists. A new copy of Sugar will not be downloaded."

    cp $sugarSourceZipsDirectory/$sugarName.zip $sugarDirectory/$sugarName.zip
    exit 0
fi


######################################################################
# Setup for download
######################################################################

# Change to the Sugar directory
cd $sugarDirectory

# Delete the cookie jar file if it exists
rm -f $cookieFile

# Set the file permissions for when sudo is required and is not required. Output of these commands will not be printed.
chmod -R 777 . &> /dev/null
sudo chmod -R 777 . &> /dev/null


#######################################################################
# Get the URL to download and authenticate to the appropriate location
#######################################################################

strippedName="${sugarName//./}"
strippedName="${strippedName//-/}"

# define the file ID and the expected Checksum for each flavor and version combination
# grab the id from the ContentId field in https://sugarclub.sugarcrm.com/api.ashx/v2/media/30/files/1410.json where 1410 is the id in the url for this file in DevClub
# grab the checksum from honeycomb

id_SugarEnt110="9c1183ea-47aa-4d71-8ee4-9ba9ad579429"
cs_SugarEnt110="10026556829c584e46360709950fd6122a46d0a3"
id_SugarPro110="72533ca8-84e5-48bb-975a-ea546a5d7578"
cs_SugarPro110="3f33481e2b6c61a34d8afbefd84d12127bd5bd19"

id_SugarEnt111="71b55d4b-e0d0-4aba-98ff-9c41e98eb6ef"
cs_SugarEnt111="27994ab9a390fa2fca92e5e4da84b309d4f9ca85"
id_SugarPro111="ee7dccdf-7129-4387-bdbb-01bb261cadfd"
cs_SugarPro111="d57999c6b7b3b5c9351c48f3e67aa1cc2bd4b2a4"

id_SugarEnt112="f8ee1af7-84e4-46cb-8ead-30839b0f7e24"
cs_SugarEnt112="324920fd1462cba13ca893afee75f8895b7d412e"
id_SugarPro112="af4e861c-b068-4799-b093-a661c4d698cc"
cs_SugarPro112="5448efd36d7d4a2367a4a7e852b41e3ad26cee9d"

# id_SugarEnt113="xxxxxxxx"
# cs_SugarEnt113="yyyyyyyy"
# id_SugarPro113="xxxxxxxx"
# cs_SugarPro113="yyyyyyyy"


idVar="id_$strippedName"
csVar="cs_$strippedName"

if [ ! -n "${!idVar}" ]; then
    echo "The requested flavor and/or version is not supported ($sugarName)"
    exit 1
fi
getFileDetailsFromSugarClub ${!idVar}
downloadUrl=$(getFileUrlFromResponse "$response")
expectedChecksum=${!csVar}


######################################################################
# Download Sugar
######################################################################

echo "Beginning download of $sugarName from $downloadUrl"
response="$(curl -v -L -c $cookieFile -b $cookieFile -H "Rest-User-Token:$token" -o $sugarName.zip $downloadUrl 2>&1)"
checkStatusCode "$response"
echo "Download complete"

#Verify the checksum is correct
checksumOutput="$(shasum $sugarName.zip)"
checksumOutput=($checksumOutput)
checksumOfDownload=${checksumOutput[0]}


if [[ $expectedChecksum != $checksumOfDownload ]]
then
    echo "The checksum of the downloaded file did not match the expected checksum"
    echo "Expected: $expectedChecksum"
    echo "Actual:   $checksumOfDownload"
    exit 1
fi


######################################################################
# Cleanup
######################################################################

# Delete the cookie jar file
rm $cookieFile

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
# $1: expected status code
# $2: response from curl command
checkStatusCode(){
    regexStatusCode=".*HTTP\/2 ([^[:space:]]*).*"

    if [[ $2 =~ $regexStatusCode ]]
    then
        statusCode="${BASH_REMATCH[1]}"
        if [[ "$statusCode" == $1 ]]
        then
            return
        else
            echo "Status code is not the expected $1: $statusCode"
            #echo "$2"
            exit 1
        fi
    else
        echo "Unable to find status code in response: $2"
        exit 1
    fi
}


getFileUrlFromResponse() {
    # made 2 passes at the regex. first one grabs everything from after FileUrl":" then the second grabs everything before the next ","
    myvar=$(sed 's/\(^.*FileUrl\"\:\"\)\(.*\)\(zip\".*\)/\2/' <<< $1)
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
    response="$(curl -v -L -c $cookieFile -b $cookieFile -H "Rest-User-Token:$token" https://sugarclub.sugarcrm.com/api.ashx/v2/media/30/files/$filecontentid.json 2>&1)"
    checkStatusCode "200" "$response"
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

id_SugarEnt90="097c6456-c4d2-450a-99ba-9c16371c1e39"
cs_SugarEnt90="895f5662ebb21f49a74a3fbc6966f1b30507ef3c"
id_SugarPro90="30a7ec5b-cd71-4ffd-8536-164299a08526"
cs_SugarPro90="e8d8fac1405912e869fcab539c33d5b5327d2c3d"

id_SugarEnt91="924c90f4-ffee-45d7-8732-043e13ba606b"
cs_SugarEnt91="b76a0470a164a776b806ec843894b4de3b1c8e64"
id_SugarPro91="b634ced8-9de5-4630-a654-76820e5e94ca"
cs_SugarPro91="5d761d8572b16fac4d83991c3dd74b508f70fba0"

id_SugarEnt92="a038a91e-b3ca-4c01-b752-f156aeb3ad34"
cs_SugarEnt92="ec3a758b2e5e81a38f743dc760c8815451972387"
id_SugarPro92="e546cf45-2773-4a71-b15a-85b5687af63a"
cs_SugarPro92="a692a47d20f48034673c82c341b1953535255e47"

id_SugarEnt93="0605d7ff-59e6-4c69-81e9-7a5481bb800e"
cs_SugarEnt93="e93eac01f650469dfddcb895f5c143b84c8ddac2"
id_SugarPro93="796818ed-2976-4e11-ba19-60631d66ec53"
cs_SugarPro93="de364d6025ae6697ee6931dbc0e2fff030b2c396"

id_SugarEnt100="1e8c51f4-b2d7-4ab6-8a0c-a7a9690875b8"
cs_SugarEnt100="dae58ae7423d6efb8f40f233e7b262b0ece91bc1"
id_SugarPro100="a56dedbf-25db-4242-8598-749f640c4688"
cs_SugarPro100="8a2d6ca94d07333ee22143627f2a72edb867683e"

id_SugarEnt101="c6f806f0-1dfc-4a5d-97a3-f89ca9b57de5"
cs_SugarEnt101="65150251d0780f1ed1e4cb2f19c92fb61a91ba19"
id_SugarPro101="878db074-69e3-4372-a800-7112e397af8f"
cs_SugarPro101="aa84273663accbca19512d6d33589b39147dbd99"

id_SugarEnt102="ea3ec8fe-eb1a-4ef9-bbd7-9ddb096433f3"
cs_SugarEnt102="10162ed696fa0ebf19847fa3bb39b7c80d0fa47b"
id_SugarPro102="f7d3a541-2f96-422d-9008-337f773e14e5"
cs_SugarPro102="6d27bbabe86c8d059bd301bc1fc34f0251d8b939"

id_SugarEnt103="efb60d0d-4253-4ad6-8091-8fea06dac868"
cs_SugarEnt103="f74d4ec245902a63d4bd91d3bc0d928d113a72d6"
id_SugarPro103="02a7e3cc-e621-4729-88a8-0b355376df88"
cs_SugarPro103="36dc42930f0928ff8b7d70e10c7724e0aac053db"

id_SugarEnt110="9c1183ea-47aa-4d71-8ee4-9ba9ad579429"
cs_SugarEnt110="10026556829c584e46360709950fd6122a46d0a3"
id_SugarPro110="72533ca8-84e5-48bb-975a-ea546a5d7578"
cs_SugarPro110="3f33481e2b6c61a34d8afbefd84d12127bd5bd19"

id_SugarEnt111="71b55d4b-e0d0-4aba-98ff-9c41e98eb6ef"
cs_SugarEnt111="27994ab9a390fa2fca92e5e4da84b309d4f9ca85"
id_SugarPro111="ee7dccdf-7129-4387-bdbb-01bb261cadfd"
cs_SugarPro111="d57999c6b7b3b5c9351c48f3e67aa1cc2bd4b2a4"

# id_SugarEnt112="xxxxxxxx"
# cs_SugarEnt112="yyyyyyyy"
# id_SugarPro112="xxxxxxxx"
# cs_SugarPro112="yyyyyyyy"

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
checkStatusCode "200" "$response"
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

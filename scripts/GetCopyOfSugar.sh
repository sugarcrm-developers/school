#!/usr/bin/env bash

# This script gets a copy of Sugar from the designated directory or downloads a Sugar dev build from the SugarCRM
# Developer Builds community.
#
# Note: you must have access to the SugarCRM Developer Builds community
# (https://community.sugarcrm.com/community/developer/developer-builds) in order for the download to be successful.


######################################################################
# Variables
######################################################################

if [[ -z "$1" ]] || [[ -z "$2" ]] || [[ -z "$3" ]] || [[ -z "$4" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Email address associated with your SugarCRM Developer Builds Community account
        2: Password associated with the above account
        3: Sugar name (For example: SugarEnt-7.11)
        4. The path to where the Sugar download should be stored
        5. (Optional) Path to where Sugar source zip files are stored. If this param is not provided, the Sugar
           source zip files will be downloaded from the SugarCRM Developer Builds Community.  The Sugar source zip files
           should be named with the following pattern: Sugar$sugarEdition-$sugarVersion. For example: SugarEnt-7.11

        For example: ./GetCopyOfSugar.sh email@example.com mypassword SugarEnt-7.11 workspace/sugardocker/data/app ../sugar_source_zips"
    exit 1
fi

# Email address associated with your SugarCRM developer community account
email=$1

# Password associated with your SugarCRM developer community account
password=$2

# The Sugar name (For example: SugarEnt-7.11)
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
    regexStatusCode=".*HTTP/1.1 ([^[:space:]]*).*"

    if [[ $2 =~ $regexStatusCode ]]
    then
        statusCode="${BASH_REMATCH[1]}"
        if [[ "$statusCode" == $1 ]]
        then
            echo "Status code is correct: $statusCode"
        else
            echo "Status code is not the expected $1: $statusCode"
            echo "$2"
            exit 1
        fi
    else
        echo "Unable to find status code in response: $2"
        exit 1
    fi
}

# Print the value of the Location included in the response
# $1: response from curl command
getLocationFromResponse(){
    regexLocation=".*Location: ([^[:space:]]*).*"

    if [[ $1 =~ $regexLocation ]]
    then
        location="${BASH_REMATCH[1]}"
        echo "$location"
    else
        echo "Unable to find location in response"
        exit 1
    fi
}

# Print the value associated with a hidden form field
# $1: name of the hidden form field
# $2: response from curl command
getHiddenFormFieldValue(){
    regexToken=".*name=\"$1\" value=\"([^\"]*).*"

    if [[ $response =~ $regexToken ]]
    then
        value="${BASH_REMATCH[1]}"

        # This is a hack specifically for Travis CI.  Travis CI outputs a string like the following in the middle
        # of the SAML Response:
        #
        # 100 12223    0 12223    0     0  16084      0 --:--:-- --:--:-- --:--:-- 16104
        # * Connection #0 to host auth.sugarcrm.com left intact
        #
        # This Regex Token pulls this junk out of the SAML Response
        newLineRegexToken="(.*)[[:space:]]+[[:digit:]][[:digit:]][[:digit:]][[:space:]].*intact[[:space:]]*(.*)"
        if [[ $1 == 'SAMLResponse' && $value =~ $newLineRegexToken ]]
        then
            value="${BASH_REMATCH[1]}${BASH_REMATCH[2]}"
        fi

        echo $value
    else
        echo "Unable to find $2 in response"
        echo "$2"
        exit 1
    fi

}


######################################################################
# Check if we need to download the Sugar source zip
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

# Set the file permissions for when sudo is required and is not required
chmod -R 777 .
sudo chmod -R 777 .


######################################################################
# Authenticate to the community
######################################################################

echo "Authenticating to Developer Builds Community..."

response="$(curl -v -c $cookieFile -b $cookieFile 'https://community.sugarcrm.com/login.jspa?ssologin=true&fragment=&referer=%2Fcommunity%2Fdeveloper%2Fdeveloper-builds' 2>&1)"
checkStatusCode "302" "$response"
location="$(getLocationFromResponse "$response")"

# Location should be something like https://auth.sugarcrm.com/saml2/idp/SSOService?SAMLRequest=fZHNbsIwEIRfxdp7EjuUn1okiBahIlGBSOihNxMMGCV26nVQ%2B%2FZ1IahUlTh6vbPf7sxw9FmV5CQtKqMTYCEFInVhtkrvE1jn02AAo3SIoirjmo8bd9Ar%2BdFIdMQLNfLLTwKN1dwIVMi1qCRyV%2FBs%2FDrncUh5bY0zhSmBjBGldR71bDQ2lbSZtCdVyPVqnsDBuRp5FBWmqhqt3FeIzV7YwlahL0U%2FpAjRAJl4vNLCnVe%2BqoTf7b8gjtS2jrJs0YKATI0t5PmQBHaiRF%2BaTRIQjPXo8aAGne6OdukDUxsmYnHsql7M2KNvwqVAVCf5K0Ns5EyjE9olEFM2CGgcsDhnfU47nPZD1um%2FA1m25z8pfbH1nlebSxPylzxfBstFlgN5u8bjG6ANg5%2Fp9jaF%2B4PF1XpI7xs9jG4Bafv8G376DQ%3D%3D&RelayState=L2NvbW11bml0eS9kZXZlbG9wZXIvZGV2ZWxvcGVyLWJ1aWxkcw%3D%3D
response="$(curl -v -c $cookieFile -b $cookieFile $location 2>&1)"
checkStatusCode "302" "$response"
location="$(getLocationFromResponse "$response")"

# Location should be something like https://auth.sugarcrm.com/saml2/idp/authpage?ReturnTo=https%3A%2F%2Fauth.sugarcrm.com%2Fsaml2%2Fidp%2FSSOService%3Fspentityid%3Dhttps%253A%252F%252Fcommunity.sugarcrm.com%26RelayState%3DL2NvbW11bml0eS9kZXZlbG9wZXIvZGV2ZWxvcGVyLWJ1aWxkcw%253D%253D
response="$(curl -v -c $cookieFile -b $cookieFile $location 2>&1)"
checkStatusCode "200" "$response"
token="$(getHiddenFormFieldValue "_token" "$response")"

response="$(curl -v -c $cookieFile -b $cookieFile --data "_token=$token&email=$email&password=$password" https://auth.sugarcrm.com/saml2/idp/authpage?ReturnTo=https%3A%2F%2Fauth.sugarcrm.com%2Fsaml2%2Fidp%2FSSOService%3Fspentityid%3Dhttps%253A%252F%252Fcommunity.sugarcrm.com%26RelayState%3DL2NvbW11bml0eS9kZXZlbG9wZXIvZGV2ZWxvcGVyLWJ1aWxkcw%253D%253D 2>&1)"
checkStatusCode "302" "$response"
location="$(getLocationFromResponse "$response")"

# Location should be something like 'https://auth.sugarcrm.com/saml2/idp/SSOService?spentityid=https%3A%2F%2Fcommunity.sugarcrm.com&RelayState=L2NvbW11bml0eS9kZXZlbG9wZXIvZGV2ZWxvcGVyLWJ1aWxkcw%3D%3D'
response="$(curl -v -c $cookieFile -b $cookieFile $location 2>&1)"
checkStatusCode "200" "$response"
samlResponse="$(getHiddenFormFieldValue "SAMLResponse" "$response")"

response="$(curl -v -c $cookieFile -b $cookieFile --data-urlencode "SAMLResponse=$samlResponse" --data-urlencode "RelayState=L2NvbW11bml0eS9kZXZlbG9wZXIvZGV2ZWxvcGVyLWJ1aWxkcw==" 'https://community.sugarcrm.com/saml/sso' 2>&1)"
checkStatusCode "302" "$response"


#######################################################################
## Download the Sugar zip
#######################################################################

sugarVersion_7_10="7.10"
sugarVersion_7_11="7.11"

sugarEdition_Ult="Ult"
sugarEdition_Ent="Ent"
sugarEdition_Pro="Pro"

# Get the url for the appropriate Sugar version and edition
if [[ "$sugarName" == "Sugar$sugarEdition_Ult-$sugarVersion_7_10" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5839-102-1-8005/SugarUlt-7.10.2.0-dev.1.zip"

elif [[ "$sugarName" == "Sugar$sugarEdition_Ent-$sugarVersion_7_10" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5837-102-1-8003/SugarEnt-7.10.2.0-dev.1.zip"

elif [[ "$sugarName" == "Sugar$sugarEdition_Pro-$sugarVersion_7_10" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5838-102-1-8004/SugarPro-7.10.2.0-dev.1.zip"

elif [[ "$sugarName" == "Sugar$sugarEdition_Ult-$sugarVersion_7_11" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5958-102-1-8147/SugarUlt-7.11.0.0-dev.1.zip"

elif [[ "$sugarName" == "Sugar$sugarEdition_Ent-$sugarVersion_7_11" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5959-102-1-8148/SugarEnt-7.11.0.0-dev.1.zip"

elif [[ "$sugarName" == "Sugar$sugarEdition_Pro-$sugarVersion_7_11" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5957-102-1-8146/SugarPro-7.11.0.0-dev.1.zip"

else
    echo "Unable to find Sugar download URL for $sugarName"
    exit 1
fi

# Download the file
echo "Beginning download of $sugarName from $downloadUrl"
response="$(curl -v -c ./mycookie -b ./mycookie -o $sugarName.zip $downloadUrl 2>&1)"
checkStatusCode "200" "$response"
echo "Download complete"

# Check we didn't get an empty zip file
fileSize=$(wc -c <"$sugarName.zip")
if [[ $fileSize -lt 60000000 ]]
then
    echo "$sugarName.zip has a file size of $fileSize.  The download may not have been successful."
    exit 1
fi


######################################################################
# Cleanup
######################################################################

# Delete the cookie jar file
rm $cookieFile

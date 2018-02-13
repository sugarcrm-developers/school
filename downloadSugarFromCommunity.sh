#!/usr/bin/env bash

# This script downloads a Sugar dev build from the SugarCRM Developer Builds community.
#
# Note: you must have access to the SugarCRM Developer Builds community
# (https://community.sugarcrm.com/community/developer/developer-builds) in order for the script to work.
#
# Run the script with the following arguments:
# 1: Email address associated with your SugarCRM Developer Builds Community account
# 2: Password associated with the above account
# 3: Sugar version to download (Example: 7.11)
# 4: Sugar Edition to download (Options: Ult, Ent, Pro)


######################################################################
# Variables
######################################################################

if [[ -z "$1" ]] || [[ -z "$2" ]] || [[ -z "$3" ]] || [[ -z "$4" ]]
then
    echo "Not all required command line arguments were set. Please run the script again with the required arguments:
        1: Email address associated with your SugarCRM Developer Builds Community account
        2: Password associated with the above account
        3: Sugar version to download (Example: 7.11)
        4: Sugar edition to download (Options: Ult, Ent, Pro)

        For example: ./downloadSugarFromCommunity.sh email@example.com mypassword 7.11 Pro"
    exit
fi

# Email address associated with your SugarCRM developer community account
email=$1

# Password associated with your SugarCRM developer community account
password=$2

# The Sugar version to download
sugarVersion=$3

# The Sugar edition to download
sugarEdition=$4

# The name of the cookie jar file where the cookies required for this script will be stored
cookieFile="mycookie"


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
            exit
        fi
    else
        echo "Unable to find status code in response: $2"
        exit
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
        exit
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
        echo "$value"
    else
        echo "Unable to find $2 in response"
        echo "$2"
        exit
    fi

}


######################################################################
# Setup
######################################################################

# Delete the cookie jar file if it exists
rm -f $cookieFile


######################################################################
# Authenticate to the community
######################################################################

response="$(curl -v -c ./mycookie -b ./mycookie 'https://community.sugarcrm.com/login.jspa?ssologin=true&fragment=&referer=%2Fcommunity%2Fdeveloper%2Fdeveloper-builds' 2>&1)"
checkStatusCode "302" "$response"
location="$(getLocationFromResponse "$response")"

# Location should be something like https://auth.sugarcrm.com/saml2/idp/SSOService?SAMLRequest=fZHNbsIwEIRfxdp7EjuUn1okiBahIlGBSOihNxMMGCV26nVQ%2B%2FZ1IahUlTh6vbPf7sxw9FmV5CQtKqMTYCEFInVhtkrvE1jn02AAo3SIoirjmo8bd9Ar%2BdFIdMQLNfLLTwKN1dwIVMi1qCRyV%2FBs%2FDrncUh5bY0zhSmBjBGldR71bDQ2lbSZtCdVyPVqnsDBuRp5FBWmqhqt3FeIzV7YwlahL0U%2FpAjRAJl4vNLCnVe%2BqoTf7b8gjtS2jrJs0YKATI0t5PmQBHaiRF%2BaTRIQjPXo8aAGne6OdukDUxsmYnHsql7M2KNvwqVAVCf5K0Ns5EyjE9olEFM2CGgcsDhnfU47nPZD1um%2FA1m25z8pfbH1nlebSxPylzxfBstFlgN5u8bjG6ANg5%2Fp9jaF%2B4PF1XpI7xs9jG4Bafv8G376DQ%3D%3D&RelayState=L2NvbW11bml0eS9kZXZlbG9wZXIvZGV2ZWxvcGVyLWJ1aWxkcw%3D%3D
response="$(curl -v -c ./mycookie -b ./mycookie $location 2>&1)"
checkStatusCode "302" "$response"
location="$(getLocationFromResponse "$response")"

# Location should be something like https://auth.sugarcrm.com/saml2/idp/authpage?ReturnTo=https%3A%2F%2Fauth.sugarcrm.com%2Fsaml2%2Fidp%2FSSOService%3Fspentityid%3Dhttps%253A%252F%252Fcommunity.sugarcrm.com%26RelayState%3DL2NvbW11bml0eS9kZXZlbG9wZXIvZGV2ZWxvcGVyLWJ1aWxkcw%253D%253D
response="$(curl -v -c ./mycookie -b ./mycookie $location 2>&1)"
checkStatusCode "200" "$response"
token="$(getHiddenFormFieldValue "_token" "$response")"

response="$(curl -v -c ./mycookie -b ./mycookie --data "_token=$token&email=$email&password=$password" https://auth.sugarcrm.com/saml2/idp/authpage?ReturnTo=https%3A%2F%2Fauth.sugarcrm.com%2Fsaml2%2Fidp%2FSSOService%3Fspentityid%3Dhttps%253A%252F%252Fcommunity.sugarcrm.com%26RelayState%3DL2NvbW11bml0eS9kZXZlbG9wZXIvZGV2ZWxvcGVyLWJ1aWxkcw%253D%253D 2>&1)"
checkStatusCode "302" "$response"
location="$(getLocationFromResponse "$response")"

# Location should be something like 'https://auth.sugarcrm.com/saml2/idp/SSOService?spentityid=https%3A%2F%2Fcommunity.sugarcrm.com&RelayState=L2NvbW11bml0eS9kZXZlbG9wZXIvZGV2ZWxvcGVyLWJ1aWxkcw%3D%3D'
response="$(curl -v -c ./mycookie -b ./mycookie $location 2>&1)"
checkStatusCode "200" "$response"
samlResponse="$(getHiddenFormFieldValue "SAMLResponse" "$response")"

response="$(curl -v -c ./mycookie -b ./mycookie --data-urlencode "SAMLResponse=$samlResponse&RelayState=L2NvbW11bml0eS9kZXZlbG9wZXIvZGV2ZWxvcGVyLWJ1aWxkcw%3D%3D" 'https://community.sugarcrm.com/saml/sso' 2>&1)"
checkStatusCode "302" "$response"


######################################################################
# Download the Sugar zip
######################################################################

sugarVersion_7_10="7.10"
sugarVersion_7_11="7.11"

sugarEdition_Ult="Ult"
sugarEdition_Ent="Ent"
sugarEdition_Pro="Pro"

# Get the url for the appropriate Sugar version and edition
if [[ "$sugarVersion$sugarEdition" == "$sugarVersion_7_10$sugarEdition_Ult" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5839-102-1-8005/SugarUlt-7.10.2.0-dev.1.zip"

elif [[ "$sugarVersion$sugarEdition" == "$sugarVersion_7_10$sugarEdition_Ent" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5837-102-1-8003/SugarEnt-7.10.2.0-dev.1.zip"

elif [[ "$sugarVersion$sugarEdition" == "$sugarVersion_7_10$sugarEdition_Pro" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5838-102-1-8004/SugarPro-7.10.2.0-dev.1.zip"

elif [[ "$sugarVersion$sugarEdition" == "$sugarVersion_7_11$sugarEdition_Ult" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5958-102-1-8147/SugarUlt-7.11.0.0-dev.1.zip"

elif [[ "$sugarVersion$sugarEdition" == "$sugarVersion_7_11$sugarEdition_Ent" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5959-102-1-8148/SugarEnt-7.11.0.0-dev.1.zip"

elif [[ "$sugarVersion$sugarEdition" == "$sugarVersion_7_11$sugarEdition_Pro" ]]
then downloadUrl="https://community.sugarcrm.com/servlet/JiveServlet/downloadBody/5957-102-1-8146/SugarPro-7.11.0.0-dev.1.zip"

else
    echo "Unable to find Sugar download URL for version $sugarVersion and edition $sugarEdition"
    exit
fi

# Download the file
fileName="Sugar$sugarEdition-$sugarVersion.zip"
echo "Beginning download of $fileName from $downloadUrl"
response="$(curl -v -c ./mycookie -b ./mycookie -o $fileName $downloadUrl 2>&1)"
checkStatusCode "200" "$response"
echo "Download complete"

# Check we didn't get an empty zip file
fileSize=$(wc -c <"$fileName")
if [[ $fileSize -lt 60000000 ]]
then
    echo "$fileName has a file size of $fileSize.  The download may not have been successful."
fi


######################################################################
# Cleanup
######################################################################

# Delete the cookie jar file
rm $cookieFile

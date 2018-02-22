#!/usr/bin/env bash

# This script runs the Sugar PHPUnit tests. It assumes the Sugar Docker stack has already been started and that
# SetupSugarPHPUnit tests has completed successfully.


######################################################################
# Setup
######################################################################

# Change to the directory where the Sugar PHPUnit tests are stored and update permissions
docker exec sugar-web1 bash -c "cd custom/tests/School/unit-php && chmod +x ../../../../vendor/bin/phpunit"

######################################################################
# Run the Professor M PHPUnit tests
######################################################################

echo "Running the PHPUnit tests for the Professor M Module Loadable Package..."
docker exec sugar-web1 bash -c "cd custom/tests/School/unit-php && ../../../../vendor/bin/phpunit"

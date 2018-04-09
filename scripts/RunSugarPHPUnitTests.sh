#!/usr/bin/env bash

# This script runs the Sugar PHPUnit tests. It assumes the Sugar Docker stack has already been started and that
# SetupSugarPHPUnit tests has completed successfully.


######################################################################
# Setup
######################################################################

# Change to the directory where the Sugar PHPUnit tests are stored & update permissions
docker exec sugar-web1 bash -c "cd tests/unit-php && chmod +x ../../vendor/bin/phpunit"


######################################################################
# Run the Sugar PHPUnit tests
######################################################################

echo "Running the PHPUnit tests for Sugar..."
docker exec sugar-web1 bash -c "cd tests/unit-php && ../../vendor/bin/phpunit"

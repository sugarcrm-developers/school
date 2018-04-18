#!/bin/bash

set -e

# Verify that needed mountpoint exists and is not empty
WORKSPACE_HOME=/workspace

if ! mountpoint -q "${WORKSPACE_HOME}"; then
  echo "Error: Must provide mountpoint"
  exit 1
fi
if [ ! "$(ls -A ${WORKSPACE_HOME})" ]; then
  echo "Error: ${WORKSPACE_HOME} cannot be empty"
  exit 1
fi
exec "$@"

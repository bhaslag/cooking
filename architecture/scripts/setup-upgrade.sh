#!/bin/bash

cd "$( dirname "${BASH_SOURCE[0]}" )"
cd ..

USAGE_ADDITIONAL_PARAMETER="[uri]"
USAGE_ADDITIONAL_HELP="\turi : the Drupal site URI, default is default\n"

source scripts/_environment.sh
source scripts/_ansible.sh

URI=default
if [ ! -z $2 ]
  then URI=$2
fi

ansible-playbook --ssh-extra-args="${ANSIBLE_SSH_ARGS}" provisioning/setup-upgrade.yml -i provisioning/inventory/$inventory -e script_uri=$URI

#!/bin/bash

set -o pipefail

PATH=/usr/bin:/usr/sbin:/bin:/sbin

###########
# variables
###########

script_canon="$(readlink -m "$0")"
absolute_path="$(dirname "${script_canon}")"
pushd "$absolute_path" >/dev/null || exit 1

script_name="${0##*/}"
lock_file="/tmp/${script_name}.lock"

vault_server="https://127.0.0.1:8200"
ca_cert="/etc/ssl/openbao/ca-chain.cert.pem"
pass_directory="/var/www/bao"

vault_token=""

###############
# basic locking
###############

if (set -o noclobber; echo "$$" >"$lock_file") 2>/dev/null
then
    # shellcheck disable=2154
    trap 'exit_code=$?; \
    set +e; \
    rm -f "$lock_file"; \
    exit $exit_code' INT TERM EXIT ERR
else
    >&2 echo "Failed to acquire lock: '$lock_file'."
    >&2 echo "Held by PID $(cat "$lock_file")"
    exit 1
fi

###########
# functions
###########

usage () {
    cat <<USAGE
Usage: $script_name
USAGE
    exit
}

#########
# options
#########

while [[ "$#" -gt 0 ]]
do
    case "$1" in
        --help | -h )
            usage
            ;;
        -- )
            shift
            break
            ;;
        * )
            usage
            ;;
    esac
done

################
# sanitiy checks
################

###########
# fn main()
###########

if [[ -r  "${pass_directory}/.env" ]]; then
    vault_token=$(grep '^BAO_TOKEN' "${pass_directory}/.env" |awk '{print $NF}' || :)
fi

if [[ -n "$vault_token" ]]; then
    curl -s --cacert ${ca_cert} --request POST --data '{"increment": "48h"}' --header "X-Vault-Token: $vault_token"  "${vault_server}/v1/auth/token/renew-self" >/dev/null
else
    >&2 echo 'No vault token found.'
    exit 1
fi

popd >/dev/null || exit 0

#!/bin/bash

set -o pipefail

PATH=/usr/bin:/bin

umask 077

###########
# variables
###########

script_canon="$(readlink -m "$0")"
absolute_path="$(dirname "${script_canon}")"
pushd "$absolute_path" >/dev/null || exit 1

script_name="${0##*/}"
lock_file="/tmp/${script_name}.lock"

temp_dir=$(mktemp -d "/tmp/${script_name}-XXXXXXXX")

vault_server="https://127.0.0.1:8200"
ca_cert="/etc/ssl/openbao/ca-chain.cert.pem"
pass_role_id=""
pass_secret_id=""
pass_directory="/var/www/bao"
new_token=""

###############
# basic locking
###############

if (set -o noclobber; echo "$$" >"$lock_file") 2>/dev/null
then
    # shellcheck disable=2154
    trap 'exit_code=$?; \
    set +e; \
    rm -f "$lock_file"; \
    rm -rf "$temp_dir"; \
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

echo -n "Enter Role ID: "
read -r pass_role_id

echo -n "Enter Secret ID: "
read -r pass_secret_id

if [[ -r  "${pass_directory}/.env" ]]; then
    old_token=$(grep -e '^BAO_TOKEN' "${pass_directory}/.env" 2>/dev/null |awk '{print $NF}' || :)
fi

if [[ -n "$pass_role_id" && -n "$pass_secret_id" ]]; then
    new_token=$(curl -s --cacert /etc/ssl/openbao/ca-chain.cert.pem --request POST --data "{\"role_id\": \"${pass_role_id}\", \"secret_id\": \"${pass_secret_id}\"}" "${vault_server}/v1/auth/approle/login" |jq -r '.auth.client_token')
fi

if [[ -z "$new_token" ]]; then
    >&2 echo "Could not generate new token."
    exit 1
elif echo "$new_token" |grep -qs "invalid role or secret ID"; then
    >&2 echo "Invalid role or secret ID."
    exit 1
else
    cat <<EOF >"${temp_dir}/.env"
BAO_ADDR = $vault_server
BAO_TOKEN = $new_token
BAO_WRAP_TTL = 21600
BAO_AUTH_USER = secret
BAO_AUTH_PW = fan
BAO_CACERT = $ca_cert
EOF
    chmod 0640 "${temp_dir}/.env"
    chown www-data:www-data "${temp_dir}/.env"
    mv "${temp_dir}/.env" "${pass_directory}/"
    rm -rf "${temp_dir}"
fi

if [[ -n "$old_token" ]]; then
    echo "Revoking old vault token."
    sleep 5
    curl -s --cacert $ca_cert --request POST --header "X-Vault-Token: $old_token"  "${vault_server}/v1/auth/token/revoke-self"
fi

popd >/dev/null || exit 0

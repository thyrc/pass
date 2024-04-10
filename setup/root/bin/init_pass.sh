#!/bin/sh

set -eu
set -o pipefail

PATH=/usr/bin:/bin

###########
# variables
###########

script_canon="$(readlink -m "$0")"
absolute_path="$(dirname "${script_canon}")"
pushd "$absolute_path" >/dev/null

script_name="${0##*/}"
lock_file="/tmp/${script_name}.lock"

temp_dir=$(mktemp -d /tmp/${script_name}-XXXXXXXX)

vault_server="https://vault.example.com"
pass_role_id=""
pass_secret_id=""
pass_directory="/var/www/pass"
new_token=""

###############
# basic locking
###############

if (set -o noclobber; echo "$$" >"$lock_file") 2>/dev/null
then
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
# fn main()
###########

echo -n "Enter Role ID: "
read -r pass_role_id

echo -n "Enter Secret ID: "
read -r pass_secret_id

if [[ -r  "${pass_directory}/.env" ]]; then
    old_token=$(grep -e '^VAULT_TOKEN' "${pass_directory}/.env" 2>/dev/null |awk '{print $NF}')
fi

if [[ -n "$pass_role_id" && -n "$pass_secret_id" ]]; then
    new_token=$(curl -s --request POST --data "{\"role_id\": \"${pass_role_id}\", \"secret_id\": \"${pass_secret_id}\"}" "${vault_server}/v1/auth/approle/login" |sed -e s'#.*"client_token":"\([^"]\+\)".*#\1#')
fi

if [[ -z "$new_token" ]]; then
    >&2 echo "Could not generate new token."
    exit 1
elif echo "$new_token" |grep -qs "invalid role or secret ID"; then
    >&2 echo "Invalid role or secret ID."
    exit 1
else
    cat <<EOF >"${temp_dir}/.env"
VAULT_ADDR = $vault_server
VAULT_TOKEN = $new_token
VAULT_WRAP_TTL = 7200
EOF
    chmod 0640 "${temp_dir}/.env"
    chown www-data:www-data "${temp_dir}/.env"
    mv "${temp_dir}/.env" "${pass_directory}/"
    rm -rf "${temp_dir}"
fi

if [[ -n "$old_token" ]]; then
    echo "Revoking old vault token."
    sleep 5
    curl -s --request POST --header "X-Vault-Token: $old_token"  "${vault_server}/v1/auth/token/revoke-self"
fi

popd >/dev/null

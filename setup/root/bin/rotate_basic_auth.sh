#!/bin/bash

set -o pipefail

PATH=/usr/bin:/bin

umask 022

###########
# variables
###########

script_canon="$(readlink -m "$0")"
absolute_path="$(dirname "${script_canon}")"
pushd "$absolute_path" >/dev/null || exit 1

script_name="${0##*/}"
lock_file="/tmp/${script_name}.lock"

temp_dir=$(mktemp -d "/tmp/${script_name}-XXXXXXXX")
pwd_file='bao.pwd'

pass_directory="/var/www/bao"

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
# fn main()
###########

buser=$(shuf -n1 ./user.list)
bpass=$(shuf -n1 ./pass.list)

tail -n1 "/etc/nginx/${pwd_file}" >"${temp_dir}/${pwd_file}"
htpasswd -nbm "$buser" "$bpass" |head -n1 >>"${temp_dir}/${pwd_file}"
chmod 0644 "${temp_dir}/${pwd_file}"
mv "${temp_dir}/${pwd_file}" "/etc/nginx/${pwd_file}"

sed -e "s/^BAO_AUTH_USER[[:space:]]\?=[[:space:]]\?[[:alnum:]]\+$/BAO_AUTH_USER = $buser/" -i ${pass_directory}/.env
sed -e "s/^BAO_AUTH_PW[[:space:]]\?=[[:space:]]\?[[:alnum:]]\+$/BAO_AUTH_PW = $bpass/" -i ${pass_directory}/.env

popd >/dev/null || exit 0

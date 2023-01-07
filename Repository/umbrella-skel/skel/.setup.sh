#!/bin/bash
# This script is called (usually once) after the skel directory is
# unpacked by the umbrella-skel installer. It will be deleted by the
# user's skel initialization script.

if [ ! -e "/etc/umbrella.conf" ] ; then
    echo 'The script must be run in an Ubrella Linux system.' ;
    exit 9
fi

. /etc/umbrella.conf

if [ `whoami` != 'umbrella-skel' ]; then
    echo "This script must be run by an umbrella-skel user."
    exit 9
fi

if [ ! -x ".setup.sh" ] ; then
    echo 'Script must be called in the skel directory itself.' ;
    exit 9
fi

# make a clean nssdb dir and cd to it
mkdir -p .pki/nssdb
pushd .pki/nssdb >/dev/null
rm -f *

# initialize certs database
certutil -d sql:. -N -f /dev/null 2>&1 | grep -v 'password file contains no data'
certutil -A -n ${REALM_NAME} -t "TC,C,T" -d sql:. -a -i /usr/local/share/ca-certificates/bcfg2ca.crt

# restore the previous working directory
popd >/dev/null

# initialize keyboard layout switcher
if [[ "${BCFG2_GROUPS}" == *\ lang-ru\ * ]]; then
    sed -i -e 's/layout="us"/layout="us,ru"/g' .config/lxqt/session.conf
fi

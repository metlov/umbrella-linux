#!/bin/bash
# This script is called before other modules in the PAM session stack
# most notably before the pam_krb5. It can be used to execute commands
# just before the session is being closed.

# do nothing for root
[[ "$PAM_USER" == "root" ]] && exit 0

# do nothing for non-local sessions
[ -n "$PAM_RHOST" ] && exit 0

# do nothing if there is no Kerberos credentials cache
klist -s || exit 0

# do nothing if there is no user's principal in the cache
(klist | grep "Default principal" | grep "$PAM_USER" &>/dev/null) || exit 0

# count the currently open sessions
SESSION_COUNT="$(w -h "$PAM_USER" | wc -l)"

if (( SESSION_COUNT == 0 )) && [[ "$PAM_TYPE" == "close_session" ]]; then
  logger "pam_session: last local user session is closed for $PAM_USER"
  export USER=$PAM_USER
  export HOME=`getent passwd "$PAM_USER" | cut -d: -f 6`
  # perform synchronization
  sudo -E -u $USER bash -c "/usr/local/bin/autounison.sh"
fi

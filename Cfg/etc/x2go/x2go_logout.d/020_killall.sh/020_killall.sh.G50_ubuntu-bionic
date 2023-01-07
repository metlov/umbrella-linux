#!/bin/bash

# Stop krenew.
# For non-system users only:
if [ `/usr/bin/id -u` -ge 1100 -a -n "$KRB5CCNAME" ] ; then
   pidFile="/tmp/pid.krb5cc_"`/bin/echo $KRB5CCNAME | /usr/bin/cut -d '_' -f 2,3`""
   /bin/cat $pidFile | /usr/bin/xargs /bin/kill
   /bin/rm -f $pidFile
fi

# Stop gpg-agent
gpgconf --kill gpg-agent

# Reset and kill ssh agent
if [ ${SSH_AGENT_PID+1} == 1 ]; then
    ssh-add -D
    ssh-agent -k > /dev/null 2>&1
    unset SSH_AGENT_PID
    unset SSH_AUTH_SOCK
fi

# Kill the remaining processes within the current session
loginctl kill-session $XDG_SESSION_ID

#!/bin/bash
# krenew.sh
# Starts krenew, once for each kerberos session ($KRB5CCNAME).
# Requires the kstart package.

# For non-system users only:
if [ `/usr/bin/id -u` -ge 1100 -a -n "$KRB5CCNAME" ] ; then

# See if there's a PID file associated with this session's ticket cache
# already..
   my_tkt_file=$(echo $KRB5CCNAME | sed -e 's/FILE://' )
   my_pid_file=$( echo $my_tkt_file | sed -e 's,/tmp/krb5cc,/tmp/pid.krb5cc,' )
# If no PID file exists, start krenew
   if [ ! -e ${my_pid_file} ]; then
      krenew -K 60 -b -p $my_pid_file
   fi
fi

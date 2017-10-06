#!/bin/bash

# Stop krenew.
# For non-system users only:
if [ `/usr/bin/id -u` -ge 1100 -a -n "$KRB5CCNAME" ] ; then
   pidFile="/tmp/pid.krb5cc_"`/bin/echo $KRB5CCNAME | /usr/bin/cut -d '_' -f 2,3`""
   /bin/cat $pidFile | /usr/bin/xargs /bin/kill
   /bin/rm -f $pidFile
fi

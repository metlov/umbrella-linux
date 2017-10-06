#!/bin/tcsh
# krenew.csh
# Starts krenew, once for each kerberos session ($KRB5CCNAME).
# Requires the kstart package.
if ( "${LOGNAME}" != "root" ) then

  set my_tkt_file=`echo $KRB5CCNAME | sed -e 's/FILE://' `
          set my_pid_file=`echo $my_tkt_file | sed -e 's,/tmp/krb5cc,/tmp/pid.krb5cc,'`

  if ( ! -e $my_pid_file ) then

     krenew -K 60 -b -p $my_pid_file

  endif

endif

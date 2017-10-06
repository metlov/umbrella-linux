#!/bin/bash
PCAT="/bin/cat"
PCOMM="/usr/bin/comm"
PECHO="/bin/echo"
PGREP="/bin/grep"
PHEAD="/usr/bin/head"
PLDAPSEARCH="/usr/bin/ldapsearch"
PSED="/bin/sed"
PSORT="/usr/bin/sort"
PUNIQ="/usr/bin/uniq"

. /usr/local/bin/umbrella.sh        # source Umbrella Linux variables

HOSTS=`${PLDAPSEARCH} -x '(|(objectClass=gotoWorkstation)(objectClass=goServer))' cn | ${PGREP} cn: | ${PSED} -e 's/cn: //g'`
# append domain name if necessary
for HOST in ${HOSTS} ; do
    if [[ ! $i == *.* ]] ; then
      HOST=${HOST}.${DOMAIN_NAME}
    fi
    NEWHOSTS=${NEWHOSTS:+$NEWHOSTS }${HOST}
done
HOSTS=$( embed_newline $NEWHOSTS )
#echo hosts: ${HOSTS}

#LDAP sanity check
RES=`${PCOMM} -23 <(${PECHO} -e $ALL_SERVERS | ${PSED} -e 's/ /\n/g' | ${PSORT} | ${PUNIQ}) <(${PECHO} -e $HOSTS | ${PSED} -e 's/ /\n/g' | ${PSORT} | ${PUNIQ}) | ${PHEAD} -1`
if [ ! -z "$RES" -a "$RES" != " " ]; then
    echo "LDAP sanity check failure. The servers must always be present in LDAP."
    exit 1
fi

exit 0

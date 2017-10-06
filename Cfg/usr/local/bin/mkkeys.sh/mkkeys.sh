#!/bin/bash
. /usr/local/bin/umbrella.sh        # source Umbrella Linux variables

PCAT="/bin/cat"
PCHOWN="/bin/chown"
PCHMOD="/bin/chmod"
PCOMM="/usr/bin/comm"
PECHO="/bin/echo"
PGREP="/bin/grep"
PHEAD="/usr/bin/head"
PLDAPSEARCH="/usr/bin/ldapsearch"
PLS="/bin/ls"
if [ -f /etc/gosa/hostmanager.keytab ]; then
    PKADMIN="/usr/bin/kadmin -p hostmanager/${HOST_CONFIG} -k -t /etc/gosa/hostmanager.keytab"
elif [ -f /etc/fusiondirectory/hostmanager.keytab ]; then
    PKADMIN="/usr/bin/kadmin -p hostmanager/${HOST_CONFIG} -k -t /etc/fusiondirectory/hostmanager.keytab"
else
    echo "hostmanager/${HOST_CONFIG} keytab not found in /etc/{gosa,fusiondirectory}."
    exit 1
fi
PMKTEMP="/bin/mktemp"
PMV="/bin/mv"
PRM="/bin/rm"
PPERL="/usr/bin/perl"
PSED="/bin/sed"
PSORT="/usr/bin/sort"
PUNIQ="/usr/bin/uniq"
IPCLASS="/usr/local/bin/ipclass"

CHECK_KEYTABS=1
# parse options
if [ $# -ge 1 ]; then
    if [ $# -gt 1 ]; then
        echo "There can be only one flag to this script."
        exit 1
    fi
    if [ $1 == "-f" ]; then
        unset CHECK_KEYTABS
    else
        echo "Usage: " `basename "$0"` "[ -f ]"
        echo "The flag -f forces creation of server keytabs if they do not exist."
        exit 1
    fi
fi


# lock the script from running twice
LOCKFILE=/var/run/`basename "$0"`.lock
if ( set -o noclobber; echo "$$" > "$LOCKFILE") 2> /dev/null; then
    trap 'rm -f "$LOCKFILE"; exit $?' INT TERM EXIT HUP QUIT PIPE

    if [ "$(id -u)" != "0" ]; then
        echo "This script must be run as root" 1>&2
        exit 1
    fi

    if ! /usr/local/bin/ldapsanity.sh; then
        echo "LDAP server is not sane, exiting." 1>&2
        exit 1
    fi

    HOSTS=`${PLDAPSEARCH} -x '(|(objectClass=gotoWorkstation)(objectClass=goServer))' cn | ${PGREP} cn: | ${PSED} -e 's/cn: //g'`
    # append domain name if necessary
    for HOST in ${HOSTS} ; do
        if [[ ! $HOST == *.* ]] ; then
          HOST=${HOST}.${DOMAIN_NAME}
        fi
        NEWHOSTS=${NEWHOSTS:+$NEWHOSTS }${HOST}
    done
    HOSTS=$( embed_newline $NEWHOSTS )
    #echo hosts: ${HOSTS}

    KEYTABDIR=/var/lib/bcfg2/Cfg/etc/krb5.keytab
    if [ ! -d $KEYTABDIR ]; then
        echo "Keytab directory " $KEYTABDIR " does not exist, exiting." 1>&2
        exit 1
    fi
    if [ ! -w $KEYTABDIR ]; then
        echo "Keytab directory " $KEYTABDIR " is not writable, exiting." 1>&2
        exit 1
    fi
    KEYTABPATH=${KEYTABDIR}/krb5.keytab.H_
    KEYS=`${PLS} ${KEYTABPATH}* | ${PSED} -e "s|${KEYTABPATH}||g"`

    #Keytab sanity check
    RES=`${PCOMM} -23 <(${PECHO} -e $ALL_SERVERS | ${PSED} -e 's/ /\n/g' | ${PSORT} | ${PUNIQ}) <(${PECHO} -e $KEYS | ${PSED} -e 's/ /\n/g' | ${PSORT} | ${PUNIQ}) | ${PHEAD} -1`
    if [ ! -z ${CHECK_KEYTABS+x} ]; then
        if [ ! -z "$RES" -a "$RES" != " " ]; then
            echo "Keytabs sanity check failure. The servers must always have their keytabs present."
            exit 1
        fi
    fi

    CLIENTSPATH="/var/lib/bcfg2/Metadata/clients.xml"
    if [ ! -f $FILE ]; then
        echo "Clients list " $CLIENTSPATH " does not exist, exiting." 1>&2
        exit 1
    fi
    BCFG2HOSTS=`${PCAT} ${CLIENTSPATH} | ${PGREP} '<Client ' | ${PSED} -e 's/<Alias name=\".*\"/>/g' | ${PSED} -e 's/^.*name=\"//g' | ${PSED} -e 's/\" .*>//g'`
    #echo bcfg2 hosts: ${BCFG2HOSTS}

    NOKEYS=`${PECHO} -e "${HOSTS}\n${KEYS}\n${KEYS}" | ${PSORT} | ${PUNIQ} -u`
    #echo nokeys: ${NOKEYS}

    NOBCFG2HOSTS=`${PECHO} -e "${HOSTS}\n${BCFG2HOSTS}\n${BCFG2HOSTS}" | ${PSORT} | ${PUNIQ} -u`
    #echo no bcfg2 hosts: ${NOBCFG2HOSTS}

    EXTRAKEYS=`${PECHO} -e "${KEYS}\n${HOSTS}\n${HOSTS}" | ${PSORT} | ${PUNIQ} -u`
    #echo extrakeys: ${EXTRAKEYS}

    EXTRABCFG2HOSTS=`${PECHO} -e "${BCFG2HOSTS}\n${HOSTS}\n${HOSTS}" | ${PSORT} | ${PUNIQ} -u`
    #echo extra bcfg2 hosts: ${EXTRABCFG2HOSTS}

    ADDKEYS=`${PECHO} -e "${NOKEYS}\n${BCFG2HOSTS}\n${BCFG2HOSTS}" | ${PSORT} | ${PUNIQ} -u`
    #echo add keys: ${ADDKEYS}

    BCFG2GREP=`${PECHO} ${EXTRABCFG2HOSTS} | sed -e 's/ /\\\|/g' | sed -e 's/\./\\\./g'`
    #echo ${BCFG2GREP}
    if [ ! -z "$BCFG2GREP" -a "$BCFG2GREP" != " " ]; then
        ${PECHO} "Deleting bcfg2 hosts: " ${EXTRABCFG2HOSTS}
        CLIENTSTMP=`${PMKTEMP}`
        ${PGREP} -v "${BCFG2GREP}" ${CLIENTSPATH} >$CLIENTSTMP
        ${PCHOWN} bcfg2:bcfg2 ${CLIENTSTMP}
        ${PCHMOD} g+rw ${CLIENTSTMP}
        #${PCAT} ${CLIENTSTMP}
        #${PLS} -la ${CLIENTSTMP}
        ${PMV} ${CLIENTSTMP} ${CLIENTSPATH}
        ${PRM} -f ${CLIENTSTMP}
    fi

    if [ ! -z "$EXTRAKEYS" -a "$EXTRAKEYS" != " " ]; then
        ${PECHO} "Deleting extra Kerberos keys: " ${EXTRAKEYS}
        for KEY in ${EXTRAKEYS}; do
            ${PRM} -f ${KEYTABPATH}${KEY}
            # now delete the host and nfs principals
            ${PKADMIN} -q "delprinc -force host/${KEY}"
            ${PKADMIN} -q "delprinc -force nfs/${KEY}"
        done
    fi

    if [ ! -z "$ADDKEYS" -a "$ADDKEYS" != " " ]; then
        ${PECHO} "Adding Kerberos keys for: " ${ADDKEYS}
        for KEY in ${ADDKEYS}; do
            ${PKADMIN} -q "addprinc -randkey host/${KEY}"
            ${PKADMIN} -q "addprinc -randkey nfs/${KEY}"
            KEYTABTMP=`${PMKTEMP}`
            ${PKADMIN} -q "ktadd -k ${KEYTABTMP}R host/${KEY} nfs/${KEY}"
            ${PCHOWN} bcfg2:bcfg2 ${KEYTABTMP}R
            ${PCHMOD} g+rw ${KEYTABTMP}R
            ${PMV} ${KEYTABTMP}R ${KEYTABPATH}${KEY}
            ${PRM} -f ${KEYTABTMP}R
            ${PRM} -f ${KEYTABTMP}
        done
    fi

    if [ ! -z "$NOBCFG2HOSTS" -a "$NOBCFG2HOSTS" != " " ]; then
        ${PECHO} "Adding new bcfg2 clients: " ${NOBCFG2HOSTS}
        for KEY in ${NOBCFG2HOSTS}; do
            IP=`${PLDAPSEARCH} -x "(&(objectClass=dhcpHost)(cn=$KEY))" dhcpStatements | ${PGREP} '^dhcpStatements: fixed-address' | ${PSED} -e 's/^.*fixed-address //g'`
            if [ -z "$IP" ]; then
                # try with a short name (without the domain part)
                IP=`${PLDAPSEARCH} -x "(&(objectClass=dhcpHost)(cn=${KEY%%.${DOMAIN_NAME}}))" dhcpStatements | ${PGREP} '^dhcpStatements: fixed-address' | ${PSED} -e 's/^.*fixed-address //g'`
            fi
            echo -n $KEY $IP
            CLASS=`${IPCLASS} ${IP}`
            if [ ! -z "$CLASS" -a "$CLASS" != " " ]; then
                ${PPERL} -pi -e "s/<\!--PLACEHOLDER-->/<Client profile=\"$CLASS\" name=\"$KEY\" version=\"1.3.3\"\/>\n  <\!--PLACEHOLDER-->/g" ${CLIENTSPATH}
            else
                echo "The class of workstation $KEY ($IP) can't be determined."
                echo "Please fix the IP address or add to the clients.xml manually."
            fi
        done
    fi

    # clean up the lock fiile, and release the trap
    rm -f "$LOCKFILE"
    trap - INT TERM EXIT
else
    echo "Lock Exists: $LOCKFILE owned by $(cat $LOCKFILE)."
fi

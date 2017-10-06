#!/bin/bash
PAWK=/usr/bin/awk
PGETENT=/usr/bin/getent
PCP=/bin/cp
PCHOWN=/bin/chown
PCHMOD=/bin/chmod
PLS=/bin/ls
PCAT=/bin/cat
PDATE=/bin/date
PGROUPS=/usr/bin/groups
PPPID=/usr/bin/id
PSORT=/usr/bin/sort
PUNIQ=/usr/bin/uniq
PECHO=/bin/echo
PENCFS=/usr/bin/encfs
PFUSERMOUNT=/bin/fusermount
PPERL=/usr/bin/perl
USERLIST=`${PGETENT} passwd | ${PAWK} -F'[/:]' '{if ($3 >= 1100 && $3 != 65534) print $1}'`
HOMESLIST=`${PLS} /export/home/`
nohomes=`${PECHO} -e "${USERLIST}\n${HOMESLIST}\n${HOMESLIST}" | ${PSORT} | ${PUNIQ} -u`
for user in ${nohomes}; do
    group=`${PPPID} -gn ${user}`
    ${PECHO} "Creating home for " ${user}:${group}
    ${PCP} -r /var/lib/umbrella-skel/unpacked/ /export/home/${user}
    ${PCHOWN} -R ${user}:${group} /export/home/${user}
    if [ -d /backups/keys ]; then
        ${PECHO} "Creating encrypted folder for ${user}"
        ${PECHO} -e "y\ny\np\n\n\n" | ${PENCFS} -S /export/home/${user}/.Шифрованные /export/home/${user}/Шифрованные
        ${PFUSERMOUNT} -u /export/home/${user}/Шифрованные
        CDATE=`${PDATE} +"%Y%m%d%H%M"`
        KEYPATH=/backups/keys/${user}-${CDATE}.encfs6.xml
        cp /export/home/${user}/.Шифрованные/.encfs6.xml ${KEYPATH}
        chmod og-rwx ${KEYPATH}
        cat >"/export/home/${user}/Шифрованные/НЕТ ДОСТУПА" << EOF
Эта шифрованная директория в данный момент не подключена.

Если Вы хотите получить доступ к сохранённым здесь Вами файлам --
обратитесь к системным администраторам.
EOF
        ${PCHOWN} -R ${user}:${group} /export/home/${user}/.Шифрованные
        ${PCHOWN} -R ${user}:${group} /export/home/${user}/Шифрованные
        ${PCHMOD} og+rx /export/home/${user}/Шифрованные
    fi
done

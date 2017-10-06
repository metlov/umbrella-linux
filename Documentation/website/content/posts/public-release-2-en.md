Title: Public release 0.2
Date: 2018-04-18 20:02
Slug: release-0.2
Lang: en

I'm happy to announce the version 0.2 of the Umbrella Linux.

### Summary of Changes:

* new "archiveworkstation" class, which works as workstation during the day, but maintains a set of system's backups during the night.
* secure all mysql server connections via SSL by default
* network topology can now be defined via lan_topology.xml and the link/switch status will be automatically monitored with Nagios.
* "localfsworkstation" class improvements for the completely offline use case.
* configuration of trusted CAs registry via firewall.xml -- a restricted set of trusted CAs, which exists in parallel with the well known CA list.
* (incomplete) initial implementation of XMPP federation with trusted systems.
* (incomplete) initial implementation of PBX (Asterisk) federation.
* (incomplete) initial implementation of Invenio host in DMZ (all the dependencies are installed and configured, but Invenio itself needs to be installed automatically).
* rewrote domain definitions in umbrella.xml, now Umbrella can be configured to host sub-domains and be a slave DNS server.
* rewrote user's skel (initial home directory contents) handling.
* automatic maintenance of CRL for Umbrella-generated x509 certificates.
* dynamic host configuration, based on their subnet.
* rewrote physical/virtual/container detection groups.
* install memtest86+ on physical machines.
* more custom OSSEC rules to improve monitoring/reduce the level of OSSEC messages SPAM.
* numerous bugfixes and enhancements of both the system and the installer.

See the
[installation instructions](/umbrella-linux/installation/)
to try Umbrella Linux yourself.

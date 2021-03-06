Title: Public release 0.4.1
Date: 2020-01-18 15:55
Slug: release-0.4.1
Lang: en

I'm happy to announce the version 0.4.1 of the Umbrella Linux.

The main focus of this release is on the LXD container mode improvements.
Currently both Ubuntu 18.04 "Bionic" and  Ubuntu 16.04 "Xenial" are supported,
but the automatic installer is only tested on "Bionic".

### Summary of Changes:

* LXD version of Umbrella now installs and runs into unprivileged containers except for the nfs server (which needs to be privileged).
* LXD version does not use NFS now for mounting home directories in containers and relies on bind mounts instead (workstations still use NFS).
* Optional external access to the mail server via SMTPS and IMAPS (configurable in firewall.xml).
* Mozilla Thunderbird E-mail client auto-configuration for all the alternative domains both from within the local network and from outside (if external mail server access is enabled).
* DNS blacklist configuration improvements for the SMTP server.
* Nagios monitoring improvements.
* Extensive OSSEC spam clean up.
* Improvements to Ubuntu 16.04 "Xenial" legacy support.
* Numerous small bugfixes.

See the
[installation instructions](/umbrella-linux/installation/)
to try Umbrella Linux yourself.

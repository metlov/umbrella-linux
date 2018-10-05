Title: Public release 0.3
Date: 2018-10-05 16:05
Slug: release-0.3
Lang: en

I'm happy to announce the version 0.3 of the Umbrella Linux.

This is, probably, the last Ubuntu Xenial based release. The work is underway on extending the support to Ubuntu Bionic.

### Summary of Changes:

* made the installer work with updated LXD (after bionic release) from xenial-backports.
* installer now works on top of freshly installed ubuntu-server (in addition to ubuntu-mini).
* x2go_thinclient now uses Debian "stretch".
* enabled cross-domain encrypted XMPP between two Umbrella Linux systems if they trust each other's root certificates.
* DNS and DHCP configuration is now generated from LDAP by own script instead of argonaut.
* removed DNS and DHCP configuration from LDAP and Fusion Directory, which greatly simplifies the host management.
* implemented host locking in Fusion Directory, which excludes the host both from DNS and DHCP configurations.
* new IP space review and address allocation script umbrella-hosts, installed on configuration server.
* new workstation registration script umbrella-addworkstation, installed on configuration server.
* switched to ulogd2 for firewall logging on router (this allows to see the firewall logs when router is an LXD container).
* new "maintenance mode" of the installer, which allows to (re-)create missing LXD containers on already installed Umbrella Linux system according to umbrella.xml specification.
* added optional backdoor server class, allowing remote itadmins to access the system in case the router is down.
* added optional DMZvpn server class and roamingworkstation profile to allow roaming Umbrella Linux systems (should be registered in Fusion Directory with location set to "roaming").
* new umbrella-mkinstall script on configuration server for generating Umbrella Linux installers for specific roaming workstations (allowing to initialize them remotely with proper VPN keys from a fresh Ubuntu Desktop install).
* more custom OSSEC rules to improve monitoring/reduce the level of OSSEC messages SPAM.
* numerous bugfixes and enhancements of both the system and the installer.

See the
[installation instructions](/umbrella-linux/installation/)
to try Umbrella Linux yourself.

Title: Public release 0.4.3
Date: 2021-12-20 18:52
Slug: release-0.4.3
Lang: en

This long-awaited release of the Umbrella Linux is focused on
migration from older Ubuntu 16.04 Xenial based systems. It still supports
both Ubuntu Xenial and Ubuntu Bionic (as well as hererogenous environment,
consisting of those), but includes migration scripts for upgrading the
Umbrella hosts. The instructions are in the file
`Documentation/1604_to_1804.txt`.

The next major release will drop the support of Ubuntu 16.04 Xenial. Please
do not forget to upgrade your systems before that.

### Summary of Changes:

* Migration scripts to rebase the individual hosts from Ubuntu Xenial to Ubuntu Bionic.
* Switch to Horde for Webmail handling instead of Squirrelmail.
* The monitor host now automatically configures MRTG for router flow monitoring.
* Removed the `<net/>` tag in umbrella.xml, allowing for more flexible network configuration.
* Specify send-only or receive-only E-mail robots in firewall.xml.
* More OSSEC spam clean up.
* Minor bugfixes.

See the
[installation instructions](/umbrella-linux/installation/)
to try Umbrella Linux yourself.

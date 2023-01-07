Title: Public release 0.5.0
Date: 2023-01-07 17:33
Slug: release-0.5.0
Lang: en

This release updates Umbrella Linux to support Ubuntu 22.04 "Jammy
Jellyfish" and Ubuntu 18.04 "Bionic Beaver". The latter support is of
production quality, the former is of alpha quality (meaning that new
features are still being developed and the release was not yet tested in
production). Note that Ubuntu 20.04 "Focal Focca" will not be supported
by Umbrella Linux.

Development of Umbrella Linux goes in conjunction with updates to
[our own fork of bcfg2 configuration management system](https://github.com/metlov/bcfg2).
The latest set of changes both to bcfg2 and to Umbrella Linux allows to
generate XML configuration "offline" in such a way that it is literally 
(character-by-character) stable/reproducible across two currently
supported distributions and two (formerly supported by Umbrella and still
supported by bcfg2) python versions. It is a very useful feature for
testing. A sample testing script and instructions are included in
`Local/testing` .

### Summary of Changes:

* Support for Ubuntu 22.04 "Jammy Jellyfish".
* Removed support for Ubuntu 16.04 "Xenial Xerus".
* Switched to Python 3 in configuration templates and everywhere.
* New installation script, based on pre-generating configuration for the whole Umbrella cluster offline.
* Literally stable configuration generation across all supported platforms.
* UEFI (and BIOS as before) network boot for workstation installer and terminals.
* Switch from ejabberd to prosody for jabber support with GSSAPI authentication.
* Specify external networks with allowed AXFR transfers for DNS data in firewall.xml.
* More OSSEC spam clean up (still more work is needed on jammy).
* Minor bugfixes.

See the
[installation instructions](installation/)
to try Umbrella Linux yourself.

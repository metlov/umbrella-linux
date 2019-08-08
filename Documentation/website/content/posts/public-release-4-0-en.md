Title: Public release 0.4.0
Date: 2019-08-08 15:35
Slug: release-0.4.0
Lang: en

I'm happy to announce the version 0.4.0 of the Umbrella Linux.

This is the first release, featuring the Ubuntu 18.04 "Bionic" support.
It also supports legacy systems, based on Ubuntu 16.04 "Xenial".

### Summary of Changes:

* Ubuntu 18.04 "Bionic" support.
* More documentation and installation examples (see Documentation/).
* Enabled new features of spamassasin (>=3.30).
* Language support selection groups to streamline the language
  support configuration of individual bundles.
* OSSEC system scan is now scheduled randomly once per day.
* Ejabberd now uses kernel keyring for Kerberos credentials.
* Redesigned PAM stack on "Bionic", which is now based around sssd.
* Network configuration on "Bionic" is now based on netplan.io,
  while "Xenial" stays with ifconfig.
* Mozilla Firefox is now the default WWW browser on "Bionic", while
  "Xenial" version stays with Chromium.
* Ubuntu 16.04 "Xenial" legacy support.
* Switched to HWE 16.04 kernel and x.org on "Xenial".
* Numerous small bugfixes.
* Installation speed up with the help of eatmydata.

See the
[installation instructions](/umbrella-linux/installation/)
to try Umbrella Linux yourself.

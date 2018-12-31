Title: Public release 0.3.2
Date: 2018-12-31 13:20
Slug: release-0.3.2
Lang: en

I'm happy to announce the version 0.3.2 of the Umbrella Linux.

This is an intermediate release before the Bionic port.

Updating the running system will require: 
1) stopping the bcfg2 server (`/etc/init.d/bcfg2-server stop`);
2) resetting bcfg2-reports database (DROP/CREATE);
3) clearing Packages cache in /var/lib/bcfg2 (`rm Packages/cache/*`)
4) updating the bcfg2 packages (`apt-get update`/`upgrade`)
5) updating the /var/lib/bcfg2 repository (`git pull -a`);
6) migrating the SSL keys and certificates with the script in
`Documentation/hacks/migration`;
7) deleting SSLCA/ ;
8) and, finally, starting the bcfg2 server again.

### Summary of Changes:

* removed Ubuntu 14.04 "Trusty" support.
* upgraded bcfg2 to the version 1.4.0pre2 with
  [Umbrella-specific patches](https://github.com/metlov/bcfg2).
* substantial installation speed up.

See the
[installation instructions](/umbrella-linux/installation/)
to try Umbrella Linux yourself.

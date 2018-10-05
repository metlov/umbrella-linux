Title: About Umbrella Linux
Date: 2018-10-05 18:39
Category: docs
Slug: about
Lang: en

Umbrella Linux is a collaboration platform for system's administrators
to work on a complex and full-featured IT infrastructure of an organization 
(big or small).

At the moment, Umbrella Linux installs and maintains a parametrized Unix
systems cluster (including both servers and workstations) with 
[LDAP](https://en.wikipedia.org/wiki/Lightweight_Directory_Access_Protocol)-based
centralized users and workstation management;
[Kerberos](https://web.mit.edu/kerberos/)-based single sign on;
[NFSv4](https://en.wikipedia.org/wiki/Network_File_System) shared filesystem;
DNS zone management, organization's web server;
external and internal E-mail services (including, optionally configured 
Webmail interface);
Jabber-based intra-organization conferencing
(with optional transparent encrypted communication across a set of trusted
Umbrella Linux instances);
roaming (but fully controlled and configured) workstations, 
communicating via VPN;
[X2Go](https://wiki.x2go.org/)-based
terminal service for local underpowered machines and for remote graphical
access to the system;
user's workstations can be (re)deployed locally
by booting from the network or remotely via an automatic individually-generated
installation script.

The security and monitoring features include the external firewall and DMZ
for the externally-accessible services (except for the SSH protocol), 
[Nagios](https://www.nagios.org/)
for checking the services status, [OSSEC](https://ossec.github.io/)
 for log analysis and active response
to known threats, and, of course, the non-stop control of the confuguration
of all the servers and workstations using [BCFG2](http://bcfg2.org/).

These are just the major features, much more is inside.

All in all the Umbrella Linux aims at including all the services required for
an independent functioning of small/medium/large organization implementing
its own cloud in the information space.
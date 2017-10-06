Title: What's our mission ?
Date: 2017-09-26 22:10
Slug: mission
Lang: en

The goal is to counteract the Internet service centralization by providing
a local fully-featured user workspace and internet service platform with 
as much externally accessible open standards based services as possible.
For most individual home users the usual Linux Distribution (such as Debian,
Ubuntu or Linux Mint) already helps to achieve this goal.

Groups of users, however, demand more services as well as separation
of rights of different users. Normally, within the Free Software based
approach, this is solved by creating a number of real/virtual servers,
providing the services for users (such as mail, file sharing, remote access,
chat and conferences) as well as public services 
(such as organization WWW site). Also, within the publically accessible
setting, there is an additional requirement of system monitoring to detect
(deal with) service interruptions and attacks, which requires even more
local services and personnel (system administrators) to monitor them.
Not every group of users can afford this price of localization to be the
master of their own data and services, only larger ones (such as corporations
or public institutions) can. Even then, such local systems are often built in
an ad-hoc manner, each is unique (but, at the same time, consists predominantly
of duplicate work, since common stuff like domains, smtp and imap servers,
etc. get configured over and over from scratch). System's administrators
of different organizations rarely collaborate.

The Umbrella Linux is intended as a platform for such a collaboration
to lower the barrier of entry for smaller groups into the business
of having their owl local information system, to minimize the work duplication
and offer easier to maintain alternative for the groups, which
already have their local ad-hoc systems.

How can we do that ?

Simply. First of all, the idea is to build upon the work of others, who
created a modern Linux Distribution (such as Ubuntu Linux). The Umbrella
Linux is mostly dedicated to configuring (as opposed to compiling) software
(not that it does not utilize its own packages here and there). This
configuration can be very complex, since in a typical Unix environment there
are many interlinked and interdependent services spread across a number
of independent servers (physical or virtual). In Umbrella Linux it is described
in XML using the language of a configuration management system called
[bcfg2](http://bcfg2.org/). This configuration is fully parametrized by a
large number of systemwide Umbrella-specific configuration options, specified
in a set of XML files (these options make different Umbrella Linux 
installations unique) -- Umbrella Linux properties. Furthermore, all the
configuration description and Umbrella-specific packages live in a git
repository, which facilitates their independent distributed development.
Local system administrators can develop additional features for their own
systems add more useful properties to XML files and, if they wish,
submit their work to the main repository in the form of patches for
other administrators to use.

We can go on an on detailing various specific features of Umbrella Linux, which
will be the subject of further posts and pages in this blog. But for now
let us summarize that **Umbrella Linux is** first and foremost
**a collaboration platform for system's administrators.**
Its constantly expanding set of features is just a byproduct of this
collaboration.

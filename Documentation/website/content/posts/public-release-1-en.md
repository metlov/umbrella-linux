Title: Initial public release 0.1
Date: 2017-10-06 22:10
Slug: release-0.1
Lang: en

I'm happy to announce the initial public release of the Umbrella Linux.

### State of the project:

The project is, actually, two years old (if we count only the time,
when the its bcfg2 repository started to be parametrized). It was created
from scratch in 2013 by me K.L.M. and two of my friends.
It is already used in production across several organizations small
(tens of employees), medium (hundreds) and large (thousands geographically
distributed personnel). This is, of course due to the power of Debian and
Ubuntu, which, if configured right, are very powerful and stable platforms
 suitable for production. The Umbrella Linux itself (the thing that
"configures stuff right") is of alpha quality, since we are still
far from the point of reaching the full or complete feature set. There are
many things still to improve and implement. Newertheless, what is implemented
usually works well.

This public release is made possible by the recent port of
the Umbrella Linux to an 
[LXD virtualization platform](https://linuxcontainers.org/lxd/),
which, in turn,
made it easy to write an installer to automatically deploy the system
on a single virtual (or physical) server machine. Please note, that
unlike QEMU/KVM version the LXD port should be considered much less
mature, stable and secure (as LXD platform itself).
The installer had received only a minimal testing and is not mature at all.
Nevertheless, it opens a relatively
easy possibility to try Umbrella Linux in a safe virtual setting.
It should be possible then (but was not tested yet) to migrate the Umbrella
Linux virtual machines to KVM one by one to gradually deploy the automatically
installed LXD version of the system.

See the
[installation instructions](/umbrella-linux/installation/)
to try Umbrella Linux yourself.

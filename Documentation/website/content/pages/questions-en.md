Title: FAQ
Date: 2022-01-07 21:08
Category: docs
Slug: questions
Lang: en

- What sets Umbrella Linux apart from other distributions ?  
Traditionally, Linux distributions were created to help install Linux onto a
single PC. Umbrella's goal from the start is to manage the whole cluster
of Linux workstations and servers in a coherent manner.
- Why Ubuntu ?  
Linux is made of hundreds of thousands of software pieces -- programs or
libraries, each developed by (an) independent (team of) authors. Linux
distributions compile, pre-configure and package these independent programs
into a coherent set. As far as compilation goes, there is a pressure on
distributions to make the compiled-in configuration as dynamic as possible
for highest versatility. Therefore, the binaries are not that much different
across the distributions. Because Umbrella does its own run-time configuration
of these binaries, there is not much difference -- which distribution's
binaries to use as a base.  
Ubuntu was chosen, because it offers the widest hardware support at the
moment. However, because of Ubuntu brave experimentation with snap and
such, it might very well be possible that future versions of Umbrella
will be based on Debian directly. Even if this switch does happen, the
difference for the user and for the system administrator will be minimal.
- Why bcfg2 ? It is a dead project !  
Automatic system configuration has a long history, littered with remnants
of bit rottent software. Many approaches were tried and forgotten. On the
other hand, while every program is written in some programming language,
it is not the language, which matters, but what the program itself does.
Similarly, the most valuable part of Umbrella is the description of the
configuration itself.
It can be expressed in another language as well.  
Still, Bcfg2 is very versatile and convenient language ! It can be argued
that it is the best in its class. Dead project ?
No problem, Umbrella is based on
[its own fork of bcfg2](https://github.com/metlov/bcfg2),
which will be maintained as long as Umbrella does.
- What is the use case ?  
Umbrella can take over all the IT infrastructure of an organization -- both
small and large. It can help configure and manage workstations, users,
software updates, network communication services (like E-mail, jabber,
WebDAV calendars, organization web site) in a coherent, secure and testable
manner. It can also serve as a base for development of organization's own
software and services without reinventing the IT
infrastructure management. With some local configuration adjustments (on top
of what is provided as a standard Umbrella Linux distribution) on a proper
hardware, it can scale from managing dozens to 1000s of computers.

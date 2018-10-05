Title: Installation
Date: 2017-10-05 12:39
Category: docs
Slug: installation
Lang: en

First, please think twice before installing. Do you really need it ?

There is so little information about Umbrella Linux right now that
the chances are you do not know what you are doing. In this case
stop ! Talk to somebody, who can explain things to you (IRL or via
the Internet), or wait until this site contains more information.

The best way to try the Umbrella Linux is to install it into a VirtualBox
virtual machine (this is the only installer mode, which have received
at least a marginal testing so far).

1. Download the Ubuntu Xenial minimal installation image 
(either [32-bit](http://archive.ubuntu.com/ubuntu/dists/xenial/main/installer-i386/current/images/netboot/mini.iso)
or [64-bit](http://archive.ubuntu.com/ubuntu/dists/xenial/main/installer-amd64/current/images/netboot/mini.iso)
version)
and save it to your hard drive.

2. Create the Virtual Machine for Umbrella to live in. For a typical
bare installation (with almost no space for user home directories or mail)
you will need a VM with 25Gb of hard drive, 3Gb RAM and a couple of virtual
CPUs. When creating a VM connect the above-downloaded Ubuntu minimal
installation image to its virtual CD drive.

    *Optional:* it also can be a good idea to have a secondary network card installed
on your physical machine so that you can later pass it through to the Umbrella
VM and have it manage the network behind it (installing and using workstations
and terminals). Without the secondary network card (or other means to connect
to the "pub" bridge) you will only be able to access your new system as if you
are outside of it.

3. Boot the virtual machine and install the Ubuntu Xenial Linux with all
the default settings (basically, agreeng to defaults all the time and
not selecting any tasks via tasksel).
Create some temporary user with a simple password.

4. After the installation is finished, log in as a temporary user you have
created. Use the `sudo -i` command to become root and download
all the files from [this web directory](/umbrella-linux/installer/) to your
root's home ( e.g. with
`wget -e robots=off -R 'index.html*' -r -np -nd -nc -l 1 https://metlov.github.io/umbrella-linux/installer/` ).
These are all simple text files and `./umbrella-install`
is a bash script, please `chmod +x ./umbrella-install` after downloading.
If you wish to generate unique passwords and certificates for your system
(which is, basically, any time if you change the system name or location
in umbrella.xml) -- delete the `umbrella_keys.xml` file (it will be recreated
by the installer with your own settings). Do not install any additional
packages at this point (may be except `mc` and `aptitude`).

5. Review the `*.xml` files and alter the settings there (since there is no
documentation yet, you will need to consult somebody if you want to do
some non-trivial changes). Again, if you have changed the system name (or
location, or just in case) -- delete the umbrella_keys.xml to have it
regenerated.

5. Run the "./umbrella-install" script you've also downloaded on the previous
step. It should do everything automatically, but may require a single hard
(with "Power off"/"Power on") reboot of the virtual machine. After reboot
run the script again and it should finish the installation. It may take
several hours to complete depending on the speed of your hard drive.
Ejoy your new Umbrella Linux system !

What to do next ?

With the help of VirtualBox you can NAT some port (say 10022) to the port 22 of
the first interface of the VM to be able to connect to it. You can log in
as a first admin user, specified in umbrella.xml with a simple password,
communicated to you at the end of the installation process. Then you can either
connect via ssh or via X2Go client (be sure to select "Custom desktop" in sesion
type and type there `umbrella-session`). From the graphical session you can run
chromium browser and visit `https://gosa.<your-domain>/` for a
Fusion Directory interface to your users, groups and workstations.
Then you can connect a secondary interface ty the second VM port,
register a couple of workstations via Fusion Directory and try to boot
them over the network to try terminals and local workstation installation.

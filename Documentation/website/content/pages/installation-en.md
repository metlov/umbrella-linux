Title: Installation
Date: 2019-08-08 14:56
Category: docs
Slug: installation
Lang: en

First, please think twice before installing. Do you really need it ?

There isn't much information about Umbrella Linux right now. The
chances are that you do not know what you are doing. In this case
stop ! Talk to somebody, who can explain things to you (IRL or via
the Internet), or wait until this site contains more information.

The best way to try the Umbrella Linux is to install it into a VirtualBox
virtual machine.

1. Download the Ubuntu 18.04 "Bionic" minimal installation image 
(either [32-bit](http://archive.ubuntu.com/ubuntu/dists/bionic/main/installer-i386/current/images/netboot/mini.iso)
or [64-bit](http://archive.ubuntu.com/ubuntu/dists/bionic/main/installer-amd64/current/images/netboot/mini.iso)
version) and save it to your hard drive. Installation over a freshly installed
Server variant of Ubuntu is also possible.

2. Create the Virtual Machine for Umbrella Server to live in. For a typical
bare installation (with almost no space for user home directories or mail)
you will need a VM with 25Gb of hard drive, 3Gb RAM and a couple of virtual
CPUs. When creating a VM load the above-downloaded Ubuntu minimal
installation image into its virtual CD drive.

    When installing into VirtualBox with bridged network adapter,
please ensure that its promiscous mode is allowed.

    *Optional:* it can also be a good idea to have a secondary network card
installed on your Umbrella Server, which will manage the network behind it
(installing and using workstations and terminals). Without the secondary
network card (or other means to connect to the "pub" bridge) you will only
be able to access your new system as if you are outside of it.

3. Boot the virtual machine and install Ubuntu (minimal or server ISO)
with all the default settings (basically, agreeng to defaults all the time
and not selecting any tasks via tasksel).
Create some temporary user with a simple password.

4. After the installation is finished, log in as a temporary user you have
created. Use the `sudo -i` command to become root and download
all the files from [this web directory](/umbrella-linux/installer/) to your
root's home ( e.g. with
`wget -e robots=off -R 'index.html*' -r -np -nd -nc -l 1 https://metlov.github.io/umbrella-linux/installer/` ).
These are all simple text files and `./umbrella-install`
is a bash script, please `chmod +x ./umbrella-install` after downloading.
If you wish to generate unique passwords and certificates for your system
(it is necessary, for example, if you change the system name or location
in umbrella.xml) -- delete the `umbrella_keys.xml` file (it will be recreated
by the installer with your own settings). Do not install any additional
packages at this point (may be except `mc` and `aptitude`).

5. Review the `*.xml` files and alter the settings there.
You may want to check the name of vmhost (must coincide with the name
of the Ubuntu system, used for installation),
the definitions of the extif on router (the IP and
gatewey addresses) and vmhost (mac address) to ensure they correspond to
the ones, reported by `ip addr` and `ip route`.
Since there is no documentation yet, you will need to consult somebody
if you want to do some non-trivial changes.

5. Run the "./umbrella-install" script you've also downloaded on the previous
step. It should do everything automatically. It may take several hours to
complete depending on the speed of your hard drive.
Ejoy your new Umbrella Linux system !

What to do next ?

With the help of VirtualBox you can NAT some port (say 10022) to the port 22 of
the first interface of the VM to be able to connect to it. You can log in
as a first admin user, specified in umbrella.xml with a simple password,
communicated to you at the end of the installation process. Then you can either
connect via ssh or via X2Go client (be sure to select "Custom desktop" in sesion
type and type there `umbrella-session`). From the graphical session you can
visit `https://gosa.<your-domain>/` in a browser for Fusion Directory
interface to your users, groups and workstations. Then you can connect a NAT
or Internal VirtualBox network as a secondary interface of the Umbrella Server
VM, create a couple of workstation VMs on this network, register their MAC
addresses in Fusion Directory and try to boot them via PXE to try
terminal mode and local workstation installation.

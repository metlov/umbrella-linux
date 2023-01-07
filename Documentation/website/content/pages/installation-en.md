Title: Installation
Date: 2023-01-07 12:23
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

1. Download the
[Ubuntu 22.04 "Jammy" server installation image](https://releases.ubuntu.com/releases/22.04/ubuntu-22.04.1-live-server-amd64.iso)
and save it to your hard drive.

2. Create the Virtual Machine (VM) for Umbrella Server to live in.
For a typical bare installation (with almost no space for user home
directories or mail) you will need a VM with 15Gb of hard drive, 
4Gb RAM and a couple of virtual CPUs.

    When installing into VirtualBox with bridged network adapter,
please ensure that its promiscous mode is allowed.

    *Optional:* it can also be a good idea to have a secondary network card
on your Umbrella Server (or the Virtualbox VM) to connect the local network
(containing workstations and terminals). Without the secondary network card
(or other means to connect to the "pub" bridge) you will only be able to
access your new system as if you are outside of it.

3. Load the installation image into the virtual CD drive of the VM, boot and
install the Ubuntu Server with minimal settings (no optional software).
Create some temporary user with a simple password.

4. After the installation is finished, log in as a temporary user you have
created. Use the `sudo -i` command to become root and download
all the files from [this web directory](../installer/) to your
root's home ( e.g. with
`wget -e robots=off -R 'index.html*' -r -np -nd -nc -l 1 https://metlov.github.io/umbrella-linux/installer/` ).
These are all simple text files and `./umbrella-install`
is a bash script, please `chmod +x ./umbrella-install` after downloading.
If you wish to generate unique passwords and certificates for your system
(it is necessary, for example, if you change the system name or location
in umbrella.xml) -- delete the `umbrella_keys.xml` file (it will be recreated
by the installer with your own settings). Do not install any additional
packages at this point (may be except `gpm`,  `mc` and `aptitude`).

5. Review the `*.xml` files and alter the settings there. You may want to
check the name of vmhost (must coincide with the name of the Ubuntu
system, used for installation), the definition of the extif on router
(the IP and gatewey addresses) to ensure it correspond to the external
network parameters, reported by `ip addr` and `ip route`. It is important
that in umbrella.xml the router gets its external interface's MAC set to
the real MAC of the external interface of the server (check with `ip
addr`). The external interface of the vmhost in umbrella.xml then can be
set to an arbitrary free MAC.

Since there is no documentation yet, it is best to consult somebody
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
visit `https://config.<your-domain>/` in a browser for Fusion Directory
interface to your users, groups and workstations. Then you can connect a NAT
or Internal VirtualBox network as a secondary interface of the Umbrella Server
VM, create a couple of workstation VMs on this network, register their MAC
addresses in Fusion Directory and try to boot them via PXE to try
terminal mode and local workstation installation.

A more detailed form of these instructions lives in
`Documentation/installer/README` file in Umbrella Linux git or in the
actual configuration of your Umbrella Linux cluster on its configuration
server (`config` by default) in the directory `/var/lib/bcfg2`.

# this file defines some basic networking variables for the host
# and for the system as a whole

import ipcalc
from genshi.template import TemplateError

# renders (as a string) /etc/network/interfaces stanza for a given interface
# TODO: this function gets uglier and uglier with each iteration
#       interface generation needs to be done better
def interface_stanza( interface, networks, domain_name, metadata, routerifs, funcifs, static_routes ):
  netnames = {
    'extif':'External network (accesible from Internet directly)',
    'intif':"Legacy organization's intranet",
    'pubif':'Network for public workstations with Internet access',
    'secif':'Network for secured workstations with no Internet access',
    'DMZif':'DMZ for Internet-accessible servers',
    'vpnif':'Network for workstations, connecting via VPN',
    'winif':'Network for Windows workstations and other insecure devices'
  }
  r=""
  bridged=interface.find('bridged')
  bridgeddev=None
  if bridged is not None:
    bridgeddev=bridged.text
    if bridgeddev <> 'none':
        r+='# bridged'+ ((' to '+netnames[interface.tag]) if (interface.tag in netnames) else '')+'\n'
        r+='auto '+bridgeddev+'\n'
        r+='iface '+bridgeddev+' inet manual\n'
        r+='\n'
  if interface.tag in netnames:
    r+='# ' + netnames[interface.tag]+'\n'
  dev=interface.find('dev').text
  r+= 'auto '+dev+'\n'
  ip=interface.find('ip')
  if ip is None:
    r+= 'iface '+dev+' inet manual\n'
  elif ip.text is None or not ip.text.strip():
    r+= 'iface '+dev+' inet dhcp\n'
  else:
    r+= 'iface '+dev+' inet static\n'
    r+= '    address '+ip.text.strip()+'\n'
    r+= '    netmask '+str(networks[interface.tag].netmask())+'\n'
    r+= '    network '+str(networks[interface.tag].network())+'\n'
    gateway=interface.find('gateway')
    if gateway is not None:
      r+= '    gateway '+gateway.text+'\n'
    nameserver=None
    if gateway is not None and interface.tag != 'extif' and interface.tag != 'intif':
      nameserver=gateway.text
    else:
      if 'backdoor' in metadata.groups:
        if interface.tag != 'extif' and interface.tag != 'intif':
          nameserver=str(routerifs['pubif'])
      else:
        nameserver='127.0.0.1'
    if nameserver is not None:
      r+='    dns-nameservers '+nameserver+'\n'
      r+='    dns-search '+domain_name+'\n'
    if 'virt-lxc' not in metadata.groups or 'nfs' in metadata.groups:
      # except for unprivileged lxc containers
      r+='    post-up /sbin/ifconfig '+dev+' txqueuelen 4'+'\n'
    for option in interface.findall('option'):
      r+= '    ' +option.text+'\n'
    for up in interface.findall('up'):
      r+= '    up ' +up.text+'\n'
  if bridgeddev is not None:
    r+= '    bridge_ports ' +bridgeddev+'\n'
    r+= '    bridge_stp off\n'
    r+= '    bridge_fd 0\n'
    r+= '    bridge_maxwait 0\n'
  vlandev=interface.find('vlandev')
  if vlandev is not None:
    vlandev=vlandev.text
    r+= '    vlan-raw-device ' +vlandev+'\n'
  if ('router' in metadata.groups) and (interface.tag == 'DMZif') and \
         ('DMZvpn' in funcifs) and ('vpnif' in networks):
    vpnet_str=str(networks['vpnif'].network())+'/'+str(networks['vpnif'].mask)
    r+= '    up ip route add '+vpnet_str+' via '+str(funcifs['DMZvpn'])+' || true \n'
    r+= '    down ip route delete '+vpnet_str+' via '+str(funcifs['DMZvpn'])+' || true \n'

  if interface.tag in static_routes:
    for static_route in static_routes[interface.tag]:
      route_net_str=str(static_route[0])
      route_via_str=str(static_route[1])
      r+= '    up ip route add '+route_net_str+' via '+route_via_str+' || true \n'
      r+= '    down ip route delete '+route_net_str+' via '+route_via_str+' || true \n'
  return r

def accumulate_tags(element, ip, name):
  global accumulate_tags
  global ipcalc
  val = element.find(name)
  if val is not None:
    val = val.text
  network=element.find('network_match')
  if network is None:
    network=element.find('network')
  elif network.text is None or not network.text.strip():
    network=None
  if network is not None:
    network=ipcalc.Network(network.text)
    if not ip in network:
      network = None
  if network is not None:
    if val is not None:
      return ([val], True)
    else:
      return ([],True)
  for sub in element.findall('ou'):
    subval=accumulate_tags(sub, ip, name)
    if subval[1]:
      if val is not None:
        return (subval[0]+[val], True)
      else:
        return subval
  return ([], False)

organization_host=False
hostname=metadata.hostname.split('.')[0]
# find the corresponding entry in umbrella.xml, also find router
# and prepare a dictionary of hosts by function
entry=None
router=None
for server in metadata.Properties['umbrella.xml'].xdata.findall('server'):
  srvname=server.find('name').text
  srvfunc=server.find('function').text
  if  srvname == hostname:
    if entry is None:
      entry=server
    else:
      raise TemplateError('Multiple entries in umbrella.xml for '+hostname+' .')
  if srvfunc == 'router':
      if router is None:
        router=server
      else:
        raise TemplateError('Multiple routers are declared in umbrella.xml .')
extif_enabled = False
intif_enabled = False
ext_address = None
int_address = None
iftypes = ['extif','intif', 'pubif', 'secif', 'DMZif', 'vpnif', 'winif']
routerifs={}
routerifdevs={}
if router is None:
  # in case there is no router we simulate its pub interface
  # by taking the gateway ip address from the root entry in
  # organization.xml
  gw=metadata.Properties['organization.xml'].xdata.find('ou').find('gateway')
  routerifs['pubif']=ipcalc.IP(gw.text.strip())
  routerifdevs['pubif']='eth0'
else:
  # fetch router interfaces from xml (used to set default routes on
  # internal subnets)
  routerifdevs={}
  routerifmacs={}
  for interface in iftypes:
    t = router.find(interface)
    if t is not None:
      routerifdevs[interface]=t.find('dev').text
      routerifmacs[interface]=t.find('mac').text
      t = t.find('ip')
      if (t is not None) and (t.text is not None and t.text.strip()):
        routerifs[interface]=ipcalc.IP(t.text.strip())
  # Check if the external interfaces are enabled at router
  # and determine the external addresses they are visible at.
  # Basically, the external address is <ip> unless <nat_ip> is specified,
  # then it is <nat_ip> (should be defined if the external interfaces
  # gets incoming packets NATed from the specified address).
  t = router.find('extif')
  if t is not None:
    ip = t.find('ip')
    if ip is not None:
      extif_enabled = True
      ext_address = ip.text.strip()
      if not ext_address:
        ext_address = None
    # can be overridden by nat address
    ip = t.find('nat_ip')
    if ip is not None:
      ext_address = ip.text.strip()
      if not ext_address:
        ext_address = None

  t = router.find('intif')
  if t is not None:
    ip = t.find('ip')
    if ip is not None:
      intif_enabled = True
      int_address = ip.text.strip()
      if not int_address:
        int_address = None
    # can be overridden by nat address
    ip = t.find('nat_ip')
    if ip is not None:
      int_address = ip.text.strip()
      if not int_address:
        int_address = None

# fetch main funchosts interfaces from xml
funcifs={}       # ip addresses of the main interfaces of function hosts
funcmacs={}      # MAC addresses of the main interfaces of function hosts
for server in metadata.Properties['umbrella.xml'].xdata.findall('server'):
  srvname=server.find('name').text
  srvfunc=server.find('function').text
  # determine the interface address from XML
  srvifs={}
  srvmacs={}
  for interface in iftypes:
    tif = server.find(interface)
    t = tif
    if t is not None:
      t = t.find('ip')
    if t is not None and t.text is not None and t.text.strip():
      srvifs[interface]=ipcalc.IP(t.text.strip())
    t = tif
    if t is not None:
      t = t.find('mac')
    if t is not None and t.text is not None and t.text.strip():
      srvmacs[interface]=t.text.strip()

  if ((srvfunc == 'router') or (srvfunc == 'vmhost') or (srvfunc == 'backdoor')):
    ifaddr=srvifs['pubif']
    ifmac=srvmacs['pubif']
  else:
    ifaddr=srvifs.values()[0]
    ifmac=srvmacs.values()[0]
  funcifs[srvfunc]=ifaddr
  funcmacs[srvfunc]=ifmac

# fetch host interfaces from xml
if entry is not None:
  # determine the interface addresses from XML
  ifs={}
  ifdevs={}
  for interface in iftypes:
    e = entry.find(interface)
    if e is not None:
      t = e.find('ip')
      if t is not None and t.text is not None and t.text.strip():
        ifs[interface]=ipcalc.IP(t.text.strip())
    if e is not None:
      t = e.find('dev')
      if t is not None and t.text is not None and t.text.strip():
        ifdevs[interface]=t.text.strip()
  ws_org_network=None
  ws_org_gateway=None
  ws_org_users_group=None
  ws_org_admin_group=None
else:
  # for workstation determine network parameters from
  # organization.xml
  ws_org_network=accumulate_tags(metadata.Properties['organization.xml'].xdata.find('ou'),
                                 ipcalc.IP(ip_addr), 'network')
  organization_host=ws_org_network[1]
  if ws_org_network[1]:
    ws_org_network=ws_org_network[0][0]

  ws_org_gateway=accumulate_tags(metadata.Properties['organization.xml'].xdata.find('ou'),
                                 ipcalc.IP(ip_addr), 'gateway')
  if ws_org_gateway[1]:
    ws_org_gateway=ws_org_gateway[0][0]

  ws_org_users_group=accumulate_tags(metadata.Properties['organization.xml'].xdata.find('ou'),
                                     ipcalc.IP(ip_addr), 'users_group')
  if ws_org_users_group[1]:
    ws_org_users_group=ws_org_users_group[0][0]
  else:
    ws_org_users_group=None

  ws_org_admin_group=accumulate_tags(metadata.Properties['organization.xml'].xdata.find('ou'),
                                     ipcalc.IP(ip_addr), 'admin_group')
  if ws_org_admin_group[1]:
    ws_org_admin_group=ws_org_admin_group[0]
  else:
    ws_org_admin_group=[]

# Here we compute the canonical hostname for this system and collect the
# secondary nameservers.
# The canonical hostname is the one that (hopefully) has proper reverse
# DNS resolution.
# By default the canonical hostname is the smtp hostname at primary domain.
# However, using the revDNS attribute in on domain names in umbrella.xml
# it is possible to set another hostname as canonical.
if 'DMZsmtp' in funchostsshort:
  CANONICAL_hostname=funchostsshort['DMZsmtp']
else:
  CANONICAL_hostname=""
CANONICAL_domain=domain_name
revDNSseen=False
secondaryNameservers={}
hostname_aliases={}

for domain in metadata.Properties['umbrella.xml'].xdata.findall('domain'):
  if domain.find('master') is None:         # only if we are master of the domain
    d_name=domain.find('name').text.strip()
    # collect hostname aliases for the domain
    aliases_set=set()
    for alias in domain.findall('alias'):
      aliases_set.add(alias.text.strip())
    # remove aliases, which can be in conflict to those, defined automatically
    if 'DMZwww' in funchosts:
      aliases_set.discard(funchostsshort['DMZwww'])
    if 'mail' in funchosts:
      aliases_set.discard(funchostsshort['mail'])
    hostname_aliases[d_name]=aliases_set

    # collect slave nameservers for the domain
    slave_nameservers=set()
    for slave in domain.findall('slave'):
      slave_nameservers.add(slave.text.strip())
    secondaryNameservers[d_name]=slave_nameservers

    # process revDNS and set the canonical hostname
    revDNS=domain.find('revDNS')
    if revDNS is not None:
      if revDNSseen:
        raise TemplateError('"revDNS" is specified for multiple domain names, '
                            'can only be specified for one.')
      revDNSseen=True
      if revDNS.text is not None:
        CANONICAL_hostname=revDNS.text.strip()
      else:
        CANONICAL_hostname=""
      CANONICAL_domain=d_name

# compute canonical fully qualified host name for this system
if CANONICAL_hostname:
  CANONICAL_FQHN = '%s.%s' % (CANONICAL_hostname, CANONICAL_domain)
else:
  CANONICAL_FQHN = CANONICAL_domain

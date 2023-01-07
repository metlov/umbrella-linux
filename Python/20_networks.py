# this file defines networks in the system
import ipaddress
import socket
from genshi.template import TemplateError

# compatibility code for ipaddress version < 3.7
# TODO: remove, once ubuntu-bionic support is removed
if hasattr(ipaddress.IPv4Network,"subnet_of"):
    is_subnet_of = lambda a, b: a.subnet_of(b)
else:
    is_subnet_of = lambda a, b: (b.network_address <= a.network_address and
                                 b.broadcast_address >= a.broadcast_address)

# all networks from umbrella.xml
networks = { \
'pubif':ipaddress.ip_network(metadata.Properties['umbrella.xml'].xdata.find('pubnet').text), \
'secif':ipaddress.ip_network(metadata.Properties['umbrella.xml'].xdata.find('secnet').text), \
'DMZif':ipaddress.ip_network(metadata.Properties['umbrella.xml'].xdata.find('DMZnet').text) }
tnet=metadata.Properties['umbrella.xml'].xdata.find('extnet')
if tnet is not None:
  networks['extif']=ipaddress.ip_network(tnet.text)
tnet=metadata.Properties['umbrella.xml'].xdata.find('intnet')
if tnet is not None:
  networks['intif']=ipaddress.ip_network(tnet.text)
tnet=metadata.Properties['umbrella.xml'].xdata.find('winnet')
if tnet is not None:
  networks['winif']=ipaddress.ip_network(tnet.text)
tnet=metadata.Properties['umbrella.xml'].xdata.find('vpnet')
if tnet is not None:
  networks['vpnif']=ipaddress.ip_network(tnet.text)

# get current host IP address
ip_addr=None
if metadata.addresses is not None:
  if len(metadata.addresses) == 0:
    try:
      ip_addr=ipaddress.ip_address(socket.gethostbyname(metadata.hostname))
    except socket.error:
      ip_addr=None
  elif len(metadata.addresses) == 1:
    ip_addr=ipaddress.ip_address(list(metadata.addresses)[0])
  else:
    for a in metadata.addresses:
      addr=ipaddress.ip_address(a)
      if addr in networks['pubif']:
        if ip_addr is None:
          ip_addr=addr
        else:
          raise TemplateError('Multiple addresses in public network for %s.'
                               % (metadata.hostname))

# internal networks
intnets = networks.copy()
intnets.pop('extif',None)
intnets.pop('intif',None)

# internal network interface names tuple
intnet_ifs = tuple(intnets.keys())

# check that internal networks do not intersect
for T1, N1 in intnets.items():
  for T2, N2 in intnets.items():
    if N2 is not N1 and N2 in N1:
      raise TemplateError('The \"'+T1[:-2]+'\" network '+str(N1)+' intersects '\
                          ' the \"'+T2[:-2]+'\" network '+str(N2)+\
                          ', which is not allowed .')

# retrieve dhcp ranges
pub_dhcp = metadata.Properties['umbrella.xml'].xdata.find('pub_dhcp')
if pub_dhcp is not None:
  pub_dhcp = [ ipaddress.ip_address(pub_dhcp.find('start').text), ipaddress.ip_address(pub_dhcp.find('end').text)]
  if int(pub_dhcp[0])>int(pub_dhcp[1]):
    raise TemplateError('The start DHCP address must be smaller or equal to the end DHCP address in <pub_dhcp> section of umbrella.xml.')

# retrieve DNS cache servers
DNScache=None
if metadata.Properties['umbrella.xml'].xdata.find('DNScache') is not None:
  DNScache=[cache.text for cache in metadata.Properties['umbrella.xml'].xdata.findall('DNScache')]

# Now we are going to remove the networks, which are fully contained
# in other networks of this set. For this we compare the networks in pairs
def cleanup_fully_contained(nets):
  global is_subnet_of
  import itertools
  nets_contained=set()
  for a, b in itertools.combinations(nets, 2):
    if is_subnet_of(a,b):
      nets_contained.add(a)
    if is_subnet_of(b,a):
      nets_contained.add(b)
  return nets.difference(nets_contained)


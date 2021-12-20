# this file defines networks in the system
import ipcalc
import socket

# get current host IP address
try:
  ip_addr=socket.gethostbyname(metadata.hostname)
except socket.error:
  ip_addr=None

# all networks from umbrella.xml
networks = { \
'pubif':ipcalc.Network(metadata.Properties['umbrella.xml'].xdata.find('pubnet').text), \
'secif':ipcalc.Network(metadata.Properties['umbrella.xml'].xdata.find('secnet').text), \
'DMZif':ipcalc.Network(metadata.Properties['umbrella.xml'].xdata.find('DMZnet').text) }
tnet=metadata.Properties['umbrella.xml'].xdata.find('extnet')
if tnet is not None:
  networks['extif']=ipcalc.Network(tnet.text)
tnet=metadata.Properties['umbrella.xml'].xdata.find('intnet')
if tnet is not None:
  networks['intif']=ipcalc.Network(tnet.text)
tnet=metadata.Properties['umbrella.xml'].xdata.find('winnet')
if tnet is not None:
  networks['winif']=ipcalc.Network(tnet.text)
tnet=metadata.Properties['umbrella.xml'].xdata.find('vpnet')
if tnet is not None:
  networks['vpnif']=ipcalc.Network(tnet.text)

# internal networks
intnets = networks.copy()
intnets.pop('extif', None)
intnets.pop('intif', None)

# internal networks as strings
intnets_str= {}
for ifname, ifnet in intnets.iteritems():
  intnets_str[ifname]=str(ifnet.network())+'/'+str(ifnet.mask)

# internal network interface names tuple
intnet_ifs = tuple(intnets.iterkeys())

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
  pub_dhcp = [ ipcalc.IP(pub_dhcp.find('start').text), ipcalc.IP(pub_dhcp.find('end').text)]
  if long(pub_dhcp[0])>long(pub_dhcp[1]):
    raise TemplateError('The start DHCP address must be smaller or equal to the end DHCP address in <pub_dhcp> section of umbrella.xml.')

# retrieve DNS cache servers
DNScache=None
if metadata.Properties['umbrella.xml'].xdata.find('DNScache') is not None:
  DNScache=[cache.text for cache in metadata.Properties['umbrella.xml'].xdata.findall('DNScache')]

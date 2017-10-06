# this file defines networks in the system
import ipcalc

# the whole network from umbrella.xml
wholenet = ipcalc.Network(metadata.Properties['umbrella.xml'].xdata.find('net').text)

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

# check that all the internal networks are part of the whole network
intnets_copy=intnets
intnets_copy.pop("vpnif", None)
for network in intnets_copy.itervalues():
  if not network in wholenet:
    raise TemplateError('The network '+str(network)+'/'+str(network.mask)+\
                        ' is not part of the whole network '+str(wholenet)+\
                        '/'+str(network.mask)+' .')

# retrieve dhcp ranges
pub_dhcp = metadata.Properties['umbrella.xml'].xdata.find('pub_dhcp')
if pub_dhcp is not None:
  pub_dhcp = [ ipcalc.IP(pub_dhcp.find('start').text), ipcalc.IP(pub_dhcp.find('end').text)]
  if long(pub_dhcp[0])>long(pub_dhcp[1]):
    raise TemplateError('The start DHCP address must be smaller or equal to the end DHCP address in <pub_dhcp> section of umbrella.xml.')

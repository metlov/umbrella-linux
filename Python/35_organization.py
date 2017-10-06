# this file defines variables and walker functions for data from
# organization.xml

import ipcalc

def fill_netclasses(netclasses, netclasses_obj, element, profile):
  global fill_netclasses
  global ipcalc
  val = element.find('bcfg2_profile')
  if val is not None:
    val = val.text
  network=element.find('network_match')
  if network is None:
    network=element.find('network')
  elif network.text is None or not network.text.strip():
    network=None
  if network is not None:
    network=ipcalc.Network(network.text)
  if network is not None:
    netclasses[str(network.network())+'/'+str(network.mask)] = val if val is not None else profile
    netclasses_obj[network] = val if val is not None else profile
  for sub in element.findall('ou'):
    fill_netclasses(netclasses, netclasses_obj, sub, val if val is not None else profile)
  return

netclasses={}
netclasses_obj={}
fill_netclasses(netclasses, netclasses_obj, metadata.Properties['organization.xml'].xdata.find('ou'), 'workstation')

# this file defines variables and walker functions for data from
# organization.xml

import ipaddress

def fill_netclasses(netclasses, element, profile):
  global fill_netclasses
  global ipaddress
  val = element.find('bcfg2_profile')
  if val is not None:
    val = val.text
  network=element.find('network_match')
  if network is None:
    network=element.find('network')
  elif network.text is None or not network.text.strip():
    network=None
  if network is not None:
    network=ipaddress.ip_network(network.text)
  if network is not None:
    netclasses[network] = val if val is not None else profile
  for sub in element.findall('ou'):
    fill_netclasses(netclasses, sub, val if val is not None else profile)
  return

netclasses={}
fill_netclasses(netclasses, metadata.Properties['organization.xml'].xdata.find('ou'), 'workstation')

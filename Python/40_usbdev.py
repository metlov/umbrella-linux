blacklistdevices=[]
hasblacklist=False
wildcardblasklist=False
for bl in metadata.Properties['usbdev.xml'].xdata.findall('blacklist'):
  host=bl.find('host')
  group=bl.find('group')
  if (host is None and group is None) or \
     (host is not None and metadata.hostname == host.text) or \
     (group is not None and group.text in metadata.groups):
    # wildcard blacklist
    # blacklist for this specific host
    # blacklist for one of this host groups
    hasblacklist=True
    blacklistdevices+=bl.iter('device')
    wildcardblasklist = wildcardblasklist or next(bl.iter('device'),None) is None
if wildcardblasklist:    # reset the list if at least one wildcard is found
  blacklistdevices=[]
whitelistdevices=[]
haswhitelist=False
wildcardwhitelist=False
for wl in metadata.Properties['usbdev.xml'].xdata.findall('whitelist'):
  host=wl.find('host')
  group=wl.find('group')
  if (host is None and group is None) or \
     (host is not None and metadata.hostname == host.text) or \
     (group is not None and group.text in metadata.groups):
    # wildcard whitelist
    # whitelist for this specific host
    # whitelist for one of this host groups
    haswhitelist=True
    whitelistdevices+=wl.iter('device')
    wildcardwhitelist = wildcardwhitelist or next(wl.iter('device'),None) is None
if wildcardwhitelist:    # reset the list if at least one wildcard is found
  whitelistdevices=[]

# this file defines some basic networking variables for the host
# and for the system as a whole

funchosts={}
funchostsshort={}
hostfuncs={}
for server in metadata.Properties['umbrella.xml'].xdata.findall('server'):
  srvnameshort=server.find('name').text
  srvname=srvnameshort+'.'+domain_name
  srvfunc=server.find('function').text
  funchostsshort[srvfunc]=srvnameshort
  funchosts[srvfunc]=srvname
  hostfuncs[srvname]=srvfunc

funchostdesc={ \
    'router' : 'The main router.', \
    'config' : 'Configuration server.', \
    'proxy'  : 'Proxy server.', \
    'mail'   : 'Mail server.', \
    'monitor': 'Monitoring server.', \
    'nfs'    : 'NFS server.', \
    'ltsp'   : 'Terminal server.', \
    'vmhost' : 'VM host.', \
    'archive': 'Archive server.', \
    'backdoor': 'Backdoor server.', \
    'DMZsmtp': 'DNS and SMTP server in DMZ.', \
    'DMZwww' : 'Web server.', \
    'DMZwww-test' : 'TEST Web server.', \
    'DMZxmpp': 'Jabber server in DMZ.', \
    'DMZpbx': 'PBX (telephony) server in DMZ.', \
    'DMZvpn': 'VPN server in DMZ.', \
    'DMZlib': 'Library (Invenio) server in DMZ.', \
  }

# determine the mail servers
smtp_outbound_relay=metadata.Properties['umbrella.xml'].xdata.find('SMTP_outbound_relay').text
if 'mail' in funchosts:
  smtp_server=funchosts['mail']
else:
  smtp_server=smtp_outbound_relay
imap_server=smtp_server

# determine the list of LDAP URIs
ldap_URIs=[ 'ldaps://'+funchosts['config'] ]
if 'proxy' in funchosts:
  ldap_URIs.append('ldaps://'+funchosts['proxy'])

# determine the NTP server
NTP_server_on_vmhost = metadata.Properties['umbrella.xml'].xdata.find('NTP_server_on_vmhost')
NTP_server_on_vmhost = 'vmhost' in funchosts and NTP_server_on_vmhost is not None
NTP_server=funchosts['config']
if NTP_server_on_vmhost:
  NTP_server=funchosts['vmhost']

# determine the Jabber server
if 'DMZxmpp' in funchosts:
  XMPP_server=funchosts['DMZxmpp']
else:
  XMPP_server=funchosts['proxy']

# here we define some parameters, extracted from firewall.xml
import ipcalc

# logic to determine who serves the SSH connections
ssh_from_ext = metadata.Properties['firewall.xml'].xdata.find('ssh_from_ext') is not None
ssh_from_int = metadata.Properties['firewall.xml'].xdata.find('ssh_from_int') is not None
forward_ssh_to_ltsp = metadata.Properties['firewall.xml'].xdata.find('forward_ssh_to_ltsp') is not None
ssh_to_ltsp=forward_ssh_to_ltsp and ('ltsp' in funchosts)
forward_smtps_imaps_to_mail = metadata.Properties['firewall.xml'].xdata.find('forward_smtps_imaps_to_mail') is not None
smtps_imaps_to_mail=forward_smtps_imaps_to_mail and ('mail' in funchosts)
# whether SSH connections are served by the router
ssh_to_router = (ssh_from_ext or ssh_from_int) and not ssh_to_ltsp
# whether SSH connections are served by the terminal server
ssh_to_ltsp = (ssh_from_ext or ssh_from_int) and ssh_to_ltsp

auth_in_DMZ = metadata.Properties['firewall.xml'].xdata.find('auth_in_DMZ')
www_homes = False
if auth_in_DMZ is not None:
  www_homes=auth_in_DMZ.find('www_homes') is not None
  auth_in_DMZ = True
else:
  auth_in_DMZ = False
mail_in_DMZ = metadata.Properties['firewall.xml'].xdata.find('mail_in_DMZ') is not None
admin_from_VPN = metadata.Properties['firewall.xml'].xdata.find('admin_from_VPN') is not None
ssh_to_sec = metadata.Properties['firewall.xml'].xdata.find('ssh_to_sec') is not None
pub_nfs_from_sec = metadata.Properties['firewall.xml'].xdata.find('pub_nfs_from_sec') is not None
jabber_from_sec = metadata.Properties['firewall.xml'].xdata.find('jabber_from_sec') is not None
mail_from_sec = metadata.Properties['firewall.xml'].xdata.find('mail_from_sec') is not None
vnc_in_gui_sessions = metadata.Properties['firewall.xml'].xdata.find('vnc_in_gui_sessions') is not None
transparent_proxy = metadata.Properties['firewall.xml'].xdata.find('transparent_proxy') is not None
slow_down_dynamic_hosts = metadata.Properties['firewall.xml'].xdata.find('slow_down_dynamic_hosts') is not None

# here we map the email addresses in the mail_in_DMZ tag to the robot servers,
# specified there.
robot_servers=[]
robot_addresses={}
if mail_in_DMZ:
  robots=metadata.Properties['firewall.xml'].xdata.find('mail_in_DMZ').findall('robot')

  # numbering of robots
  num=0
  robot_addrs={}
  address_numbering={}

  # function to add the address to the numbering
  def noteaddr(name,addr):
    global address_numbering, robot_addrs, num
    if addr in address_numbering:
      robot_addrs[name]=address_numbering[addr]
    else:
      address_numbering[addr]=num
      robot_addrs[name]=num
      num+=1

  for robot in robots:
    addr=ipcalc.IP(robot.find('ip').text.strip())
    mailing_list_name=robot.find('list_name')
    if mailing_list_name is not None:
      mailing_list_name=mailing_list_name.text
      ml_suffixes=['admin', 'bounces', 'confirm', 'join', 'leave',
                   'owner', 'request', 'subscribe', 'unsubscribe']
      noteaddr(mailing_list_name,addr)
      for suffix in ml_suffixes:
        noteaddr(mailing_list_name+'-'+suffix,addr)
    else:
      noteaddr(robot.find('name').text.strip(),addr)
  robot_addresses=robot_addrs       # move to global scope
  robot_servers = [None] * len(address_numbering)
  for addr, num in address_numbering.iteritems():
    robot_servers[num]=addr

# now the list robot_servers contains the numbered robot server addresses
# and the dictionary robot_addresses maps the robot names to these numbers

# here we parse DNS whitelists and blacklists for SMTP hosts
dns_smtp_whitelists = []
dns_smtp_blacklists = []
def cacheDNSListItem(item, DNSList):
  itemName=item.text.strip()
  recordMatch=item.get('recordMatch')
  DNSList.append((itemName,recordMatch))

dns_smtp_lists=metadata.Properties['firewall.xml'].xdata.find('DNS_SMTP_lists')
if dns_smtp_lists is not None:
  for dnsname in dns_smtp_lists.findall('whitelist'):
    cacheDNSListItem(dnsname, dns_smtp_whitelists)
  for dnsname in dns_smtp_lists.findall('blacklist'):
    cacheDNSListItem(dnsname, dns_smtp_blacklists)
del dnsname
del dns_smtp_lists

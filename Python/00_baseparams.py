# system name (and its localized string version)
system_name=metadata.Properties['umbrella.xml'].xdata.find('system_name').text
system_name_local=metadata.Properties['umbrella.xml'].xdata.find('system_name_local')
if system_name_local is not None:
  system_name_local=system_name_local.text
else:
  system_name_local=system_name

# long name of this system (and its localization)
long_name=metadata.Properties['umbrella.xml'].xdata.find('long_name').text
long_name_local=metadata.Properties['umbrella.xml'].xdata.find('long_name_local')
if long_name_local is not None:
  long_name_local=long_name_local.text
else:
  long_name_local=long_name

# department name (in case of department-local installation)
dept_name=metadata.Properties['umbrella.xml'].xdata.find('dept_name')
if dept_name is not None:
  dept_name=dept_name.text
dept_name_local=metadata.Properties['umbrella.xml'].xdata.find('dept_name_local')
if dept_name_local is not None:
  dept_name_local=dept_name_local.text
else:
  dept_name_local=dept_name

# location of this system (and its localized name)
location=metadata.Properties['umbrella.xml'].xdata.find('location').text
location_local=metadata.Properties['umbrella.xml'].xdata.find('location_local')
if location_local is not None:
  location_local=location_local.text
else:
  location_local=location

# state, where this system is installed (and its localized name)
state=metadata.Properties['umbrella.xml'].xdata.find('state').text
state_local=metadata.Properties['umbrella.xml'].xdata.find('state_local')
if state_local is not None:
  state_local=state_local.text
else:
  state_local=state

# country, where this system is installed (and its localized name)
country=metadata.Properties['umbrella.xml'].xdata.find('country').text
country_local=metadata.Properties['umbrella.xml'].xdata.find('country_local')
if country_local is not None:
  country_local=country_local.text
else:
  country_local=country

# country code
country_code=metadata.Properties['umbrella.xml'].xdata.find('country_code').text

# language
language=metadata.Properties['umbrella.xml'].xdata.find('language')
if language is None:
  language='ru_RU'
else:
  language=language.text

# timezone
timezone=metadata.Properties['umbrella.xml'].xdata.find('timezone')
if timezone is None:
  timezone='Europe/Moscow'
else:
  timezone=timezone.text

# the main domain name
domain_name=metadata.Properties['umbrella.xml'].xdata.find('domain').find('name').text
domain_name=domain_name.lower()
# set of all the domain names for this Umbrella instance
domain_names=[]
domain_notifies={}
domain_AXFR={}
for domain in metadata.Properties['umbrella.xml'].xdata.findall('domain'):
  if domain.find('master') is None:     # only if we are master of the domain
    d_name=domain.find('name').text
    domain_names.append(d_name)
    notifies=list()
    for notify in domain.findall('notify'):
      notifies.append(notify.text.strip())
    if len(notifies)>0:
      domain_notifies[d_name]=notifies
    AXFRs=list()
    for AXFR in domain.findall('AXFRallow'):
      AXFRs.append(AXFR.text.strip())
    if len(AXFRs)>0:
      domain_AXFR[d_name]=AXFRs
domain_names=sorted(domain_names)
realm_name=domain_name.upper()
ldap_root=','.join([ 'dc='+dc for dc in domain_name.split('.') ])
install_zabbix = metadata.Properties['umbrella.xml'].xdata.find('install_zabbix') is not None
upstream_proxy=metadata.Properties['umbrella.xml'].xdata.find('upstream_proxy')
if upstream_proxy is not None:
  upstream_proxy=upstream_proxy.text
admins=[admin.text for admin in metadata.Properties['umbrella.xml'].xdata.findall('admin')]

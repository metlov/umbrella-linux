from lxml import etree
structures=None

# This code is a dirty Frame hack, but so far it is the only
# way I (K.L.M.) know to extract the necessary information from
# within the bcfg2 server. The payout of having bcfg2 and ossec
# collaborating on the configuration file management is HUGE !
import sys
try:
  frame=1
  while structures is None:
    caller = sys._getframe(frame)
    if 'structures' in caller.f_locals:
      structures=caller.f_locals['structures']
    frame+=1
except:
  structures=None

# list of directories to be monitored by syscheck
dir_list=['/etc']

# initial list of files to ignore
path_list=['/etc/mtab','/etc/hosts.deny', '/etc/adjtime',
           '/var/ossec/etc/ossec.conf',
           '/var/ossec/etc/internal_options.conf']

if structures is not None:
  for struct in structures:
    paths=struct.iter("Path")
    for path in paths:
      pathname=path.get("name")
      for dir in dir_list:
        if pathname.startswith(dir):
          path_list.append(path.get("name"))
path_list.sort()
# now path_list contains the pathnames from within the OSSEC-monitored
# dir_list, which are managed by BCFG2 and should be ignored by the OSSEC

# this file computes the reverse zone structure for (possibly classless)
# Umbrella networks
import ipcalc

# first, let us determine a set of wrapping classfull networks
classfulnets=set()
cache={}
for T,N in networks.items():
  if T not in set(['extif','intif','vpnif']):
    if N.mask % 8 == 0:
      # network is by itself classful
      classfulnets.add(N)
    else:
      mask = 8*(N.mask / 8)
      netmask = long(0x100000000) - 2**(32-mask)
      classfulwrapper=ipcalc.Network(netmask & long(N.network()),mask)
      # this ugly cache is needed because ipcalc.Network can't be compared
      # properly and the same networks can be added to set twice
      if str(classfulwrapper) in cache:
        classfulwrapper=cache[str(classfulwrapper)]
      else:
        cache[str(classfulwrapper)]=classfulwrapper
      classfulnets.add(classfulwrapper)

# now we need to remove classful networks, which are subsets of each other
newclassfulnets=classfulnets.copy()
for N1 in classfulnets:
  for N2 in classfulnets:
    if N2 in N1 and N2.size() < N1.size():
      if N2 in newclassfulnets:
        newclassfulnets.remove(N2)
classfulnets=newclassfulnets

# now we compute the set of linking zones, which are classful, but not one
# of the Umbrella networks
linkingclassfulnets=list(classfulnets.difference(networks.values()))

# some utility functions

# get short name for a classless network N in the contest of the classfull
# network NC
def classlessShortName(N,NC):
  return '-'.join(str(N.host_first()).split('.')[NC.mask/8:])+'-'+\
         '-'.join(str(N.host_last()).split('.')[NC.mask/8:])

def findClassful(N, classfulnets):
  if N in classfulnets:
    return N
  else:
    # find a wrapping classful subnet
    for NN in classfulnets:
      if N in NN:
        return NN
  return None

# generates a zone name for network N, given a set of wrapping classful networks
def networkZoneName(N, NC, classlessShortName):
  seq=NC.to_tuple()[0].split('.')[NC.mask/8-1::-1] + ['in-addr', 'arpa']
  if NC is not N:
    seq.insert(0,classlessShortName(N,NC))
  return '.'.join(seq)

# reversed host IP in a specified network
def hostRevIP(IP, N):
  return '.'.join(str(IP).split('.')[:N.mask/8-1:-1])

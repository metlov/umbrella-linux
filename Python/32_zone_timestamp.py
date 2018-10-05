import os
import datetime

timestamp=int(os.path.getmtime(repo+'/Properties/umbrella.xml'))
timestamp_days=datetime.datetime.fromtimestamp(timestamp).strftime("%Y%m%d")
seq_fname=repo+'/Cfg/etc/bind/zones/zone/zone.H_dummy.sequence'
seq_number=0
if os.path.isfile(seq_fname):
  seq_timestamp=int(os.path.getmtime(seq_fname))
  try:
    with open(seq_fname) as f:
      seq_number = int(f.readline())
  except Exception:
    pass
else:
  seq_timestamp=timestamp-1
if timestamp>seq_timestamp:
  # the umbrella.xml file was modified since the last zone generation
  seq_timestamp_days=datetime.datetime.fromtimestamp(seq_timestamp).strftime("%Y%m%d")
  if timestamp_days==seq_timestamp_days:
    seq_number = seq_number + 1
  else:
    seq_number=0
  seq_number = seq_number % 100
  try:
    # write the current sequence number to a file
    with open(seq_fname, 'w') as f:
      f.write('%d' % seq_number)
    # remember the umbrella.xml modification time
    os.utime(seq_fname,(timestamp, timestamp))
  except Exception:
    pass

timestamp= (timestamp_days+'{0:02d}').format(seq_number)

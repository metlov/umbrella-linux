# this file defines functions for password file manupulation
# The password
import os
import time
import string
import random


def read_password(fname, lifetime_days, password_length=12):
    global os, time, random, string
    password=''
    # if file exists and newer than specified number of days 
    if os.path.isfile(fname) and (lifetime_days<0 or \
        (time.time() - os.path.getmtime(fname)) < (lifetime_days*24*60*60)):
        with open(fname) as f:
            password=f.readline()
    if not password:
        # generate a random password
        password=''.join(random.choice(string.ascii_uppercase + string.digits) for _ in range(password_length))
        # store it
        with open(fname, 'w') as f:
            f.write(password)
    return password

# this file defines functions for password file manupulation
# The password
import os
import time
import string
import random


def read_password(fname, lifetime_months):
    global os, time, random, string
    password=''
    # if file does not exist or older than 12 months
    if os.path.isfile(fname) and (lifetime_months<0 or \
        (time.time() - os.path.getmtime(fname)) < (lifetime_months*30*24*60*60)):
        with open(fname) as f:
            password=f.readline()
    if not password:
        # generate a random password
        password_length=12
        password=''.join(random.choice(string.ascii_uppercase + string.digits) for _ in range(password_length))
        # store it
        with open(fname, 'w') as f:
            f.write(password)
    return password

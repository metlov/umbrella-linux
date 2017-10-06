#!/bin/bash

# 20 Jul 2016: usage of this file is deprecated. Please source 
#              /etc/umbrella.conf instead, which defines everything
#              umbrella.sh defined except the ALL_SERVERS variable.

embed_newline()
{
   local p="$1"
   shift
   for i in "$@"
   do
      p="$p\n$i"         # Append
   done
   echo -e "$p"          # Use -e
}

. /etc/umbrella.conf

ALL_SERVERS=$( embed_newline $SERVERS )
ALL_SERVERS_WITH_KEYTAB=$( embed_newline $SERVERS_WITH_KEYTAB )
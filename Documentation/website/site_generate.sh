#!/bin/bash

autoindex() {
  # usage: autoindex [dir]
  INDEX=`ls -1 $1 | grep -v '^index.html$' | sed "s/^.*/      <li\>\<a\ href=\"&\"\>&\<\\/a\>\<\\/li\>/"`
  cat << EOF
<html>
  <head><title>Index of $1</title></head>
  <body>
    <h2>Index of $1</h2>
    <hr>
    <ui>
$INDEX
    <ui>
  </body>
</html>
EOF
}

autoindex output/umbrella-linux/installer >output/umbrella-linux/installer/index.html

if [ -z "$1" ]; then
  pelican content
else
  pelican content -s $1
fi

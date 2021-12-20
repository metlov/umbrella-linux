#!/usr/bin/env python
# -*- coding: utf-8 -*- #
from __future__ import unicode_literals

conffile="./pelicanconf.py"
with open(conffile, "rb") as source_file:
    code = compile(source_file.read(), conffile, "exec")
exec(code)

SITEURL = u'http://localhost:8001'

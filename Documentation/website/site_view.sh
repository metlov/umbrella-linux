#!/bin/bash
./site_generate.sh ./pelicanconf_local.py && (pushd output/; python -m pelican.server; popd)

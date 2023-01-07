#!/bin/bash
# Directory where the source packages will be assembled
SRCDIR=/tmp/unfs3-source

# Please update these two parameters when version bumping
SHA=c8f2d2cd4529955419bad0e163f88d47ff176b8d
SHADATE=20221006
SHAVERS=0.10.0

# These are github parameters to make script useful may by for other packages.
BASEURL=https://github.com/unfs3/    # includes github user, must end in slash
REPO=unfs3

# distribution code names
DISTS="jammy"

source ../prep_sources.sh

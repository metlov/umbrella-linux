#!/bin/bash
# Directory where the source packages will be assembled
SRCDIR=/tmp/unfs3-source

# Please update these two parameters when version bumping
SHA=cf96c70bf0b4dedd2cb5b0a72a4292e9ab8d0b2e
SHADATE=20190612
SHAVERS=0.9.23

# These are github parameters to make script useful may by for other packages.
BASEURL=https://github.com/unfs3/    # includes github user, must end in slash
REPO=unfs3

# distribution code names
DISTS="bionic xenial"

source ../prep_sources.sh

#!/bin/bash
# Directory where the source packages will be assembled
: ${SRCDIR?Tells where the source packages will be assembled. }
: ${SHA?The full commit hash.}
: ${SHADATE?The date of the commit as YYYYMMDD.}
: ${SHAVERS?The released package version.}
: ${BASEURL?The base Github URL with trailing slash.}
: ${REPO?The repository (and the package) name.}
: ${DISTS?The list of Ubuntu distros to assemble the sources for.}

echo "Preparing sources in ${SRCDIR}"
rm -rf ${SRCDIR}
mkdir ${SRCDIR}
echo "Downloading UNFS3 commit id ${SHA}."
wget -O ${SRCDIR}/${SHA}.zip ${BASEURL}${REPO}/archive/${SHA}.zip -q --show-progress
echo -n "Unpacking sources..."
unzip -q ${SRCDIR}/${SHA}.zip -d ${SRCDIR}/
echo " done."
echo -n "Creating the original sources archive..."
PKGVERS=${SHAVERS}+git${SHADATE}~${SHA:0:7}
PKGDIR=${REPO}-${PKGVERS}
PKGARC=${REPO}_${PKGVERS}
mv ${SRCDIR}/${REPO}-${SHA} ${SRCDIR}/${PKGDIR}
pushd ${SRCDIR} >/dev/null
tar cfz ${SRCDIR}/${PKGARC}.orig.tar.gz ${PKGDIR}/
popd >/dev/null
echo " done."
for dist in ${DISTS}; do
echo "Creating the $dist sources..."
rm -rf ${SRCDIR}/${PKGDIR}/debian
cp -r ./debian.${dist} ${SRCDIR}/${PKGDIR}/debian
pushd ${SRCDIR} >/dev/null
dpkg-source -b ${PKGDIR}/
popd >/dev/null
echo "done."
done
echo -n "Removing the unpacked sources and the downloaded source archive..."
rm -rf rm -rf ${SRCDIR}/${PKGDIR}
rm ${SRCDIR}/${SHA}.zip
echo " done."
echo "Everything is done, you'll find the .dsc files in ${SRCDIR}"

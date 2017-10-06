#-----------------------------------------------------------------
# Umbrella Linux skel management
#
#        (c) 2018 Konstantin L. Metlov <metlov@fti.dn.ua>
#
# This file is licensed to you on the terms of GNU General Public License
# version 3. The full text of the licence is in the LICENSE file inside
# the root directory of the Umbrella Linux configuration repository.

# update the user's skel to the latest version
upgrade_skel ()
{
    # doing the upgrades within a function to
    # prevent messing with the global scope
    source "/etc/umbrella.conf"

    # TODO: remove once migration to Umbrella Linux 0.2 is complete
    if [ -z "${XMPP_SERVER}" ]; then
       XMPP_SERVER=${HOST_PROXY}
    fi

    # skel exists, but may be we need to upgrade it
    # first we determine the version
    if [ ! -s "$HOME/.initialized" ]; then
        # the very first version, which did not have the version embedded
        if [ ! -s "$HOME/Рабочий стол/test" ]; then
            # we remove an empty file "test" on the desktop
            rm -f "$HOME/Рабочий стол/test"
        fi

        # we specify the connect server for Pidgin to have it
        # properly manage the certificates
        if ! grep -q "connect_server" "$HOME/.purple/accounts.xml"; then
            # no option connect_server, we need to specify it
            perl -pi -e "s|<setting name='port'(.*)</setting>|\
<setting name='port'\1</setting>\
\n\t\t\t<setting name='connect_server' type='string'>${XMPP_SERVER}</setting>\
|g" "$HOME/.purple/accounts.xml"
            # we also remove the stale certificate to have it refetched
            rm -f "$HOME/.purple/certificates/x509/tls_peers/$DOMAIN_NAME"
        fi
        # this way we have upgraded our skel to the version 1
        SKEL_VERSION=1
        echo "$SKEL_VERSION" > "$HOME/.initialized"
    else
        # here we read the skel version from file
        typeset -i SKEL_VERSION=$(cat "$HOME/.initialized")
    fi
    # here we shall perform the upgrades to the incremental skel versions one
    # by one
    # if [ $SKEL_VERSION == "1" ]; then
    #     # upgrade from version 1 to version 2
    #
    #     # if the upgrade is successful -- note that
    #     SKEL_VERSION=2
    #     echo "$SKEL_VERSION" > "$HOME/.initialized"
    # fi
    # if [ $SKEL_VERSION == "2" ]; then
    #     # upgrade from version 2 to version 3
    #
    #     # if the upgrade is successful -- note that
    #     SKEL_VERSION=3
    #     echo "$SKEL_VERSION" > "$HOME/.initialized"
    # fi
    # ... etc ...
}

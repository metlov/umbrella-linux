# ~/.profile: executed by the command interpreter for login shells.

# This profile is from Umbrella Linix skel. It initializes and upgrades
# skel in user's homedir.

# if running bash
if [ -n "$BASH_VERSION" ]; then
    # include .bashrc if it exists
    if [ -f "$HOME/.bashrc" ]; then
	. "$HOME/.bashrc"
    fi
fi

# set PATH so it includes user's private bin if it exists
if [ -d "$HOME/bin" ] ; then
    PATH="$HOME/bin:$PATH"
fi

# to enable Mozilla Thunderbird (and Firefox) certificate sharing with Chromium
export NSS_DEFAULT_DB_TYPE="sql"

# initialize the current version of skel if it was not initialized and
# upgrade the skel to the latest version if the upgrade is avaliable
if [ ! -f $HOME/.initialized ]; then
    init_skel ()
    {
        # doing the initialization within a function to
        # prevent messing with the global scope
        source "/etc/umbrella.conf"

        # TODO: remove once migration to Umbrella Linux 0.2 is complete
        if [ -z "${XMPP_SERVER}" ]; then
           XMPP_SERVER=${HOST_PROXY}
        fi

        # initialize Jabber account name and proxy server
        sed -i "s/_LOGIN_AT_DOMAIN_/${USER}@${DOMAIN_NAME}/g" "$HOME/.purple/accounts.xml"
        sed -i "s/_JABBER_SERVER_/${XMPP_SERVER}/g" "$HOME/.purple/accounts.xml"

        rm -f $HOME/.setup.sh
        rm -f $HOME/.skel-functions.sh
        # the last version of the skel is initialized
        echo "1" > $HOME/.initialized
    }
    init_skel
fi
# upgrade the skel (the separate "if" is necessary to make it possible to
# both initialize and upgrade the skel to the latest version in one go for
# the users, which did not log in before the update became available)
if [ -f $HOME/.initialized ]; then
    if [ -f /var/lib/umbrella-skel/unpacked/.skel-functions.sh ]; then
        # source the skel maintenance functions from the latest skel version
        source /var/lib/umbrella-skel/unpacked/.skel-functions.sh
        # upgrade the current user's skel
        upgrade_skel
    fi
fi

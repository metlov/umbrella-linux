#!/bin/bash
. /usr/local/bin/umbrella.sh        # source Umbrella Linux variables

# do nothing if home is not defined
[ -z "$HOME" ] && { \
   logger "autounison: HOME is not set"; \
   exit 0; }

# do nothing if user is not defined
[ -z "$USER" ] && { \
   logger "autounison: USER is not set."; \
   exit 0; }

# do nothing if ltsp host is not defined
[ -z "$HOST_LTSP" ] && { \
   logger "autounison: LTSP host is not defined, nothing to synvhronize against."; \
   exit 0; }

# do nothing if LTSP server is inaccessible or passwordless login is impossible
(ssh -o BatchMode=yes -o ConnectTimeout=5 -q "$USER"@"$HOST_LTSP" exit) || { \
   logger "autounison: can't do passwordless login to $HOST_LTSP as $USER."; \
   exit 0; }

# do nothing if local user group is not defined
[ -z "$LOCAL_USER_GROUP" ] && { \
   logger "autounison: local users group is not defined."; \
   exit 0; }

# do nothing if user is not in the local group for this workstation
(id -nG "$USER" | grep -qw "$LOCAL_USER_GROUP") || { \
   logger "autounison: user $USER is not a member of the local users group $LOCAL_USER_GROUP." ; \
   exit 0; }

export UNISON=$HOME/.unison
if [ ! -d "$UNISON" ]; then
  # no profile directory exists, we need to initialize it
  rm -rf "$UNISON"
  mkdir "$UNISON"
fi
AUTOPROFILE="$UNISON/autounison.prf"
if [ ! -f "$AUTOPROFILE" ]; then
  cat >"$AUTOPROFILE" <<EOF;
# Unison preferences file
root = $HOME
root = ssh://$USER@$HOST_LTSP/$HOME

# ignore some common paths, which are better to keep local
ignore = Path .unison
ignore = Path .bash_history
ignore = Path .lesshst
ignore = Path .rnd
ignore = Path .Xauthority
ignore = Path .ICEauthority
ignore = Path .xsession-errors
ignore = Path .xsession-errors.old
ignore = Path .fonts.cache-1
ignore = Path .xscreensaver
ignore = Path .cache
ignore = Path .local
ignore = Path .autounison.log
ignore = Path .autounison.log.old
ignore = Path .config/chromium/*/Session*
ignore = Path .config/chromium/*/GPUCache
ignore = Path .config/chromium/*/Local*
ignore = Path .config/chromium/*/Session*
ignore = Path .config/pulse
ignore = Path .config/ibus
ignore = Path .config/.initialized

# prefer configuration directories and hidden files from the root replica
preferpartial = Regex \..* -> ssh://$USER@$HOST_LTSP/$HOME

auto=true
group=false
owner=false
times=true
confirmbigdel=true
EOF
fi
if [ -n "$UNISON" ]; then
  MANUALSYNCFILE="$UNISON/manualsyncrequired"
  UNISON_EXIT_CODE=0
    if [ -z "$PAM_USER" ] && [ -n "$DISPLAY" ]; then
    # we are in X and not running from PAM, perform synchronization via GUI
    if [ -f "$MANUALSYNCFILE" ]; then
      # only if manual synchronization is required
      unison-gtk autounison
      UNISON_EXIT_CODE=$?
    fi
  else
  # X display is not available, do batch synchronization
    logger "autounison: syncronizing home folder for $USER..."
    # rotate log file
    LOG="$UNISON/autounison.log"
    if [ -f "$LOG" ]; then
      mv "$LOG" "$LOG.old"
    fi

    unison autounison -auto -batch &>"$LOG"
    UNISON_EXIT_CODE=$?
  fi
  if [ $UNISON_EXIT_CODE -eq 0 ]; then
    logger "autounison: syncronization for $USER completed."
    rm "$MANUALSYNCFILE"
  else
    logger "autounison: syncronization for $USER requires manual intervention."
    touch "$MANUALSYNCFILE"
  fi
fi

#!/bin/bash
[[ "$PAM_USER" == "root" ]] && exit 0

SESSION_COUNT="$(w -h "$PAM_USER" | wc -l)"

if (( SESSION_COUNT == 1 )) && [[ "$PAM_TYPE" == "close_session" ]]; then
  killall -u "$PAM_USER" chromium-browser
  sleep 5
  killall -u "$PAM_USER"
  sleep 2
  killall -9 -u "$PAM_USER"
fi

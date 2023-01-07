#!/bin/bash

# This script can be used to generate custom logo images,
# it can be called as
# ./generate-png.sh [ <logo-filename.svg> [ "Custom Logo Caption" ] ]
#
# dependencies:
#    librsvg2-bin imagemagick ttf-ubuntu-font-family

# exit on errors
set -e

SVG='logo.svg'
if [ "$#" -ge 1 ]; then
    SVG=$1
fi

# generate pre-rendered versions of the logo
# 4:3
rsvg-convert -a -w  160 -h  120 "$SVG" -o logo-160x120.png
rsvg-convert -a -w  320 -h  240 "$SVG" -o logo-320x240.png
rsvg-convert -a -w  480 -h  360 "$SVG" -o logo-480x360.png
rsvg-convert -a -w  640 -h  480 "$SVG" -o logo-640x480.png
rsvg-convert -a -w  960 -h  720 "$SVG" -o logo-960x720.png
rsvg-convert -a -w 1024 -h  768 "$SVG" -o logo-1024x768.png
rsvg-convert -a -w 1920 -h 1440 "$SVG" -o logo-1920x1440.png
# 16:9
rsvg-convert -a -w 1920 -h 1080 "$SVG" -o logo-1920x1080.png

# generate backgrounds
TEXT='Umbrella Linux'
if [ "$#" -ge 2 ]; then
    TEXT=$2
fi
# 4:3
convert logo-320x240.png -gravity Center -extent 640x480 -font 'Ubuntu-Bold' -pointsize 30 -annotate +0+155 "$TEXT" 640x480-cc.png
convert logo-480x360.png -gravity Center -extent 1024x768 -font 'Ubuntu-Bold' -pointsize 40 -annotate +0+210 "$TEXT" 1024x768-cc.png
convert logo-960x720.png -gravity Center -extent 2048x1536 -font 'Ubuntu-Bold' -pointsize 80 -annotate +0+450 "$TEXT" 2048x1536-cc.png
convert logo-320x240.png -resize 50% -gravity NorthWest -extent 640x480 -font 'Ubuntu-Bold' -pointsize 15 -annotate +47+125 "$TEXT" 640x480-nw.png
convert logo-480x360.png -gravity NorthWest -extent 1024x768 -font 'Ubuntu-Bold' -pointsize 40 -annotate +40+370 "$TEXT" 1024x768-nw.png
convert logo-960x720.png -gravity NorthWest -extent 2048x1536 -font 'Ubuntu-Bold' -pointsize 80 -annotate +80+730 "$TEXT" 2048x1536-nw.png
# 16:9
convert logo-480x360.png -gravity Center -extent 1440x900 -font 'Ubuntu-Bold' -pointsize 40 -annotate +0+210 "$TEXT" 1440x900-cc.png
convert logo-960x720.png -gravity Center -extent 2560x1440 -font 'Ubuntu-Bold' -pointsize 80 -annotate +0+450 "$TEXT" 2560x1440-cc.png
convert logo-480x360.png -gravity NorthWest -extent 1440x900 -font 'Ubuntu-Bold' -pointsize 40 -annotate +40+370 "$TEXT" 1440x900-nw.png
convert logo-960x720.png -gravity NorthWest -extent 2560x1440 -font 'Ubuntu-Bold' -pointsize 80 -annotate +80+730 "$TEXT" 2560x1440-nw.png

# make screenshot
convert 2560x1440-cc.png -resize x250 screenshot.png

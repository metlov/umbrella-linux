#!/bin/bash
# FFmpeg compilation for Ubuntu 18.04, 18.10 and Debian 9.
# Alvaro Bustos. Thanks to Hunter.
# 8-3-2019

sudo apt update
sudo apt -y --force-yes install autoconf automake build-essential libass-dev libfreetype6-dev libsdl1.2-dev libtheora-dev libtool libva-dev libvdpau-dev libvorbis-dev libxcb1-dev libxcb-shm0-dev libxcb-xfixes0-dev pkg-config texi2html zlib1g-dev mercurial cmake libx264-dev libfdk-aac-dev libmp3lame-dev libvpx-dev libmp3lame-dev

# Create a directory for sources.
SOURCES=$(mkdir ~/ffmpeg_sources)
cd ~/ffmpeg_sources

# Download the necessary sources.
if [ ! -e yasm-1.3.0.tar.gz ]; then
    wget http://www.tortall.net/projects/yasm/releases/yasm-1.3.0.tar.gz
fi
if [ ! -e fdk-aac-0.1.6.tar.gz ]; then
# wget -O fdk-aac.tar.gz https://github.com/mstorsjo/fdk-aac/tarball/master
wget https://netcologne.dl.sourceforge.net/project/opencore-amr/fdk-aac/fdk-aac-0.1.6.tar.gz
fi
if [ ! -e ffmpeg-4.1.tar.gz ]; then
wget http://ffmpeg.org/releases/ffmpeg-4.1.tar.gz
fi

# Unpack files
for file in `ls ~/ffmpeg_sources/*.tar.*`; do
tar -xvf $file
done

cd yasm-*/
./configure --prefix="$HOME/ffmpeg_build" --bindir="$HOME/bin" && make && sudo make install && make distclean; cd ..

cd fdk-aac-*/
autoreconf -fiv && ./configure --prefix="$HOME/ffmpeg_build" --disable-shared && make && sudo make install && make distclean; cd ..

cd ffmpeg-*/
PATH="$HOME/bin:$PATH" PKG_CONFIG_PATH="$HOME/ffmpeg_build/lib/pkgconfig" ./configure --prefix="$HOME/ffmpeg_build" --pkg-config-flags="--static" --extra-cflags="-I$HOME/ffmpeg_build/include" --extra-ldflags="-L$HOME/ffmpeg_build/lib" --bindir="$HOME/bin" --enable-gpl --enable-libass --enable-libfdk-aac --enable-libfreetype --enable-libmp3lame --enable-libopus --enable-libtheora --enable-libvorbis --enable-libvpx --enable-libx264 --enable-nonfree && PATH="$HOME/bin:$PATH" make && sudo make install && make distclean && hash -r; cd ..

cd ~/bin
cp ffmpeg ffprobe vsyasm yasm ytasm /usr/local/bin

echo "FFmpeg Compilation is Finished!"

# Setting Up an Encoding Environment

[Previous: An Introduction to FFmpeg](/encoding/ffmpeg/) | [Next: Things to do before encoding](/encoding/prereqs)

## Table of Contents

* [Introduction](#introduction)
* [FFmpeg Installation](#ffmpeg-installation)
* [Other Helpful Software](#other-helpful-software)
* [Conclusion](#conclusion)
* [Resources](#Resources)

---

## Introduction

We now have a basic idea of what FFmpeg is and what its `ffmpeg` tool can do. Next, we will set up an environment to make use of it so that we can begin encoding.

## FFmpeg Installation

FFmpeg is an open-source project, so it's source code is freely available for downloading [here](https://ffmpeg.org/download.html) if we would like to [compile it ourselves](https://trac.ffmpeg.org/wiki/CompilationGuide). Otherwise, FFmpeg offers static builds for our OS:

[Linux](https://ffmpeg.org/download.html#build-linux) | [Windows](https://ffmpeg.org/download.html#build-windows) | [macOS](https://ffmpeg.org/download.html#build-mac)

Alternatively, we can install FFmpeg through our package manager:

[Debian](https://tracker.debian.org/pkg/ffmpeg) | [Ubuntu](https://launchpad.net/ubuntu/+source/ffmpeg) | [Fedora / Red Hat Enterprise](https://rpmfusion.org/) | [Chocolatey](https://chocolatey.org/packages/ffmpeg) | [Homebrew](https://formulae.brew.sh/formula/ffmpeg)

If we are installing with a static build, we should extract the downloaded archive file to an easily discoverable directory like our C Drive. The extracted folder will be named after the build, so it is recommended that we rename the folder to `ffmpeg`. This makes the process of updating as easy as replacing the folder. Finally, we should add the `bin` subdirectory to our PATH environment variable so that we can can call the ffmpeg command-line tool without the canonical path to the executable.

Ultimately, we should be able to open a terminal and have the ability to call `ffmpeg` from any path in our system.

**Remark:** We have an encoding standard that requires the use of the latest release version of FFmpeg. FFmpeg targets approximately 6 months per major release with smaller patches in between. The moderation team notifies the community of new releases in the [Discord server](https://discordapp.com/invite/m9zbVyQ) and in submission posts if our builds are out of date. We may want to periodically check our static build site of choice for new stable builds.

## Other Helpful Software

Of course, there exists other software useful for the encoding and verification processes. While not a necessity, we may want to consider using some of these tools.

**[Spek](http://spek.cc/)** is an Acoustic Spectrum Analyser that helps to analysize audio files by showing their [spectrogram](https://en.wikipedia.org/wiki/Spectrogram). This tool is helpful in analyzing audio quality of source files and encoded WebMs.

**[MediaInfo](https://mediaarea.net/en/MediaInfo/Download)** is a convenient unified display of the most relevant technical and tag data for video and audio files. This tool is helpful in analyzing file properties in tandem with FFprobe to verify source files and encoded WebMs.

**[mpv](https://mpv.io/)** is a free, open source, and cross-platform media player. This tool is useful for testing playback, retrieving timestamps for encodes and taking high quality screenshots to share defect findings, among other things.

**[VLC](https://www.videolan.org/vlc/)** is a free and open source cross-platform multimedia player and framework that plays most multimedia files as well as DVDs, Audio CDs, VCDs, and various streaming protocols. This is a tool that is useful for testing playback.

**[MPC-HC](https://mpc-hc.org/)** is an extremely light-weight, open source media player for Windows. This tool has a great feature for retrieving accurate timestamps for encodes. However, this project is not under active development. If this is a concern for us, [MPC-BE](https://sourceforge.net/projects/mpcbe/) is an alternative.

**[Python](https://www.python.org/downloads/)** 3.6+ is required if we'd like to make use of the moderation team's scripting solutions for the encoding process.

**[MKVToolNix](https://mkvtoolnix.download/)** is a set of tools to create, alter and inspect Matroska files under Linux, other Unices and Windows.

## Conclusion

Our system is now ready for encoding efforts! Next, let's talk about what to do before starting an encoding process.

## Resources

[wikiHow - How to Install FFmpeg on Windows](https://www.wikihow.com/Install-FFmpeg-on-Windows)

[Adaptive Samples - How to Install FFmpeg on Windows](https://blog.gregzaal.com/how-to-install-ffmpeg-on-windows/)

[Install ffmpeg on Mac OS X](http://jollejolles.com/install-ffmpeg-on-mac-os-x/)

---

[Previous: An Introduction to FFmpeg](/encoding/ffmpeg/) | [Next: Things to do before encoding](/encoding/prereqs)

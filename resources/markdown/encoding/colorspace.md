# Colorspace

## Table of Contents

* [Standard](#standard)
* [Goal](#goal)
* [Implementation](#implementation)
* [Resources](#resources)

---

## Standard

Videos must identify colorspace.

## Goal

If not supplied by the video, playback decoding of color data may not be accurate depending on the player. Some players will guess based on resolution. Some players will guess based on the file type. Because this behavior is not consistent, we will provide this data ourselves based on the source.

## Implementation

We will simply add a set of arguments to specify the color data in both passes:

**HD**

    -colorspace "bt709" -color_primaries "bt709" -color_trc "bt709"

**NTSC SD**

    -colorspace "smpte170m" -color_primaries "smpte170m" -color_trc "smpte170m"

**PAL SD**

    -colorspace "bt470bg" -color_primaries "bt470bg" -color_trc "gamma28"

The source file may already be tagged with color data. It is advised to carrover this data to the encoded file.

## Resources

[Colorspace support in FFmpeg](https://trac.ffmpeg.org/wiki/colorspace)

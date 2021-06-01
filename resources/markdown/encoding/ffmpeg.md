# An Introduction to FFmpeg

[Previous: Encoding Effort Overview](/encoding) | [Next: Setting Up an Encoding Environment](/encoding/setup)

## Table of Contents

* [Introduction](#introduction)
* [Usage](#usage)
* [Examples](#examples)
* [Conclusion](#conclusion)
* [Resources](#Resources)

---

## Introduction

FFmpeg is a complete, cross-platform solution to record, convert and stream audio and video. This solution includes a number of libraries and a set of programs for handling these media-related tasks. The `ffmpeg` program itself is a command line tool designed to process video and audio files. It is used in programs like Handbrake and iTunes and on platforms like YouTube. This program is the primary tool that we will use for our encoding efforts.

**Remark**: FFmpeg also includes a command line tool called `ffprobe` which displays media information. We can use this tool to help us verify our videos after encoding.

## Usage

The basic idea of a command given to the `ffmpeg` command line tool is to read from an arbitrary number of input files and write to an arbitrary number of output files. The structure of a command is as follows:

    ffmpeg "[global options]" "[input options]" -i input "[output options]" output

**global options** are shared among all FFmpeg tools and do many things from adjusting the logging level to showing information like the current version to listing available library tools. The full list of options can be found [here](https://ffmpeg.org/ffmpeg.html#Generic-options). For our encoding efforts, use of global options is not a necessity but useful to know.

**input options** act on the input files. With input options we can block streams of an input file from being filtered or mapped to an output file. Behavior of seek arguments `-ss`, `-t` & `-to` are also affected by their placement as input or output options. The latter will be relevant to our encoding workflow.

**-i input** are the list of input files that we are performing some encoding process on for our output files. We will more often than not be acting on a single input source file to create our WebMs.

**output options** describe the encoding process acting on our input files to produce our output files. We will elaborate on these sets of options relevant to our encoding process later.

**output** are the list of output files of our encoding process. In our encoding process our output is a WebM file.

## Examples

Let's highlight a few of the many things we can do using `ffmpeg` to give an idea of what it does and how it works!

**Get information related to the file**

    ffmpeg -i input.mkv

Where the video file is named `input.mkv` in our current directory and the output is a listing of media information printed to the console.

**Hide Banner**

    ffmpeg -hide_banner -i input.mkv

Where the listing of media information printed to the console removes information related to the FFmpeg build using global option `-hide_banner`.

**Convert video to another format**

    ffmpeg -i input.mkv output.webm

Where the input file `input.mkv` is converted to the WebM format using default encoding settings.

**Extract the subtitle stream from a source file**

    ffmpeg -i input.mkv -map 0:s:0 subtitles.ass

Where we are mapping the first subtitle stream to the first subtitle stream of the output file `subtitles.ass` using [stream selection](https://ffmpeg.org/ffmpeg.html#Stream-selection) and implicitly ignoring other streams from the input by not mapping those as well.

**Create a gif using a palette file**

    ffmpeg -i input.mkv -vf "palettegen=stats_mode=diff" palette.png

#

    ffmpeg -i input.mkv -i palette.png -lavfi "paletteuse" -y output.gif

In the first command, we are creating `palette.png` using the [`palettegen`](https://ffmpeg.org/ffmpeg-filters.html#palettegen-1) filter. In the second command, we are using the output file from our first command as our second input file to help create the gif using the [`paletteuse`](https://ffmpeg.org/ffmpeg-filters.html#paletteuse) filter.

**Show the differences between two input files**

    ffmpeg -i input1.mkv -i input2.mkv -filter_complex "blend=all_mode=difference" differences.mkv

We are creating an output file that blends the video streams into each other using the [`blend`](https://ffmpeg.org/ffmpeg-filters.html#blend-1) filter to display the difference between the streams.

## Conclusion

Hopefully this gives us a basic idea of what FFmpeg is and what its `ffmpeg` tool can do. FFmpeg can be both useful and daunting given its scope and scale. Our community encourages ongoing learning of these tools. Questions are always welcomed as well.

Next, we will walk through how to set up FFmpeg and related tools on our machine to begin encoding for ourselves.

## Resources

[FFmpeg Official Site](https://ffmpeg.org/)

[FFmpeg Documentation](https://ffmpeg.org/ffmpeg.html)

[FFmpeg Bug Tracker and Wiki](https://trac.ffmpeg.org/)

[A FFmpeg Tutorial For Beginners](http://keycorner.org/pub/text/doc/ffmpeg-tutorial.htm)

[Beginners Guide to FFmpeg](https://www.codediesel.com/data/pdf/Beginners-Guide-to-FFmpeg.pdf)

[20+ FFmpeg Commands For Beginners](https://www.ostechnix.com/20-ffmpeg-commands-beginners/)

[19 FFmpeg Commands For All Needs](https://catswhocode.com/ffmpeg-commands/)

---

[Previous: Encoding Effort Overview](/encoding) | [Next: Setting Up an Encoding Environment](/encoding/setup)

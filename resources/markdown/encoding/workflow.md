# Encoding Workflow

[Previous: Things to do before encoding](/encoding/prereqs) | [Next: Verification](/encoding/verification)

## Table of Contents

* [Introduction](#introduction)
* [Step 1: First-pass Audio Normalization](#step-1-first-pass-audio-normalization)
* [Step 2: First-pass Video Encode](#step-2-first-pass-video-encode)
* [Step 3: Second Pass Encode](#step-3-second-pass-encode)
* [Recommended Starting Point Values](#recommended-starting-point-values)
* [Workflow Example](#workflow-example)
* [Conclusion](#conclusion)

---

## Introduction

Now that we have prepared for our encode, let's walk through the workflow for our encoding process. At a high-level, we are performing a combination of a 2-pass audio normalization method and 2-pass video encoding method, so we will walk through each step individually and provide general guidance on their structure and options.

## Step 1: First-pass Audio Normalization

### Goal

Because we source videos from a variety of groups who process audio differently, we need to enforce uniform levels of perceived loudness and volume control in our audio. This will produce a better listening experience across multiple videos in sequence.

We will be making use of the recommendations [outlined by the AES](http://www.aes.org/technical/documents/AESTD1004_1_15_10.pdf):

* Target Loudness should not exceed -16 LUFS
* Target Loudness should not be lower than -20 LUFS
* Maximum Peak should not exceed -1.0 dB TP

### Implementation

[`loudnorm`](https://ffmpeg.org/ffmpeg-filters.html#loudnorm), the EBU R128 loudness normalization audio filter, allows us to implement the recommendations defined above.

The 2-pass method of this filter measures the loudness stats of the source video on the first pass which we will use in our second-pass command. This is required of the linear normalization type, in which compression of our audio is more likely to be avoided as a by-product of normalization than the dynamic normalization type that the filter defaults to.

The structure of our first command looks like this:

    ffmpeg -ss hh:mm:ss.SSS -to hh:mm:ss.SSS -i "input.something" -af "loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json" -f null /dev/null

**-ss hh:mm:ss.SSS -to hh:mm:ss.SSS** represents our seeking options where `-ss` is our start position and `-to` is our end position. In the [previous guide](/encoding/prereqs#arguments) we detail our method for getting accurate positions for our encode, so by now we should have them handy.

When our seeking options are input options, `ffmpeg` will use input seeking, where the input is parsed using keyframe positions. The file streams are not parsed prior to our starting position.

When our seeking options are output options, `ffmpeg` will use output seeking, where the input is parsed and discarded until we reach our starting position.

For the purposes of our workflow, input seeking is a requirement for this step as loudnorm will read in the loudness stats prior to our start position if we use output seeking. More information about seeking can be found [here](https://trac.ffmpeg.org/wiki/Seeking).

**-i input.something** is our input file. This should be the source file that we verified in the previous guide.

**-af** is an alias for `-filter:a` and is the argument for which we are providing a list of filters to filter the input audio stream. The list of available audio filters that we can use can be found [here](https://ffmpeg.org/ffmpeg-filters.html#Audio-Filters).

**loudnorm** is our audio filter that we are filtering the input audio stream with. The remaining section of the filter string are our filter options. The filter syntax separates the filter from its options with a `=` character. Filter options are separated from option values with a `=` character. Filter options are delimited with a `:` character. The syntax is also documented [here](https://trac.ffmpeg.org/wiki/FilteringGuide#Filtersyntax).

**I=-16** is our loudnorm filter option that defines our target loudness of -16 LUFS.

**LRA=20** is our loudnorm filter option that defines a target loudness range of 20 LUFS. We set this defensively as the maximum allowed value to minimize the chance of loudnorm reverting to dynamic compression. This can happen if our target loudness range is lower than the source.

**TP=-1** is our loudnorm filter option that defines our maximum true peak of -1.0 dB. Ideally we want a true peak comfortably below this limit.

**dual_mono=true** is our loudnorm filter option that applies a compensating effect to input files of a mono layout intended for stereo playback. We set this defensively on the rare occasion that we encounter a file of this layout. Sources of other layouts are not affected by this setting.

**linear=true** is our loudnorm filter option that specifies our desire to normalize by linearly scaling the source audio. The measured argument values in the second pass normalization filter are required for this normalization type. Loudnorm will revert to dynamic normalization type if our target loudness range is lower than the source or if our target loudness produces a true peak that is above our limit.

**print_format=json** is our loudnorm filter option that will produce output in JSON format.

**-f** is the argument that tells `ffmpeg` to force the output format.

**null** is the null muxer. It does not generate any output file. We want the output to be printed to console instead. 

**/dev/null** is the null device (NUL on Windows). We provide this instead of an output file because we want output printed to the console instead.

This command will produce output of the following format:

    {
        "input_i" : "#.##",
        "input_tp" : "#.##",
        "input_lra" : "#.##",
        "input_thresh" : "#.##",
        "output_i" : "#.##",
        "output_tp" : "#.##",
        "output_lra" : "#.##",
        "output_thresh" : "#.##",
        "normalization_type" : "dynamic",
        "target_offset" : "#.##"
    }

We will then supply these input values to the audio filter on our second pass.

### Example

**Command**

    ffmpeg -ss 00:00.960 -to 01:30.089 -i "[Group] Show Title (BDRip 1920x1080 x264 FLAC).mkv" -af "loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json" -f null /dev/null

**Output**

    {
        "input_i" : "-17.25",
        "input_tp" : "-8.01",
        "input_lra" : "8.70",
        "input_thresh" : "-27.57",
        "output_i" : "-14.72",
        "output_tp" : "-1.00",
        "output_lra" : "4.70",
        "output_thresh" : "-24.85",
        "normalization_type" : "dynamic",
        "target_offset" : "-1.28"
     }

We will use these example values in our second-pass command to demonstrate how to make use of this output.

### Resources

[k.ylo.ph - loudnorm](http://k.ylo.ph/2016/04/04/loudnorm.html)

[FFmpeg - Audio Volume Manipulation](https://trac.ffmpeg.org/wiki/AudioVolume)

## Step 2: First-pass Video Encode

### Goal

Next, we will perform the first-pass of our 2-pass video encoding method. This step will produce a logfile of the video stream that will be used in the second-pass.

**Remark:** Step 1 and Step 2 can be performed in any order. What matters is that we have our loudness stats and logfile before proceeding to our second-pass.

### Implementation

The structure of our first command looks like this:

    ffmpeg -ss hh:mm:ss.SSS -to hh:mm:ss.SSS -i "input.something" -pass 1 -c:v libvpx-vp9 [bitrate control arguments] -cpu-used 4 -g 240 -threads 4 -tile-columns 6 -frame-parallel 0 -auto-alt-ref 1 -lag-in-frames 25 -row-mt 1 -pix_fmt yuv420p [colorspace arguments] -an -sn -f webm -y /dev/null

**-ss hh:mm:ss.SSS -to hh:mm:ss.SSS** represents our seeking options as described in Step 1.

**-i input.something** is our input file as described in Step 1.

**-pass 1** selects the pass number (1 or 2) for 2-pass video encoding. Here we specify 1 to tell our codec that we want to produce the pass 1 logfile.

**-c:v** is an alias for `-vcodec` and `-codec:v` and is our video codec. The WebM file format allows for the use of the VP8 and VP9 video formats. These argument values are `libvpx` and `libvpx-vp9`, respectively. Both use the libvpx codec.

**bitrate control arguments**

There are two primary modes of bitrate control that we can use. The first targets an average bitrate. The second targets a certain perceptual quality level. While both modes have their advantages and disadvantages, it is recommended that we use the latter as our default. However, it is also encouraged to iterate over different settings to produce better quality videos, so attempting both for our encode is the safest option.

Some comparisons of bitrate control modes:

[1](https://slow.pics/c/W7HaraFl)

Our arguments for the mode that targets average bitrate are:

    -b:v "####k" -maxrate "####k" -qcomp 0.3

**-b:v** is our target video bitrate. Expressed in bits/s.

**-maxrate** is our maximum overall bitrate allowed. Expressed in bits/s. 

**-qcomp** sets video quantizer scale compression. It is used as a constant in the ratecontrol equation. Range is 0.0 to 1.0, where lower values bias to constant bitrates and high values bias to perceptual quality. Here will will bias toward the former.

Our arguments for the mode that targets average perceptual quality are:

    -crf "##" -b:v 0 -qcomp 0.7

**-crf** is our Constant Rate Factor. It sets the quality/size tradeoff. Its range is 0-63, where lower values are better quality.

**-b:v** must be 0 in in this mode.

**-qcomp 0.7** biases to perceptual quality.

**-cpu-used 4** sets how efficient the compression will be. Its range is 0-5. The default is 0. Higher values increase encoding speed at the expense of quality and rate control accuracy. A higher value is safe for our first-pass.

**-g 240** specifies our maximum keyframe interval. This is a recommended value that targets a keyframe once every 240 frames.

**-threads** specifies the number of threads to use for encoding. Range is 1 - {number of cores}.

**-tile-columns** is a feature that enables tile-based/multi-threaded encoding/decoding if value is > 0 and `-threads` > 0.

**-frame-parallel** is an old multithreading implementation that can hurt quality and is enabled by default, so we disable it defensively.

**-auto-alt-ref** determines the use of alternate reference frames, a VP9 feature that enhances quality. `-lag-in-frames` must be >= 12 to trigger this feature.

**-lag-in-frames** is the number of frames to look ahead when encoding for the purpose of frame type and rate control. Range is 0-25.

**-row-mt** enables tile row multi-threading.

**-pix_fmt** sets the pixel format. By default, ffmpeg selects the same pixel format as the input. We set it explicitly to the common `yuv420p` format [so that there aren't issues with playback in browsers](https://i.imgur.com/loPjhGL.jpg).

**-an** disables audio streams from processing. We don't need to parse audio streams in this step since the logfile concerns the video stream.

**-sn** - Disable subtitles streams from processing. We don't need to parse subtitle streams in this step since the logfile concerns the video stream.

**-y** overwrites output files without asking.

### Example

**Targeted average bitrate**

    ffmpeg -ss 00:00.960 -to 01:30.089 -i "[Group] Show Title (BDRip 1920x1080 x264 FLAC).mkv" -pass 1 -c:v libvpx-vp9 -b:v 5600k -maxrate 6400k -qcomp 0.3 -g 240 -threads 4 -tile-columns 6 -frame-parallel 0 -auto-alt-ref 1 -lag-in-frames 25 -row-mt 1 -pix_fmt yuv420p -colorspace bt709 -color_primaries bt709 -color_trc bt709 -an -sn -f webm -y /dev/null

**Targeted average perceptual quality**

    ffmpeg -ss 00:00.960 -to 01:30.089 -i "[Group] Show Title (BDRip 1920x1080 x264 FLAC).mkv" -pass 1 -c:v libvpx-vp9 -crf 18 -b:v 0 -qcomp 0.7 -g 240 -threads 4 -tile-columns 6 -frame-parallel 0 -auto-alt-ref 1 -lag-in-frames 25 -row-mt 1 -pix_fmt yuv420p -colorspace bt709 -color_primaries bt709 -color_trc bt709 -an -sn -f webm -y /dev/null

In summary, this command:

* Tells `ffmpeg` to process the input within our start and end positions
* Tells our video codec that we are performing a first-pass to generate a logfile for use in our second-pass
* Tells our video codec that we want to use the VP9 video format
* Tells our video codec how to control our video bitrate
* Tells our video codec our desired multithreading & parallelization behaviors
* Tells our video codec our pixel format and colorspace data
* Tells `ffmpeg` to ignore audio and video streams from our input
* Tells `ffmpeg` to target the WebM format
* Tells `ffmpeg` to write output to console. The codec will generate the logfile.

### Resources

[Average Bitrate v Average Perceptual Quality](https://trac.ffmpeg.org/wiki/Encode/VP9)

## Step 3: Second Pass Encode

### Goal

Finally, we will perform the second-pass of our 2-pass encoding method. This step will produce an encoded WebM using the measured loudness stats from in Step 1 and the logfile from Step 2.

### Implementation

The structure of our command looks like this:

    ffmpeg -ss hh:mm:ss.SSS -to hh:mm:ss.SSS -i "input.something" -pass 2 -c:v libvpx-vp9 [bitrate control arguments] -cpu-used 0 -g 240 -threads 4 -af [second pass loudnorm filter] -tile-columns 6 -frame-parallel 0 -auto-alt-ref 1 -lag-in-frames 25 -row-mt 1 -pix_fmt yuv420p [colorspace arguments] -c:a libopus -b:a 192k -map_metadata -1 -map_chapters -1 -sn -f webm -y "output.webm"

**-ss hh:mm:ss.SSS -to hh:mm:ss.SSS** represents our seeking options as described in Steps 1 and 2.

**-i input.something** is our input file as described in Steps 1 and 2.

**-pass 2** is our pass number for 2-pass video encoding as described in Step 2. Here we specify 2 to tell our codec that we want to produce an encoded video using a pass 1 logfile.

**-c:v** is our video codec as described in Step 2.

**bitrate control arguments**

As in Step 2, we will review the arguments for average bitrate and average perceptual quality modes.

Our arguments for the mode that targets average bitrate are:

    -b:v "####k" -maxrate "####k" -bufsize 6000k -qcomp 0.3

**-b:v** is our target video bitrate as described in Step 2.

**-maxrate** is our maximum overall bitrate allowed as described in Step 2.

**-bufsize** is our rate control buffer. Expressed in bits/s.

**-qcomp** sets video quantizer scale compression as described in Step 2.

Our arguments for the mode that targets average perceptual quality are:

    -crf "##" -b:v 0 -qcomp 0.7

**-cpu-used 0** sets the compression efficiency as described in Step 2. Here we set the default value defensively so as not to compromise quality and rate control accuracy while encoding the streams.

**-g 240** specifies our maximum keyframe interval as described in Step 2.

**-threads** specifies the number of threads to use for encoding as described in Step 2.

**-tile-columns** is a feature that enables tile-based/multi-threaded encoding/decoding as described in Step 2.

**-frame-parallel** is an old multithreading implementation as described in Step 2.

**-auto-alt-ref** determines the use of alternate reference frames as described in Step 2.

**-lag-in-frames** is the number of frames to look ahead as described in Step 2.

**-row-mt** enables tile row multi-threading as described in Step 2.

**-pix_fmt** sets the pixel format as described in Step 2.

**colorspace arguments**

In the [previous guide](/encoding/prereqs#arguments) we detail our method for determining the colorspace characteristics from our source file, so by now we should have them handy.

**-c:a** - Audio codec. Alias for `-codec:a` and `-acodec`. The WebM file format allows for the use of the Vorbis and Opus audio formats. These argument values are `libvorbis` and `libopus`, respectively, and correspond to the codec name.

**-b:a** - Audio bitrate. Expressed in bits/s.

**-map_metadata -1** - Remove all metadata tags not directly generated by FFmpeg.

**-map_chapters -1** - Remove source file menu data

**-f** is the argument that tells `ffmpeg` to force the output format as described in Steps 1 and 2.

**-y** overwrites output files without asking as described in Steps 1 and 2.

**output.webm** is the output file of our encoding process.

### Example

**Targeted average bitrate**

    ffmpeg -ss 00:00.960 -to 01:30.089 -i "[Group] Show Title (BDRip 1920x1080 x264 FLAC).mkv" -pass 2 -c:v libvpx-vp9 -b:v 5600k -maxrate 6400k -bufsize 6000k -qcomp 0.3 -cpu-used 0 -g 240 -threads 4 -af loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json:measured_I=-17.25:measured_LRA=8.70:measured_TP=-8.01:measured_thresh=-27.57:offset=-1.28 -tile-columns 6 -frame-parallel 0 -auto-alt-ref 1 -lag-in-frames 25 -row-mt 1 -pix_fmt yuv420p -colorspace bt709 -color_primaries bt709 -color_trc bt709 -c:a libopus -b:a 320k -map_metadata -1 -map_chapters -1 -sn -f webm -y "output.webm"

**Targeted average perceptual quality**

    ffmpeg -ss 00:00.960 -to 01:30.089 -i "[Group] Show Title (BDRip 1920x1080 x264 FLAC).mkv" -pass 2 -c:v libvpx-vp9 -crf 18 -b:v 0 -qcomp 0.7 -cpu-used 0 -g 240 -threads 4 -af loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json:measured_I=-17.25:measured_LRA=8.70:measured_TP=-8.01:measured_thresh=-27.57:offset=-1.28 -tile-columns 6 -frame-parallel 0 -auto-alt-ref 1 -lag-in-frames 25 -row-mt 1 -pix_fmt yuv420p -colorspace bt709 -color_primaries bt709 -color_trc bt709 -c:a libopus -b:a 320k -map_metadata -1 -map_chapters -1 -sn -f webm -y "output.webm"

In summary, this command:

* Tells `ffmpeg` to process the input within our start and end positions
* Tells our video codec that we are performing a second-pass using a logfile from our first pass
* Tells our video codec that we want to use the VP9 video format
* Tells our video codec how to control our video bitrate
* Tells our video codec our desired multithreading & parallelization behaviors
* Tells our video codec our pixel format and colorspace data
* Tells our audio codec that we want to use the Opus audio format
* Tells our audio codec our target bitrate
* Tells `ffmpeg` to perform linear normalization on the audio stream with measured loudness stats
* Tells `ffmpeg` to prevent copying global metadata from our input file to our output file
* Tells `ffmpeg` to prevent copying menu metadata from our input file to our output file
* Tells `ffmpeg` to target the WebM format
* Tells `ffmpeg` to write to the output file `output.webm`

### Resources

* [Metadata](https://www.ffmpeg.org/ffmpeg-all.html#Metadata-1)

## Recommended Starting Point Values

A general guideline for the recommended values of arguments based on input.

**Note:** These values are offered as a reasonable starting point. Tinkering is encouraged to find the presets that work best for us.

**Video**

| Input | Video Bitrate  | CRF   |
| ----- | -----          | ----- |
| 480p  | 2400k to 3200k | 10-18 |
| 720p  | 3200k to 3700k | 10-24 |
| 1080p | 4200k to 5600k | 10-30 |

**Threads**

`[Number of processor cores] - 1`

**qcomp**

Average Bitrate: 0.3 - 0.4

Average Perceptual Quality: 0.6 - 0.7

### Resources

* [ffmpeg Documentation](http://ffmpeg.org/ffmpeg.html)
* [VP9 Encoding Guide](http://wiki.webmproject.org/ffmpeg/vp9-encoding-guide)
* [Seeking](https://trac.ffmpeg.org/wiki/Seeking)

## Workflow Example

### Assumptions

We have a source file at the location `E:\Anime\[Group] Show Title (BDRip 1920x1080 x264 FLAC).mkv`.

We have determined that the sequence starts at 00:00.960 and ends at 01:31.049.

We have opened a terminal to the `E:\Anime` directory.

### Workflow

**Command 1**

    ffmpeg -ss 00:00.960 -to 01:30.089 -i "[Group] Show Title (BDRip 1920x1080 x264 FLAC).mkv" -af "loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json" -f null /dev/null

**Output 1**

    {
        "input_i" : "-17.25",
        "input_tp" : "-8.01",
        "input_lra" : "8.70",
        "input_thresh" : "-27.57",
        "output_i" : "-14.72",
        "output_tp" : "-1.00",
        "output_lra" : "4.70",
        "output_thresh" : "-24.85",
        "normalization_type" : "dynamic",
        "target_offset" : "-1.28"
     }

**Command 2**

    ffmpeg -ss 00:00.960 -to 01:30.089 -i "[Group] Show Title (BDRip 1920x1080 x264 FLAC).mkv" -pass 1 -c:v libvpx-vp9 -crf 18 -b:v 0 -qcomp 0.7 -g 240 -threads 4 -tile-columns 6 -frame-parallel 0 -auto-alt-ref 1 -lag-in-frames 25 -row-mt 1 -pix_fmt yuv420p -colorspace bt709 -color_primaries bt709 -color_trc bt709 -an -sn -f webm -y /dev/null

**Output 2**

Produces `E:\Anime\ffmpeg2pass-0.log` logfile.

**Command 3**

    ffmpeg -ss 00:00.960 -to 01:30.089 -i "[Group] Show Title (BDRip 1920x1080 x264 FLAC).mkv" -pass 2 -c:v libvpx-vp9 -crf 18 -b:v 0 -qcomp 0.7 -cpu-used 0 -g 240 -threads 4 -af loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json:measured_I=-17.25:measured_LRA=8.70:measured_TP=-8.01:measured_thresh=-27.57:offset=-1.28 -tile-columns 6 -frame-parallel 0 -auto-alt-ref 1 -lag-in-frames 25 -row-mt 1 -pix_fmt yuv420p -colorspace bt709 -color_primaries bt709 -color_trc bt709 -c:a libopus -b:a 320k -map_metadata -1 -map_chapters -1 -sn -f webm -y "output.webm"

**Output 3**

Produces `E:\Anime\output.webm`.

## Conclusion

Now that we have encoded our WebMs, we will verify that they are acceptable for submission.

---

[Previous: Things to do before encoding](/encoding/prereqs) | [Next: Verification](/encoding/verification)

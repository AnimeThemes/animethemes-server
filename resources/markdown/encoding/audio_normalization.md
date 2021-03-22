# Audio Normalization

## Table of Contents

* [Standard](#standard)
* [Goal](#goal)
* [Implementation](#implementation)
* [Example](#example)
* [Verification](#verification)
* [Downmixing 5.1 to stereo](#downmixing-51-to-stereo)
* [Resources](#resources)

---

## Standard

Audio must be normalized as described by the [AES Streaming Loudness Recommendation](http://www.aes.org/technical/documents/AESTD1004_1_15_10.pdf).

## Goal

Because we source videos from a variety of groups who process audio differently, we need to enforce uniform levels of perceived loudness and volume control in our audio. This will produce a better listening experience across multiple videos in sequence.

We will be making use of the recommendations outlined by the AES:

* Target Loudness should not exceed -16 LUFS
* Target Loudness should not be lower than -20 LUFS
* Maximum Peak should not exceed -1.0 dB TP

## Implementation

[`loudnorm`](https://ffmpeg.org/ffmpeg-filters.html#loudnorm), the EBU R128 loudness normalization audio filter, allows us to implement the recommendations defined above.

First, we need to analyze and measure the loudness stats of the source video. Our command will be formatted as follows:

    ffmpeg -ss hh:mm:ss.SSS -i input.something -t hh:mm:ss.SSS -af "loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json" -f null /dev/null

`I=-16` defines our target loudness of -16 LUFS.

`LRA=20` defines a target loudness range of 20 LUFS. We set this defensively as the maximum allowed value to minimize the chance of loudnorm reverting to dynamic compression. This can happen if our target loudness range is lower than the source.

`TP=-1` defines our maximum true peak of -1.0 dB. Ideally we want a true peak comfortably below this limit.

`dual_mono=true` applies a compensating effect to input files of a mono layout intended for stereo playback. We set this defensively on the rare occasion that we encounter a file of this layout. Sources of other layouts are not affected by this setting.

`linear=true` specifies our desire to normalize by linearly scaling the source audio. The measured argument values in the second pass normalization filter are required for this normalization type. Loudnorm will revert to dynamic normalization type if our target loudness range is lower than the source or if our target loudness produces a true peak that is above our limit.

`print_format=json` will produce output of the following format:

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

We will then supply these input values to the audio filter on our second pass. Our argument will be formatted as follows:

    -af "loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json:measured_I=#.##:measured_LRA=#.##:measured_TP=#.##:measured_thresh=#.##:offset=#.##"

**Remark:** It is recommended to use fast seek for first-pass audio normalization commands. Loudness stat results are not consistent with slow seeking. It is possible that loudnorm is analyzing the loudness before the start position in this case.

## Example

    ffmpeg -ss 00:00.960 -to 01:31.049 -i "[Group] Show Title (BDRip 1920x1080 x264 FLAC).mkv" -af "loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json" -f null /dev/null

Output:

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

Second pass audio filter:

    -af "loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json:measured_I=-17.25:measured_LRA=8.70:measured_TP=-8.01:measured_thresh=-27.57:offset=-1.28"

## Verification

If audio is successfully normalized, the output of the second pass command should look like this:

    {
        "input_i" : "-14.71",
        "input_tp" : "-1.00",
        "input_lra" : "4.70",
        "input_thresh" : "-24.84",
        "output_i" : "-16.00",
        "output_tp" : "-6.24",
        "output_lra" : "8.80",
        "output_thresh" : "-26.32",
        "normalization_type" : "linear",
        "target_offset" : "-1.29"
    }

`output_i` (-16.00 here) should be at or near `-16.00`.

`output_tp` (-6.24 here) should be less than `-1.0`. Variance is acceptable for this value.

`output_lra` (8.80 here) should be roughly the same as the source `input_lra` value (8.70 here).

If the true peak of the output file is near or above the limit of `-1.0` or the loudness range has been reduced, it is possible that `loudnorm` reverted to dynamic compression which is not desirable.

## Downmixing 5.1 to stereo

FFmpeg provides the shorthand argument [`-ac 2`](https://trac.ffmpeg.org/wiki/AudioChannelManipulation#a5.1stereo) to downmix from surround sound to stereo in accordance with the [ATSC standards](http://www.atsc.org/wp-content/uploads/2015/03/A52-201212-17.pdf).

If the source file audio is a surround sound mix and we make use of the shorthand argument for downmixing to stereo, the downmix will happen AFTER normalization and will produce a video whose loudness falls outside of the acceptable range.

To prevent this, we need to remove the `-ac 2` shorthand from our commands and instead apply the [`aresample`](https://ffmpeg.org/ffmpeg-filters.html#aresample-1) filter to resample the input audio BEFORE normalizing.

Our first-pass argument will be formatted as follows:

    -af "aresample=ocl=stereo,loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json"

Our second-pass argument will be formatted as follows:

    -af "aresample=ocl=stereo,loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json:measured_I=-17.25:measured_LRA=8.70:measured_TP=-8.01:measured_thresh=-27.57:offset=-1.28"

**Example**

[Downmixed using `-ac 2`](https://files.catbox.moe/tzqpow.webm) | [Downmixed using `aresample`](https://files.catbox.moe/itbwdx.webm)

## Resources

[k.ylo.ph - loudnorm](http://k.ylo.ph/2016/04/04/loudnorm.html)

[FFmpeg - Audio Volume Manipulation](https://trac.ffmpeg.org/wiki/AudioVolume)

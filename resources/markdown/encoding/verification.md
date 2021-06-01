# Verification

[Previous: Encoding Workflow](/encoding/workflow)

## Table of Contents

* [Introduction](#introduction)
* [Step 1: Inspect file properties with MediaInfo](#step-1-inspect-file-properties-with-mediainfo)
* [Step 2: Execute compliance tests against WebM(s) with the WebM Verifier](#step-2-execute-compliance-tests-against-webms-with-the-webm-verifier)
* [Step 3: Inspect audio spectrogram with Spek](#step-3-inspect-audio-spectrogram-with-spek)
* [Step 4: Playback](#step-4-playback)
* [Step 5: Evaluation](#step-5-evaluation)
* [Conclusion](#conclusion)

---

## Introduction

Now that we have encoded our WebMs, we will verify that they are acceptable for submission.

## Step 1: Inspect file properties with [MediaInfo](https://mediaarea.net/en/MediaInfo/Download/Windows)

Once installed, right-clicking on the WebM file should include [MediaInfo](https://i.imgur.com/MkMAixZ.png) in the context-menu. [Example](https://i.imgur.com/NcWCA0e.png).

We can check some property files against encoding standard criteria:

**Files must use the latest release version of FFmpeg**

"Writing application" and "Writing library" in the General Section should be "Lavf58.45.100" or greater.

At the time of writing, **4.3.2** is the latest version of FFmpeg and the version string is **58.45.100**. The string is configured [here](https://github.com/FFmpeg/FFmpeg/blob/master/libavformat/version.h).

**Files must use the WebM container**

"Format" in the General Section should be "WebM".

**Files must adhere to our [size restrictions](/guidelines#maintain-a-balance-between-video-quality-and-file-size)**

The fields "File size", "Duration" and "Overall bit rate" in the General Section should follow these limits:

| Resolution (Video Height) | File size for 90 second video | Overall bit rate|
| ----- | ------ | ---------- |
| 480p  | 30 MiB | 3,400 kb/s |
| 720p  | 45 MiB | 4,000 kb/s |
| 1080p | 60 MiB | 6,000 kb/s |

These are written into our guidelines, which suggests flexibility. If the sequence is sufficiently complex, a 61 MiB 1080p 90-second video probably shouldn't be rejected outright. However, a 90 MiB version of the same video should be. Similarly, we should encourage compression of sufficiently simple sequences. It's likely that rolling credits over a black screen don't need to push near the file size limits.

**Files must erase source metadata using `-map_metadata -1`**

If additional properties not pictured in the example appear in any section, that property might be carryover from the source file. We will also verify this in the next step.

**Files must erase source menu data using `-map_chapters -1`**

Ensure that there does not exist a "Menu" section after the "Audio" section.

**Videos must use the VP9 video codec**

"Format" in the Video Section should be "VP9" and "Codec ID" in the Video Section should be "V_VP9".

**Videos must use the yuv420p pixel format**

MediaInfo does not include this property. We will verify this property in the next step.

**Videos must identify [colorspace](/encoding/colorspace)**

"Color primaries", "Transfer characteristics" and "Matrix coefficients" properties in the Video Section should be set to one of the following

| Color primaries | Transfer characteristics | Matrix coefficients |
| ----------- | ----------------- | ----------------- |
| BT.709      | BT.709            | BT.709            |
| BT.601 PAL  | BT.470 System B/G | BT.470 System B/G |
| BT.601 NTSC | BT.601            | BT.601            |

If these properties do not appear, the colorspace has not been set.

**Videos must be encoded at the same framerate as the source file. Motion interpolated videos (60FPS converted) are not allowed**

"Frame rate" in the Video Section should be 23.976 or 29.970 in the general case. We want to preserve the frame rate of the source, but uncommon frame rates are not preferable.

**Audio must use the Opus format**

"Format" in the Audio Section should be "Opus" and "Codec ID" in the Audio Section should be "A_OPUS".

**Audio must be [normalized](/encoding/audio_normalization) as described by the [AES Streaming Loudness Recommendation](http://www.aes.org/technical/documents/AESTD1004_1_15_10.pdf)**

This isn't a file property and won't be included in MediaInfo. We will verify audio normalization in the next step.

**Audio must use a default bitrate of 192 kbps**

MediaInfo does not include bit rate for each track. We will verify audio bitrate in the next step.

**Audio must use a bitrate of 320 kbps if the source is a DVD or BD release, and the source bitrate is > 320 kbps**

MediaInfo does not include bit rate for each track. We will verify audio bitrate in the next step.

**Audio must use a sampling rate of 48k**

"Sampling rate" in the Audio Section should be "48.0 kHz".

**Audio must use a two channel stereo mix.**

"Channel layout" in the Audio Section should be "2 channels".

## Step 2: Execute compliance tests against WebM(s) with the [WebM Verifier](/encoding/utilities#animethemes-webm-verifier)

In our terminal, execute `test_webm` or `test_webm.py`, depending on our python installation, to verify all WebM(s) in the current directory.

[Example](https://i.imgur.com/T2j3u1d.png)

Execute `test_webm filename.webm` or `test_webm.py filename.webm` to verify `filename.webm` in the current directory.

[Example](https://i.imgur.com/bnNRmvY.png)

Failures will be displayed as stack tracebacks with a descriptive message on the failing condition.

[Example](https://i.imgur.com/eQPLUei.png). This WebM had source file chapter data, did not specify colorspace and was encoded on an old FFmpeg build.

Execute `test_webm filename.webm --loglevel debug` or `test_webm.py filename.webm --loglevel debug` if we want to view file properties and calculated values used in tests.

[Example](https://i.imgur.com/Wd98JA0.png)

**Remarks**

Not every failure should result in an outright rejection as described in the MediaInfo File size guidelines above. For example:

* Libopus defaults to variable bitrate mode. For retro series in particular, this often results in an audio bitrate outside the range of this script's check. This likely isn't a cause for alarm. However, if the audio bitrate is less than 192 kbps, this is not desirable.
* Loudnorm results also introduce variance, sometimes outside the range of this script's check. Rerun with `--loglevel debug` to confirm that the mean loudness is near -16 LUFS and the peak loudness is below -1 LUFS.

## Step 3: Inspect audio spectrogram with [Spek](http://spek.cc/)

Right-click on the WebM and select Open with > Choose another app. In the "How do you want to open this file?" dialog, select Spek. Spek might be hidden under More apps.

Spectrograms can hint at the quality of the audio. The more defined and the greater the range of the spectrogram, the higher likelihood of better audio.

[Example - < 96 kbps Android](https://i.imgur.com/fbrZeiV.png)

[Example - Crunchyroll](https://i.imgur.com/QYNmwhr.png)

[Example - BS11](https://i.imgur.com/gLFfJmL.jpg)

[Example - Funi](https://i.imgur.com/zYWVLQb.jpg)

[Example - Blu Ray](https://i.imgur.com/FHTbd9h.jpg)

## Step 4: Playback

Remark: Set the volume louder than normal for casual listening. Maximize the window. Turn up the monitor brightness. Turn off f.lux.

Play back the video.

Listen for audio defects. Does the audio sound good? Does it sound over-compressed? Do we hear any clipping? Does it skip anywhere? Is the audio cut properly?

Look for video defects. Is the video rendering correctly? Is there evidence of interlacing or frameblending? Is there a watermark or watermark warping? Is the outlining sufficiently sharp? Do we see a lot of banding/aliasing/ringing? Is the video blurry or blocky?

In both our web browser and media player of choice, check the start and end of the video for stray frames. If the first or last frame is episode content or otherwise determined to be extraneous, we will need to encode again from our source.

In both our web browser and media player, check the start and end of the video for stray noise. In our web browser, refresh the tab multiple times to hear back the start of the video. In our media player, use the left arrow shortcut multiple times to hear back the start of the video. Sometimes stray noise is only picked up in playback in our web browser. Sometimes only in our media player.

## Step 5: Evaluation

The result of verification is left to the discretion of the encoder. Weigh findings against the encoding standards in making a determination. It is encouraged to iterate over different settings to produce the best quality video within our constraints. Doing so is also in the best interest of continued learning and producing better quality videos.

Don't shy away from following up with the moderation team, or our encoding community if anything remains uncertain.

## Conclusion

We have evaluated our encoded WebMs and will either proceed to submit them according to the [guidelines](/guidelines) or will make additional attempts to better the quality.

---

[Previous: Encoding Workflow](/encoding/workflow)

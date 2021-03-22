# Things to do before encoding

[Previous: Setting Up an Encoding Environment](/encoding/setup) | [Next: Encoding Workflow](/encoding/workflow)

## Table of Contents

* [Introduction](#introduction)
* [Sourcing](#sourcing)
* [Verifying the Source](#verifying-the-source)
* [Arguments](#arguments)
* [Environment](#environment)
* [Conclusion](#conclusion)

---

## Introduction

Now that our system is ready for encoding efforts, let's talk about what we should do prior to starting an encoding process.

## Sourcing

Before we start an encode, we need to find an adequate source to encode from. We are not allowed overt discussions of piracy, but we can offer some general guidance.

**Use a source as close to the original master as possible**

BDMVs & DVDISOs are as close to masters as we can get, but may still require remuxing, filtering, deinterlacing, etc.

Remuxes are BDMVs or DVDISOs that have been copied to another container such as Matroska.

BDRips & DVDRips are encoded from BDMVs & DVDISOs and may be filtered by the group. These sources are still desirable but not all groups are equal. Please review our trusted group list on our [Discord](https://discordapp.com/invite/m9zbVyQ).

Webrips from official streaming platforms are preferred for broadcast sequences. However, not all platforms are equal.

* Preferred: CR/VRV, Funi, AMZN, NF
* Avoid: Hidive, AniTV, Anime Zone, anything hardsubbed, anything taken from platforms like YouTube or bilibili

Re-encodes and tiny encodes are made to save disk space and are not desirable for us.

RAWs from terrestrial stations like Tokyo MX are not desirable as they will often have display motion blurring artifacts and [whatever is going on here](https://i.imgur.com/AOZPBee.jpg).

RAWs from premium or satellite channels like AT-X and BS-11 are acceptable if a better source does not exist.

**Avoid the use of sources that have been re-encoded multiple times**

As mentioned above, sources closer to the master as preferable as encoding is generally a destructive process.

**Check the [Trello](https://trello.com/b/ELroQzwV/animethemes-encoding) board for suggestions**

Tasks ready for encoding will include potential sources by name.

**Ask Questions**

If we are unsure the source is adequate, reach out to the moderation team and community.

## Verifying the Source

Once we have identified and obtained a source of acceptable origin, we should review some of the finer details of the file before to confirm that the source is okay for use.

**Inspect the source file properties**

Programs like [MediaInfo](https://mediaarea.net/en/MediaInfo/Download) & tools like [`ffprobe`](https://ffmpeg.org/ffprobe.html) can tell us about the file properties and formats.

Check the file format. Is the format outdated? Avoid Windows Media Video, AVI, MPEG-1, MPEG-2.

Check the overall bitrate. Is the bitrate far below our [file size restrictions](/guidelines#maintain-a-balance-between-video-quality-and-file-size)? This may be undesirable if the file is already compressed and degraded in quality. 

Check if the file has an Encoded date entry. Is the encode date relatively recent, or is the encode date more than 10 years ago?

Check the video stream. Does the video use outdated formats? Is the frame rate uncommon (not 24000/1001 or 30000/1001)? Is the video interlaced or telecined?

Check the audio stream. Does the video use outdated formats? Lossless streams are most preferable. Lossy formats like AAC are acceptable. Formats like vorbis are to be avoided.

Check the audio bitrate. If the bitrate is lower than our own standards, we may want to reconsider using the source.

Check the spectrogram of the audio stream in a program like [Spek](http://spek.cc/).

**Watch the video**

Play the video back. Are there issues with the audio or the video? It may be hard to produce an acceptable video if our source has [issues](https://i.imgur.com/x3KCTYu.jpg).

## Arguments

At this point we should determine certain arguments we may need for the encoding process of this particular source.

**Timestamps**

First, we should determine the start and end positions of our encode. [MPC-HC](https://mpc-hc.org/) or [MPC-BE](https://sourceforge.net/projects/mpcbe/) are recommended to get the most accurate positions. We can do this by opening the file in the player, using the ctrl + g shortcut, and modifying the timestamp value in the Go To Dialog that appears. Using this method, we can get the exact position of the start and end frames.

If we are working with an extra, we may want to refer to our [Common Start/End Positions](https://docs.google.com/spreadsheets/d/1FflijVd5GX3P8vi47GK-Ut7_S8fwoMeewo8HraiqATc/edit?usp=sharing) spreadsheet as we prefer the padding in these videos to be cut out.

**Audio filters**

Check for stray noise at the start position of our encode before the song starts. We may want to use a fade-in audio filter for our encode if we do.

Check the end position of our encode. Does the audio cut out abruptly or fade into environmental noise. We may want to use a fade-out audio filter for our encode if we do.

Please refer to the [Audio Filtering](/encoding/audio_filtering) guide for recommendations. Especially when producing an encode from an episode rather than an extra, applying this filter defensively will reduce the chance of introducing stray noise to the start or end of our encode.

**Colorspace**

Because we have an encoding standard that colorspace data must be tagged, we need to identify the characteristics of our source file. In some cases our source file will be tagged in which case we can generally copy them. We can verify if our source tags these characteristics with [MediaInfo](/encoding/verification#step_1.3A_inspect_file_properties_with_mediainfo).

If our source is not tagged, we can first fall back to contextual data. Is the source marked as R1/R2J DVD? Our source likely uses the NTSC standard. Is our source another DVD region? It is likely PAL. Is our source HD? We can likely use bt709.

Our final fallback is resolution. If our source is 480p, it is likely NTSC. If our source is 576p, it is likely PAL. If our source is 720p or 1080p, it is likely bt709.

If we are unsure and have no contextual data, we can iterate over each set of arguments in separate commands and verify which characteristics produce the best result. The common set of colorspace arguments can be found [here](/encoding/colorspace).

**Scaling**

Sometimes we may want to downscale the source video. For example, we may use a 1080p source for a new broadcast depending on the platform, but we want our broadcast encodes to be 720p in the general case. We may need to use the [scale](/encoding/video_filtering#scale) video filter in this case.

**De-interlacing**

If our source is interlaced, we will need to use the [yadif](/encoding/video_filtering#yadif) video filter to deinterlace.

**Decimating**

If our source is telecined, we will need to use the `fieldmatch` and `decimate` filters to get 24FPS progressive video. We can verify this by framestepping through our video. If we see a pattern of 3 normal frames followed by 2 interlaced frames, our video is likely telecined.

**Cropping**

If our source needs to be cropped, we will need to use the [crop](/encoding/video_filtering#crop) video filter.

**Subtitles**

If we are adding subtitles to our encode, we will need to use the [subtitles](/encoding/video_filtering#subtitles) filter.

## Environment

Once we have determined this set of arguments that will be needed for our encode, it is worth checking that our environment is updated before starting.

Check if our FFmpeg build is up to date.

If we are using scripts to automate our encoding, please check if they are up to date.

## Conclusion

We are now ready to start encoding. Next, we will go over the encoding process to produce WebMs that meet our standards.

---

[Previous: Setting Up an Encoding Environment](/encoding/setup) | [Next: Encoding Workflow](/encoding/workflow)

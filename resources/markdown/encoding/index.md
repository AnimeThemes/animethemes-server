# Encoding Effort Overview

## Table of Contents

* [Introduction](#introduction)
* [Standards](#standards)
  * [General](#general)
  * [Video](#video)
  * [Audio](#audio)
* [Guides](#guides)

---

## Introduction

One of the primary efforts of AnimeThemes is to grow our repository of anime opening and ending themes. For this effort, we need to produce WebMs from source video through encoding, where source video is compressed to our targeted format.

## Standards

Because our WebMs are produced from a variety of sources and by different volunteer encoders, we want to ensure that our videos are standardized. Enumerated below are formats, file properties and conditions that we want to enforce to achieve as much consistency as we can.

### General

**Files must use the [latest release version](https://www.gyan.dev/ffmpeg/builds/) of FFmpeg.**

FFmpeg is a project in active development with semi-regular releases. The same is true for the codecs that we use through FFmpeg to encode our video and audio streams. Because these releases apply bug fixes, new features & performance improvements that correlate to better quality and faster encoding times, we want encoders to work with the latest stable version of this software.

**Files must use the WebM format.**

To achieve consistency, we want videos in our repository to be of the same format. The WebM format targets use in HTML5 video and audio elements. Because we deliver our content this way, the WebM format is suitable for our needs.

**Files must adhere to our [size restrictions](/guidelines#maintain-a-balance-between-video-quality-and-file-size).**

We want to maximize quality within a reasonable file size range so that our videos remain easy to stream from our host.

**Files must erase source metadata using `-map_metadata -1`.**

By default, FFmpeg will copy global metadata from the source file. While some of these entries do not effect the end user experience, some, like movie name or title, may be parsed and displayed by a media player. Regardless of effect, we want our encodes to contain only the metadata that pertains to our own encode of the source, so we need to tell FFmpeg explicitly to disable copying of global metadata.

**Files must erase source menu data using `-map_chapters -1`.**

By default, FFmpeg will copy chapters from the source file if at least one chapter is specified. Because chapters do not apply to our encodes, we we need to tell FFmpeg explicitly to disable copying of chapters.

### Video

**Videos must use the VP9 video format.**

The WebM format supports VP8 and VP9 video formats. VP9 is the successor to VP8 designed to match quality at lower bitrates. Because we want to maximize the quality of our videos, we want our encoders to use this format.

**Videos must use the yuv420p pixel format.**

Pixel format is the format in which color data is encoded and organized into specific layouts of pixels. YUV420p is a common format that compresses well and is widely compatible. [Other pixel formats may not render correctly in playback](https://i.imgur.com/loPjhGL.jpg). By default, FFmpeg selects the same pixel format as the input, so we need to tell FFmpeg explicitly to convert from the source if needed.

**Videos must identify [colorspace](/encoding/colorspace).**

Colorspace describes how an array of pixel values should be displayed on the screen. It provides information like how pixel values are stored within a file, and what the range and meaning of those values are. If unspecified, media players may guess the colorspace of the file incorrectly and display the wrong colors. Because FFmpeg will leave colorspace unspecified if our source is unspecified, we need to tell FFmpeg explicitly to specify the colorspace of our video.

**Videos must be encoded at the same framerate as the source file. Motion interpolated videos (60FPS converted) are not allowed.**

We want to preserve the framerate of the source file. Unless we are experiencing issues with dropped frames or otherwise necessitated, we do not want to tamper with the framerate.

### Audio

**Audio must use the Opus format.**

The WebM format supports Vorbis and Opus audio formats. Opus compresses more efficiently and results in better quality than Vorbis, so we want to use this format.

**Audio must be [normalized](/encoding/audio_normalization) as described by the [AES Streaming Loudness Recommendation](http://www.aes.org/technical/documents/AESTD1004_1_15_10.pdf).**

Because we source videos from a variety of groups who process audio differently, we need to enforce uniform levels of perceived loudness and volume control in our audio. This will produce a better listening experience across multiple videos in sequence.

**Audio must use a default bitrate of 192 kbps.**

192 kbps is a standard medium quality bitrate that sits somewhere in the middle of the range used by streaming services and is well above degradation thresholds for our audio format.

**Audio must use a bitrate of 320 kbps if the source is a DVD or BD release and the source bitrate is > 320 kbps.**

320 kbps is a standard high quality bitrate that we will use for sources that make use of lossless formats like FLAC to preserve as much detail as we can.

**Audio must use a sampling rate of 48k.**

Our method of audio normalization will upsample our audio to 192 kHz if unspecified. To prevent this, we will defensively set our sampling rate to 48 kHz, a standard sampling rate for music.

**Audio must use a two channel stereo mix.**

Because we are targeting playback in HTML5 video and audio elements, we assume that our average user will playback through stereo headphones or speakers. Additionally, other audio layouts such as 5.1 may prevent successful playback on certain devices.

### Remarks

Video will be verified by the moderation team. Following the requirements does not guarantee that our submission will be accepted.

Consider using our [WebM Verifier](/encoding/utilities#animethemes-webm-verifier) script to test submissions against the requirements.

## Guides

These guides aim to provide detailed steps on how to produce WebMs that comply with our encoding standards listed above. The goal is for anyone new to encoding to be able to pick up the basics to then begin contributing to this project.

### [Step 1: An Introduction to FFmpeg](/encoding/ffmpeg/)

### [Step 2: Setting Up an Encoding Environment](/encoding/setup)

### [Step 3: Things to do before encoding](/encoding/prereqs)

### [Step 4: Encoding Workflow](/encoding/workflow)

[Audio Normalization](/encoding/audio_normalization)

[Audio Filtering](/encoding/audio_filtering)

[Video Filtering](/encoding/video_filtering)

### [Step 5: Verification](/encoding/verification)

### Other

[Troubleshooting](/encoding/troubleshooting)

[Utilities](/encoding/utilities)

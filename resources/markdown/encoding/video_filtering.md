# Video Filtering

## Table of Contents

* [crop](#crop)
* [decimate](#decimate)
* [gradfun](#gradfun)
* [hqdn3d](#hqdn3d)
* [scale](#scale)
* [subtitles](#subtitles)
* [unsharp](#unsharp)
* [yadif](#yadif)
* [Timeline Editing](#timeline-editing)

---

## crop

The [`crop`](https://ffmpeg.org/ffmpeg-filters.html#crop) filter crops the input video to given dimensions.

    -vf "crop=in_w-16:in_h"

Crop 8 pixels from the left and right borders

[Uncropped 1](https://files.catbox.moe/iwu1dd.webm) | [Cropped 1](https://files.catbox.moe/ivpnqf.webm) | [Uncropped 2](https://files.catbox.moe/3d35cm.webm) | [Cropped 2](https://files.catbox.moe/n1wb8p.webm)

## decimate

The [`decimate`](https://ffmpeg.org/ffmpeg-filters.html#decimate-1) filter drops duplicate frames at regular intervals. If we have a 29.97fps progressive source where every 5th frame is a duplicate, we may want to apply this filter to discard the duplicate frame. This will produce smoother playback, and these duplicate frames may suffer from [blending](https://slow.pics/c/Rs25vZfW).

[No video filters](https://files.catbox.moe/pu07wj.webm) | [decimate](https://files.catbox.moe/d0vpgf.webm)

## gradfun

The [`gradfun`](https://ffmpeg.org/ffmpeg-filters.html#gradfun) filter aims to fix banding artifacts by interpolating and dithering the gradients introducing the bands.

[No video filters]() | [gradfun]()

`gradfun` is designed for playback and may introduce Moiré patterns. Be selective if using this filter.

[No video filters](https://files.catbox.moe/acd3vf.webm) | [unsharp](https://files.catbox.moe/3dibw0.webm) | [gradfun introducing Moiré patterns](https://files.catbox.moe/tzarq7.webm)

## hqdn3d

The [`hqdn3d`](https://ffmpeg.org/ffmpeg-filters.html#hqdn3d-1) filter aims to reduce noise and produce smoother images.

Addressing minor temporal noise with strength setting `-vf hqdn3d=0:0:3:3`

[No video filters](https://files.catbox.moe/2jhidl.webm) | [Denoised](https://files.catbox.moe/w3n6lv.webm)

## scale

The [`scale`](https://ffmpeg.org/ffmpeg-filters.html#scale-1) filter resizes the input video.

    -vf "scale=-1:720"

Downscale video to 720, preserving aspect ratio

## subtitles

The [`subtitles`](https://ffmpeg.org/ffmpeg-filters.html#subtitles-1) filter draws subtitles on top of input video using the libass library.

    ffmpeg -i "input[input].something" -ss hh:mm:ss.SSS -to hh:mm:ss.SSS -pass 2 ... -vf subtitles="input\[input\].something" ...

* [Slow seek is needed to correctly sync the subtitle track to the video](https://trac.ffmpeg.org/ticket/2067)
* [Special characters need to be escaped when passed to libavfilter](https://trac.ffmpeg.org/ticket/3334)

## unsharp

TODO

## yadif

TODO

## Timeline Editing

TODO

# Audio Filtering

## Table of Contents

* [afade](#afade)
  * [Implementation](#implementation)
  * [Examples](#examples)
  * [Resources](#resources)

---

## afade

The [`afade`](https://ffmpeg.org/ffmpeg-filters.html#afade-1) audio filter allows us to apply fade-in/out effect to input audio. This can be useful in removing stray noise or in isolating the track if we do not have a clean place to start or end our cuts.

### Implementation

We will apply the `afade` filter in our second pass **after** normalization. Our argument is formatted as follows:

    -af "loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json:measured_I=#.##:measured_LRA=#.##:measured_TP=#.##:measured_thresh=#.##:offset=#.##,afade=..."

`d` specifies the duration of the effect

`curve` specifies the fade transition curve. See Resources for a visual reference guide.

`st` specifies the start time of the fade effect

**Remark**

The values for `d` and `st` are relative to the start position of the encode in fast seek mode.

For example, if our start position is 00:00.960 and we are applying a fade in of duration 0.500 starting at the position 00:00.960, our `d` is 0.500 and our `st` is 0, or unset.

The values for `st` are the positions in the source file in slow seek mode.

For example, if the start position is 00:00.960 and we are applying a fade in of duration 0.500 starting at the position 00:00.960, our `d` is 0.500 and our `st` is 0.960.

### Examples

Fade-in of .300s where we are attempting to isolate the track from stray noise present from the position of the first frame to the start of the track.

    -af "loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json:measured_I=-20.11:measured_LRA=9.20:measured_TP=-12.03:measured_thresh=-30.43:offset=-2.11,afade=d=0.300:curve=exp"

[Without afade filter](https://files.catbox.moe/mw12k4.webm) | [With afade filter](https://files.catbox.moe/p9tqmt.webm)

Fade-out of .500s where the track does not cleanly stop on the ending position we've chosen. The duration of the encode is exactly 90s.

    -af "loudnorm=I=-16:LRA=20:TP=-1:dual_mono=true:linear=true:print_format=json:measured_I=-20.11:measured_LRA=9.20:measured_TP=-12.03:measured_thresh=-30.43:offset=-2.11,afade=t=out:st=89.500:d=0.500"

[Without afade filter](https://files.catbox.moe/j981fj.webm) | [With afade filter](https://files.catbox.moe/py8s84.webm)

### Resources

[FFmpeg afade (audio fade filter) curves cheatsheet](https://trac.ffmpeg.org/wiki/AfadeCurves)

# Troubleshooting

## Table of Contents

* [Packed B-frames in AVI video](#packed-b-frames-in-avi-video)
  * [Description](#description)
  * [Solution](#solution)
  * [Resources](#resources)

---

## Packed B-frames in AVI video

### Description

"[Video uses a non-standard and wasteful way to store B-frames ('packed B-frames'). Consider using the mpeg4_unpack_bframes bitstream filter without encoding but stream copy to fix it.](https://i.imgur.com/5tKTQzS.png)"

This message indicates a source file that uses DivX-style packed B-frames. Packed bitstream isn't standard MPEG-4 and was used as a workaround for issues with the Video for Windows platform.

Unaddressed, there may be potential accuracy issues with fast seek and audio-visual syncing.

### Solution

Execute the following with the source file as the input. Use the output as the source for the encode.

    ffmpeg -i "input.avi" -codec copy -bsf:v "mpeg4_unpack_bframes" output.avi

### Resources

[mpeg4_unpack_bframes](https://ffmpeg.org/ffmpeg-bitstream-filters.html)

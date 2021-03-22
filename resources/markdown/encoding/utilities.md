# Utilities

## Table of Contents

* [alert-after](#alert-after)
* [animethemes-batch-encoder](#animethemes-batch-encoder)
* [animethemes-webm-verifier](#animethemes-webm-verifier)

---

## alert-after

Get a desktop notification after a command finishes executing.

In some cases, two-pass filtering may take some time. It could be useful to be notified when the encode is finished, especially if we are not actively monitoring the process.

### Installation

1. [Install Rust](https://rustup.rs/)

2. `cargo install alert-after`

### Usage

    aa "[command]"

#

    aa ffmpeg -ss 00:00.960 -i "[Group] Show Title (BDRip 1920x1080 x264 FLAC).mkv" -to 01:30.089 -pass 2 ...

### Resources

[Github](https://github.com/frewsxcv/alert-after)

## [animethemes-batch-encoder](https://pypi.org/project/animethemes-batch-encoder/)

### Description

Generate and execute collection of FFmpeg commands sequentially from external file to produce WebMs that meet [encoding standards](/encoding#standards).

Take advantage of sleep, work, or any other time that we cannot actively monitor the encoding process to produce a set of encodes for later quality checking and/or tweaking for additional encodes.

Ideally we are iterating over a combination of filters and settings, picking the best one at the end.

### Install

**Requirements:**

* FFmpeg
* Python >= 3.6

**Install:**

    pip install animethemes-batch-encoder

### Usage

        python -m batch_encoder [-h] --mode [{1,2,3}] [--file [FILE]] [--configfile [CONFIGFILE]] --loglevel [{debug,info,error}]

**Mode**

`--mode 1` generates commands from input files in the current directory.

The user will be prompted for values that are not determined programmatically, such as inclusion/exclusion of a source file candidate, start time, end time and output file name.

`--mode 2` executes commands from file in the current directory line-by-line.

By default, the program looks for a file named `commands.txt` in the current directory. This file name can be specified by the `--file` argument.

`--mode 3` generates commands from input files in the current directory and executes the commands sequentially without writing to file.

The `--file` argument will be ignored in this case.

**File**

The file that commands are written to or read from.

By default, the program will write to or read from `commands.txt` in the current directory.

**Config File**

The configuration file in which our encoding properties are defined.

By default, the program will write to or read from `batch_encoder.ini` in the user config directory of appname `batch_encoder` and author `AnimeThemes`.

Example: `C:\Users\paranarimasu\AppData\Local\AnimeThemes\batch_encoder\batch_encoder.ini`

**Encoding Properties**

`AllowedFileTypes` is a comma-separated listing of file extensions that will be considered for source file candidates.

`EncodingModes` is a comma-separated listing of [bitrate control modes](https://developers.google.com/media/vp9/bitrate-modes) for inclusion and ordering of commands.

Available bitrate control modes are:

* `CBR` Constant Bitrate Mode
* `VBR` Variable Bitrate Mode
* `CQ` Constrained Quality Mode

`CRFs` is a comma-separated listing of ordered CRF values to use with `VBR` and/or `CQ` bitrate control modes.

`IncludeUnfiltered` is a flag for including or excluding an encode without video filters for each bitrate control mode and CRF pairing.

`VideoFilters` is a configuration item list used for named video filtergraphs for each bitrate control mode and CRF pairing.

**Logging**

Determines the level of the logging for the program.

`--loglevel error` will only output error messages.

`--loglevel info` will output error messages and script progression info messages.

`--loglevel debug` will output all messages, including variable dumps.

### Resources

[Github](https://github.com/AnimeThemes/animethemes-batch-encoder)

## [animethemes-webm-verifier](https://pypi.org/project/animethemes-webm-verifier/)

### Description

Verify WebM(s) against [encoding standards](/encoding#standards).

Executes a test suite on the input WebM(s) to verify compliance.

Test success/failure does **NOT** guarantee acceptance/rejection of submissions. In some tests, we are determining the correctness of our file properties. In other tests, we are flagging uncommon property values for inspection.

### Install

**Requirements:**

* FFmpeg
* Python >= 3.6

**Install:**

    pip install animethemes-webm-verifier

### Usage

    python -m test_webm [-h] [--loglevel [{debug,info,error}] [file [file ...]]

* `--loglevel error`: Only show error messages
* `--loglevel info`: Show error messages and script progression info messages
* `--loglevel debug`: Show all messages, including variable dumps
* `[file ...]`: The WebM(s) to verify. If not provided, we will test all WebMs in the current directory.

### Resources

[Github](https://github.com/AnimeThemes/animethemes-webm-verifier)

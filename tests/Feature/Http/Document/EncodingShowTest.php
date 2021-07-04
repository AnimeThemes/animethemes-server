<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

/**
 * Class EncodingShowTest.
 */
class EncodingShowTest extends TestCase
{
    /**
     * Audio Filtering shall be displayed as a document.
     *
     * @return void
     */
    public function testAudioFiltering()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'audio_filtering']));

        $response->assertViewIs('document');
    }

    /**
     * Audio Normalization shall be displayed as a document.
     *
     * @return void
     */
    public function testAudioNormalization()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'audio_normalization']));

        $response->assertViewIs('document');
    }

    /**
     * Colorspace shall be displayed as a document.
     *
     * @return void
     */
    public function testColorspace()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'colorspace']));

        $response->assertViewIs('document');
    }

    /**
     * Common Positions shall be displayed as a document.
     *
     * @return void
     */
    public function testCommonPositions()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'common_positions']));

        $response->assertViewIs('document');
    }

    /**
     * FFmpeg shall be displayed as a document.
     *
     * @return void
     */
    public function testFFmpeg()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'ffmpeg']));

        $response->assertViewIs('document');
    }

    /**
     * Prereqs shall be displayed as a document.
     *
     * @return void
     */
    public function testPrereqs()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'prereqs']));

        $response->assertViewIs('document');
    }

    /**
     * Setup shall be displayed as a document.
     *
     * @return void
     */
    public function testSetup()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'setup']));

        $response->assertViewIs('document');
    }

    /**
     * Troubleshooting shall be displayed as a document.
     *
     * @return void
     */
    public function testTroubleshooting()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'troubleshooting']));

        $response->assertViewIs('document');
    }

    /**
     * Utilities shall be displayed as a document.
     *
     * @return void
     */
    public function testUtilities()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'utilities']));

        $response->assertViewIs('document');
    }

    /**
     * Verification shall be displayed as a document.
     *
     * @return void
     */
    public function testVerification()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'verification']));

        $response->assertViewIs('document');
    }

    /**
     * Video Filtering shall be displayed as a document.
     *
     * @return void
     */
    public function testVideoFiltering()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'video_filtering']));

        $response->assertViewIs('document');
    }

    /**
     * Workflow shall be displayed as a document.
     *
     * @return void
     */
    public function testWorkflow()
    {
        $response = $this->get(route('encoding.show', ['docName' => 'workflow']));

        $response->assertViewIs('document');
    }
}

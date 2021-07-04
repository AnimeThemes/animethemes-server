<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Sitemap;

use Tests\TestCase;

/**
 * Class EncodingSitemapTest.
 */
class EncodingSitemapTest extends TestCase
{
    /**
     * The encoding sitemap shall display the encoding sitemap view.
     *
     * @return void
     */
    public function testSitemapIndex()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertViewIs('sitemap.encoding');
    }

    /**
     * The encoding sitemap shall display the encoding index route.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.index'));
    }

    /**
     * The encoding sitemap shall display the encoding audio_filtering route.
     *
     * @return void
     */
    public function testAudioFiltering()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'audio_filtering']));
    }

    /**
     * The encoding sitemap shall display the encoding audio_normalization route.
     *
     * @return void
     */
    public function testAudioNormalization()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'audio_normalization']));
    }

    /**
     * The encoding sitemap shall display the encoding colorspace route.
     *
     * @return void
     */
    public function testColorspace()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'colorspace']));
    }

    /**
     * The encoding sitemap shall display the encoding common_positions route.
     *
     * @return void
     */
    public function testCommonPositions()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'common_positions']));
    }

    /**
     * The encoding sitemap shall display the encoding ffmpeg route.
     *
     * @return void
     */
    public function testFFmpeg()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'ffmpeg']));
    }

    /**
     * The encoding sitemap shall display the encoding prereqs route.
     *
     * @return void
     */
    public function testPrereqs()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'prereqs']));
    }

    /**
     * The encoding sitemap shall display the encoding setup route.
     *
     * @return void
     */
    public function testSetup()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'setup']));
    }

    /**
     * The encoding sitemap shall display the encoding troubleshooting route.
     *
     * @return void
     */
    public function testTroubleshooting()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'troubleshooting']));
    }

    /**
     * The encoding sitemap shall display the encoding utilities route.
     *
     * @return void
     */
    public function testUtilities()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'utilities']));
    }

    /**
     * The encoding sitemap shall display the encoding verification route.
     *
     * @return void
     */
    public function testVerification()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'verification']));
    }

    /**
     * The encoding sitemap shall display the encoding videoFiltering route.
     *
     * @return void
     */
    public function testVideoFiltering()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'video_filtering']));
    }

    /**
     * The encoding sitemap shall display the encoding workflow route.
     *
     * @return void
     */
    public function testWorkflow()
    {
        $response = $this->get(route('sitemap.encoding'));

        $response->assertSee(route('encoding.show', ['docName' => 'workflow']));
    }
}

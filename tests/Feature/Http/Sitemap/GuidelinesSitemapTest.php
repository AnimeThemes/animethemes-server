<?php

namespace Tests\Feature\Http\Sitemap;

use Tests\TestCase;

class GuidelinesSitemapTest extends TestCase
{
    /**
     * The guidelines sitemap shall display the guidelines sitemap view.
     *
     * @return void
     */
    public function testSitemapIndex()
    {
        $response = $this->get(route('sitemap.guidelines'));

        $response->assertViewIs('sitemap.guidelines');
    }

    /**
     * The guidelines sitemap shall display the guidelines index route.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('sitemap.guidelines'));

        $response->assertSee(route('guidelines.index'));
    }

    /**
     * The guidelines sitemap shall display the guidelines approved_hosts route.
     *
     * @return void
     */
    public function testApprovedHosts()
    {
        $response = $this->get(route('sitemap.guidelines'));

        $response->assertSee(route('guidelines.show', ['docName' => 'approved_hosts']));
    }

    /**
     * The guidelines sitemap shall display the guidelines submission_title_formatting route.
     *
     * @return void
     */
    public function testSubmissionTitleFormatting()
    {
        $response = $this->get(route('sitemap.guidelines'));

        $response->assertSee(route('guidelines.show', ['docName' => 'submission_title_formatting']));
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Sitemap;

use Tests\TestCase;

/**
 * Class GuidelinesSitemapTest.
 */
class GuidelinesSitemapTest extends TestCase
{
    /**
     * The guidelines sitemap shall display the guidelines sitemap view.
     *
     * @return void
     */
    public function testSitemapIndex(): void
    {
        $response = $this->get(route('sitemap.guidelines'));

        $response->assertViewIs('sitemap.guidelines');
    }

    /**
     * The guidelines sitemap shall display the guidelines index route.
     *
     * @return void
     */
    public function testIndex(): void
    {
        $response = $this->get(route('sitemap.guidelines'));

        $response->assertSee(route('guidelines.index'));
    }

    /**
     * The guidelines sitemap shall display the guidelines approved_hosts route.
     *
     * @return void
     */
    public function testApprovedHosts(): void
    {
        $response = $this->get(route('sitemap.guidelines'));

        $response->assertSee(route('guidelines.show', ['docName' => 'approved_hosts']));
    }

    /**
     * The guidelines sitemap shall display the guideline submission_title_formatting route.
     *
     * @return void
     */
    public function testSubmissionTitleFormatting(): void
    {
        $response = $this->get(route('sitemap.guidelines'));

        $response->assertSee(route('guidelines.show', ['docName' => 'submission_title_formatting']));
    }
}

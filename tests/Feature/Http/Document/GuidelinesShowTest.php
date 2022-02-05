<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

/**
 * Class GuidelinesShowTest.
 */
class GuidelinesShowTest extends TestCase
{
    /**
     * Approved Hosts shall be displayed as a document.
     *
     * @return void
     */
    public function testApprovedHosts(): void
    {
        $response = $this->get(route('guidelines.show', ['docName' => 'approved_hosts']));

        $response->assertViewIs('document');
    }

    /**
     * Submission Title Formatting shall be displayed as a document.
     *
     * @return void
     */
    public function testSubmissionTitleFormatting(): void
    {
        $response = $this->get(route('guidelines.show', ['docName' => 'submission_title_formatting']));

        $response->assertViewIs('document');
    }
}

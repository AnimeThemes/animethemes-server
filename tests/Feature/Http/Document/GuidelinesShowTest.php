<?php

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

class GuidelinesShowTest extends TestCase
{
    /**
     * Approved Hosts shall be displayed as a document.
     *
     * @return void
     */
    public function testApprovedHosts()
    {
        $response = $this->get(route('guidelines.show', ['docName' => 'approved_hosts']));

        $response->assertViewIs('document');
    }

    /**
     * Submission Title Formatting shall be displayed as a document.
     *
     * @return void
     */
    public function testSubmissionTitleFormatting()
    {
        $response = $this->get(route('guidelines.show', ['docName' => 'submission_title_formatting']));

        $response->assertViewIs('document');
    }
}

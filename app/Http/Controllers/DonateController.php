<?php

namespace App\Http\Controllers;

use Laravel\Jetstream\Jetstream;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

class DonateController extends Controller
{
    /**
     * Show the donate document for the application.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $donateFile = Jetstream::localizedMarkdownPath('donate.md');

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        return view('donate', [
            'donate'=> (new CommonMarkConverter([], $environment))->convertToHtml(file_get_contents($donateFile)),
        ]);
    }
}

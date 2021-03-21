<?php

namespace App\Http\Controllers;

use Laravel\Jetstream\Jetstream;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

class FaqController extends Controller
{
    /**
     * Show the FAQ for the application.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $faqFile = Jetstream::localizedMarkdownPath('faq.md');

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        return view('faq', [
            'faq'=> (new CommonMarkConverter([], $environment))->convertToHtml(file_get_contents($faqFile)),
        ]);
    }
}

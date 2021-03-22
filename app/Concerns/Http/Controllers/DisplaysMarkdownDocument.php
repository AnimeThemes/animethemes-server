<?php

namespace App\Concerns\Http\Controllers;

use Laravel\Jetstream\Jetstream;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Element\IndentedCode;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;

trait DisplaysMarkdownDocument
{
    /**
     * Display markdown document.
     *
     * @param string $docPath
     * @return \Illuminate\View\View
     */
    protected function displayMarkdownDocument(string $docPath)
    {
        $document = Jetstream::localizedMarkdownPath($docPath.'.md');

        if ($document === null) {
            abort(404);
        }

        $environment = Environment::createCommonMarkEnvironment();

        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $environment->addExtension(new HeadingPermalinkExtension());

        $environment->addBlockRenderer(FencedCode::class, new FencedCodeRenderer(['powershell', 'json']));
        $environment->addBlockRenderer(IndentedCode::class, new IndentedCodeRenderer(['powershell', 'json']));

        $config = [
            'heading_permalink' => [
                'inner_contents' => '',
            ],
        ];

        return view('document', [
            'document' => (new CommonMarkConverter($config, $environment))->convertToHtml(file_get_contents($document)),
        ]);
    }
}

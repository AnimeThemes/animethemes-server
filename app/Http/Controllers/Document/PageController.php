<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Document\Page;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;

/**
 * Class PageController.
 */
class PageController extends Controller
{
    /**
     * Display Markdown document.
     *
     * @param Page $page
     * @return View|Factory
     */
    public function show(Page $page): View|Factory
    {
        $config = [
            'heading_permalink' => [
                'symbol' => '',
                'id_prefix' => '',
            ],
        ];

        $environment = new Environment($config);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $environment->addRenderer(FencedCode::class, new FencedCodeRenderer(['powershell', 'json']));
        $environment->addRenderer(IndentedCode::class, new IndentedCodeRenderer(['powershell', 'json']));

        $converter = new MarkdownConverter($environment);

        return view('document', [
            'document' => $converter->convert($page->body),
        ]);
    }
}

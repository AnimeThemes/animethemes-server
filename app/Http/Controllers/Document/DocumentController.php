<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Laravel\Jetstream\Jetstream;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DocumentController.
 */
abstract class DocumentController extends Controller
{
    /**
     * Display markdown document.
     *
     * @param string $docPath
     * @return View|Factory
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    protected function displayMarkdownDocument(string $docPath): View | Factory
    {
        $document = Jetstream::localizedMarkdownPath($docPath.'.md');

        if ($document === null) {
            abort(404);
        }

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
            'document' => $converter->convertToHtml(file_get_contents($document)),
        ]);
    }
}

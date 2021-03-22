<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('guidelines.index') }}</loc>
    </url>
    <url>
        <loc>{{ route('guidelines.show', ['docName' => 'submission_title_formatting']) }}</loc>
    </url>
    <url>
        <loc>{{ route('guidelines.show', ['docName' => 'approved_hosts']) }}</loc>
    </url>
</sitemapindex>

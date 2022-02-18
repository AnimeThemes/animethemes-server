<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($pages as $page)
        <url>
            <loc>{{ route('page.show', ['page' => $page]) }}</loc>
        </url>
    @endforeach
</sitemapindex>

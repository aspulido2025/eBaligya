<?php
/**
 * Simple Sitemap Generator (Procedural Version)
 * -------------------------------------------------------
 * Generates sitemap.xml for your site.
 * Works with defined BASE_URL, no Composer required.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ---------------------------------------------------------------------
// CONFIG
// ---------------------------------------------------------------------
require_once __DIR__ . '/../../../config/init.php';

$baseUrl = rtrim(BASE_URL, '/');          

// 🧭 Automatically locate /public directory even if nested under /tools/*
$dir = __DIR__;
while ($dir && basename($dir) !== 'public') {
    $parent = dirname($dir);
    if ($parent === $dir) break;
    $dir = $parent;
}
$rootDir = realpath($dir ?: __DIR__);     // ✅ final /public root
$sitemapFile = $rootDir . '/sitemap.xml';
$urls = [];

// ---------------------------------------------------------------------
// HELPERS
// ---------------------------------------------------------------------
function add_url(&$urls, $loc, $priority = '0.5', $changefreq = 'monthly', $lastmod = null) {
    $urls[] = [
        'loc'        => $loc,
        'priority'   => $priority,
        'changefreq' => $changefreq,
        'lastmod'    => $lastmod ?? date('Y-m-d'),
    ];
}

function scan_directory($dir, $baseUrl, &$urls, $rootDir) {
    // Skip non-public dirs
    $excluded = [
    'tools', 'assets', 'includes', 'rbac', 'auth', 'tables',
    'tests', 'theme', 'xhtml'];
    if (in_array(basename($dir), $excluded)) return;

    $files = glob("$dir/*.{php,html}", GLOB_BRACE);
    foreach ($files as $file) {
        $basename = basename($file);
        if (in_array($basename, ['generate_sitemap.php', 'config.php', 'db.php'])) continue;

        // 🔹 Get relative path (trim absolute /home/... up to /public)
        $relPath = str_replace($rootDir, '', $file);
        $relPath = str_replace('\\', '/', $relPath);
        $relPath = ltrim($relPath, '/'); // clean double slashes

        // 🔹 Build final public URL
        $url = rtrim($baseUrl, '/') . '/' . $relPath;

        // Priority rules
        $priority = '0.7';
        $changefreq = 'weekly';
        if (preg_match('/index/i', $basename)) {
            $priority = '1.0';
            $changefreq = 'daily';
        } elseif (preg_match('/(about|contact)/i', $basename)) {
            $priority = '0.5';
            $changefreq = 'monthly';
        }

        add_url($urls, $url, $priority, $changefreq, date('Y-m-d', filemtime($file)));
    }

    // Recurse into subdirectories
    foreach (glob("$dir/*", GLOB_ONLYDIR) as $subdir) {
        scan_directory($subdir, $baseUrl, $urls, $rootDir);
    }
}


// ---------------------------------------------------------------------
// GENERATE
// ---------------------------------------------------------------------
add_url($urls, "$baseUrl/", '1.0', 'daily'); // homepage
scan_directory($rootDir, $baseUrl, $urls, $rootDir);


$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>');
$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

foreach ($urls as $item) {
    $url = $xml->addChild('url');
    $url->addChild('loc', htmlspecialchars($item['loc']));
    $url->addChild('lastmod', $item['lastmod']);
    $url->addChild('changefreq', $item['changefreq']);
    $url->addChild('priority', $item['priority']);
}

$xml->asXML($sitemapFile);

echo "✅ Sitemap generated successfully at <b>{$sitemapFile}</b><br>";
echo "Total URLs: " . count($urls);

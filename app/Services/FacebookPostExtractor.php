<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FacebookPostExtractor
{
    /**
     * Extract post title, campaign tag, subtitle, and real engagement metrics (likes, comments, shares)
     * from a Facebook post URL.
     *
     * @return array{
     *     post_url: string,
     *     canonical_url: string,
     *     title: string,
     *     campaign_tag: string,
     *     banner_subtitle: string,
     *     likes: int,
     *     comments: int,
     *     shares: int,
     *     post_type: string,
     *     source_mode: string,
     *     has_live_metrics: bool,
     *     message: string
     * }
     */
    public function extractFromUrl(string $rawUrl, ?string $fbCookie = null): array
    {
        $url = $this->normalizeFacebookUrl(trim($rawUrl));
        $postType = $this->detectPostType($url);

        $liveData = $this->fetchPublicPageData($url, $fbCookie);

        $title = $liveData['title'] ?? $this->generateFallbackTitle($url, $postType);
        $campaignTag = $this->inferCampaignTag($title);
        $bannerSubtitle = $liveData['subtitle'] ?? $this->inferBannerSubtitle($title, $campaignTag);

        $hasLiveMetrics = $liveData['likes'] !== null
            || $liveData['comments'] !== null
            || $liveData['shares'] !== null;

        $likes = (int) ($liveData['likes'] ?? 0);
        $comments = (int) ($liveData['comments'] ?? 0);
        $shares = (int) ($liveData['shares'] ?? 0);

        if ($hasLiveMetrics) {
            $sourceMode = 'live_facebook';
            $message = "Data asli berhasil dibaca dari Facebook: {$likes} Likes • {$comments} Komentar • {$shares} Share";
        } elseif ($postType === 'Facebook Marketplace Listing') {
            $sourceMode = 'marketplace_listing';
            $message = 'Judul terbaca. Catatan: Halaman produk Marketplace (marketplace/item) di Facebook memang tidak menampilkan angka Like, Komen & Share secara publik. Silakan isi angkanya atau gunakan link postingan Feed/Grup.';
        } else {
            $sourceMode = 'login_restricted';
            $message = 'Facebook membatasi pembacaan angka Like/Komen/Share tanpa login pada link ini (privasi akun/anti-bot FB). Silakan masukkan angkanya atau isi Cookie FB (opsional).';
        }

        return [
            'post_url' => $url,
            'canonical_url' => $liveData['canonical_url'] ?? $url,
            'title' => Str::limit($title, 175, ''),
            'campaign_tag' => Str::limit($campaignTag, 45, ''),
            'banner_subtitle' => Str::limit($bannerSubtitle, 115, ''),
            'likes' => max(0, $likes),
            'comments' => max(0, $comments),
            'shares' => max(0, $shares),
            'post_type' => $postType,
            'source_mode' => $sourceMode,
            'has_live_metrics' => $hasLiveMetrics,
            'message' => $message,
        ];
    }

    /**
     * Normalize mobile or web subdomains to www.facebook.com.
     */
    protected function normalizeFacebookUrl(string $url): string
    {
        return preg_replace('#^https?://(m|web|mbasic|touch)\.facebook\.com/#i', 'https://www.facebook.com/', $url) ?? $url;
    }

    /**
     * Detect the kind of Facebook link pasted by the user.
     */
    protected function detectPostType(string $url): string
    {
        $lower = strtolower($url);

        if (str_contains($lower, '/marketplace/item')) {
            return 'Facebook Marketplace Listing';
        }

        if (str_contains($lower, '/groups/')) {
            return 'Facebook Group Post';
        }

        if (str_contains($lower, '/share/')) {
            return 'Facebook Share Link';
        }

        return 'Facebook Post';
    }

    /**
     * Multi-stage fetch:
     * 1. Crawler OpenGraph fetch (resolves /share/p/ shortlinks to canonical URL & reads og:title / og:description)
     * 2. Desktop Comet SSR fetch (parses Relay JSON reaction_count, total_comment_count, share_count)
     * 3. Facebook Official Embed Plugin fetch (/plugins/post.php)
     *
     * @return array{canonical_url: string, title: ?string, subtitle: ?string, likes: ?int, comments: ?int, shares: ?int}
     */
    protected function fetchPublicPageData(string $url, ?string $fbCookie = null): array
    {
        $result = [
            'canonical_url' => $url,
            'title' => null,
            'subtitle' => null,
            'likes' => null,
            'comments' => null,
            'shares' => null,
        ];

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return $result;
        }

        $cookieHeader = trim((string) ($fbCookie ?: config('services.facebook.cookie', env('FACEBOOK_COOKIE', ''))));

        // Stage 1: Fetch with crawler User-Agent to resolve share links and read OpenGraph metadata
        try {
            $crawlerHeaders = [
                'User-Agent' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
            ];
            if ($cookieHeader !== '') {
                $crawlerHeaders['Cookie'] = $cookieHeader;
            }

            $crawlerResponse = Http::withoutVerifying()
                ->withHeaders($crawlerHeaders)
                ->timeout(6)
                ->get($url);

            if ($crawlerResponse->successful()) {
                $crawlerHtml = $crawlerResponse->body();
                $ogUrl = $this->extractMetaContent($crawlerHtml, 'og:url');
                if ($ogUrl && filter_var($ogUrl, FILTER_VALIDATE_URL) && ! str_contains($ogUrl, '/login')) {
                    $result['canonical_url'] = $this->normalizeFacebookUrl($ogUrl);
                }

                $this->populateTitleAndSubtitleFromHtml($crawlerHtml, $result);
                $this->mergeMetrics($result, $this->parsePublicMetricsFromHtml($crawlerHtml));
            }
        } catch (\Throwable) {
            // Continue to Stage 2
        }

        // Stage 2: Fetch with Desktop Chrome Navigation headers to extract Comet Relay JSON counts
        $targetUrls = array_unique([$result['canonical_url'], $url]);
        foreach ($targetUrls as $targetUrl) {
            if ($result['likes'] !== null && $result['comments'] !== null && $result['shares'] !== null) {
                break;
            }

            try {
                $browserHeaders = [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Sec-Ch-Ua' => '"Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                    'Sec-Ch-Ua-Mobile' => '?0',
                    'Sec-Ch-Ua-Platform' => '"Windows"',
                    'Sec-Fetch-Dest' => 'document',
                    'Sec-Fetch-Mode' => 'navigate',
                    'Sec-Fetch-Site' => 'none',
                    'Upgrade-Insecure-Requests' => '1',
                ];
                if ($cookieHeader !== '') {
                    $browserHeaders['Cookie'] = $cookieHeader;
                }

                $browserResponse = Http::withoutVerifying()
                    ->withHeaders($browserHeaders)
                    ->timeout(6)
                    ->get($targetUrl);

                if ($browserResponse->successful()) {
                    $browserHtml = $browserResponse->body();

                    $ogUrl = $this->extractMetaContent($browserHtml, 'og:url');
                    if ($ogUrl && filter_var($ogUrl, FILTER_VALIDATE_URL) && ! str_contains($ogUrl, '/login')) {
                        $result['canonical_url'] = $this->normalizeFacebookUrl($ogUrl);
                    }

                    $this->populateTitleAndSubtitleFromHtml($browserHtml, $result);
                    $this->mergeMetrics($result, $this->parsePublicMetricsFromHtml($browserHtml));
                }
            } catch (\Throwable) {
                // Continue to Stage 3
            }
        }

        // Stage 3: Try Facebook's official public post embed widget (/plugins/post.php)
        if ($result['likes'] === null && $result['comments'] === null && $result['shares'] === null) {
            try {
                $embedUrl = 'https://www.facebook.com/plugins/post.php?href='.urlencode($result['canonical_url']).'&show_text=true&width=500';
                $embedHeaders = [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                    'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Referer' => 'https://www.facebook.com/',
                ];
                if ($cookieHeader !== '') {
                    $embedHeaders['Cookie'] = $cookieHeader;
                }

                $embedResponse = Http::withoutVerifying()
                    ->withHeaders($embedHeaders)
                    ->timeout(5)
                    ->get($embedUrl);

                if ($embedResponse->successful()) {
                    $embedHtml = $embedResponse->body();
                    $this->populateTitleAndSubtitleFromHtml($embedHtml, $result);
                    $this->mergeMetrics($result, $this->parsePublicMetricsFromHtml($embedHtml));
                    $this->mergeMetrics($result, $this->parseEmbedPluginHtml($embedHtml));
                }
            } catch (\Throwable) {
                // Ignore embed errors
            }
        }

        return $result;
    }

    /**
     * @param  array{canonical_url: string, title: ?string, subtitle: ?string, likes: ?int, comments: ?int, shares: ?int}  $result
     */
    protected function populateTitleAndSubtitleFromHtml(string $html, array &$result): void
    {
        $ogTitle = $this->extractMetaContent($html, 'og:title')
            ?? $this->extractMetaContent($html, 'twitter:title')
            ?? $this->extractHtmlTitle($html);

        $ogDescription = $this->extractMetaContent($html, 'og:description')
            ?? $this->extractMetaContent($html, 'twitter:description')
            ?? $this->extractMetaContent($html, 'description');

        // Also look for post message text in Comet JSON if OpenGraph title is generic
        $jsonText = null;
        if (preg_match('/"message"\s*:\s*\{\s*"text"\s*:\s*"([^"]{5,300})"/i', $html, $m)) {
            $decoded = json_decode('"'.$m[1].'"');
            if (is_string($decoded)) {
                $jsonText = $this->cleanFacebookText($decoded);
            }
        }

        $cleanedTitle = $this->cleanFacebookText($ogTitle);
        $cleanedDescription = $this->cleanFacebookText($ogDescription);

        if ($result['title'] === null) {
            if ($cleanedTitle && ! $this->isGenericFacebookTitle($cleanedTitle)) {
                $result['title'] = $cleanedTitle;
            } elseif ($cleanedDescription && ! $this->isGenericFacebookTitle($cleanedDescription)) {
                $result['title'] = Str::limit($cleanedDescription, 120, '');
            } elseif ($jsonText && ! $this->isGenericFacebookTitle($jsonText)) {
                $result['title'] = Str::limit($jsonText, 120, '');
            }
        }

        if ($result['subtitle'] === null) {
            if ($cleanedDescription && $cleanedDescription !== $result['title'] && ! $this->isGenericFacebookTitle($cleanedDescription)) {
                $result['subtitle'] = Str::limit($cleanedDescription, 100, '');
            } elseif ($jsonText && $jsonText !== $result['title'] && ! $this->isGenericFacebookTitle($jsonText)) {
                $result['subtitle'] = Str::limit($jsonText, 100, '');
            }
        }
    }

    /**
     * @param  array{canonical_url: string, title: ?string, subtitle: ?string, likes: ?int, comments: ?int, shares: ?int}  $target
     * @param  array{likes: ?int, comments: ?int, shares: ?int}  $source
     */
    protected function mergeMetrics(array &$target, array $source): void
    {
        if ($target['likes'] === null && $source['likes'] !== null) {
            $target['likes'] = $source['likes'];
        }
        if ($target['comments'] === null && $source['comments'] !== null) {
            $target['comments'] = $source['comments'];
        }
        if ($target['shares'] === null && $source['shares'] !== null) {
            $target['shares'] = $source['shares'];
        }
    }

    /**
     * Parse reaction, comment, and share counts from Facebook Comet Relay JSON & meta description.
     *
     * @return array{likes: ?int, comments: ?int, shares: ?int}
     */
    protected function parsePublicMetricsFromHtml(string $html): array
    {
        $likes = null;
        $comments = null;
        $shares = null;

        $description = (string) ($this->extractMetaContent($html, 'og:description')
            ?? $this->extractMetaContent($html, 'description')
            ?? '');

        // 1. Likes / Reactions from Comet Relay JSON & Meta Description
        $likePatterns = [
            '/"reaction_count"\s*:\s*\{\s*"count"\s*:\s*(\d+)/i',
            '/"unified_reactors"\s*:\s*\{\s*"count"\s*:\s*(\d+)/i',
            '/"reactors"\s*:\s*\{\s*"count"\s*:\s*(\d+)/i',
            '/"likers"\s*:\s*\{\s*"count"\s*:\s*(\d+)/i',
            '/"reaction_count"\s*:\s*(\d+)/i',
            '/"i18n_reaction_count"\s*:\s*"([\d\.,]+(?:\s*(?:rb|jt|K|M))?)"/i',
        ];

        foreach ($likePatterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                $likes = $this->parseFormattedCount($m[1]);
                break;
            }
        }

        if ($likes === null && preg_match('/([\d\.,]+(?:\s*(?:rb|jt|K|M))?)\s+(?:suka|likes|reactions)/i', $description, $m)) {
            $likes = $this->parseFormattedCount($m[1]);
        }

        // 2. Comments from Comet Relay JSON & Meta Description
        $commentPatterns = [
            '/"comment_rendering_instance"\s*:\s*\{\s*"comments"\s*:\s*\{\s*"total_count"\s*:\s*(\d+)/i',
            '/"total_comment_count"\s*:\s*(\d+)/i',
            '/"comment_count"\s*:\s*\{\s*"total_count"\s*:\s*(\d+)/i',
            '/"comments"\s*:\s*\{\s*"total_count"\s*:\s*(\d+)/i',
            '/"comments"\s*:\s*\{\s*"count"\s*:\s*(\d+)/i',
            '/"i18n_comment_count"\s*:\s*"([\d\.,]+(?:\s*(?:rb|jt|K|M))?)"/i',
        ];

        foreach ($commentPatterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                $comments = $this->parseFormattedCount($m[1]);
                break;
            }
        }

        if ($comments === null && preg_match('/([\d\.,]+(?:\s*(?:rb|jt|K|M))?)\s+(?:komentar|comments)/i', $description, $m)) {
            $comments = $this->parseFormattedCount($m[1]);
        }

        // 3. Shares from Comet Relay JSON & Meta Description
        $sharePatterns = [
            '/"share_count"\s*:\s*\{\s*"count"\s*:\s*(\d+)/i',
            '/"reshares"\s*:\s*\{\s*"count"\s*:\s*(\d+)/i',
            '/"global_share_count"\s*:\s*(\d+)/i',
            '/"i18n_share_count"\s*:\s*"([\d\.,]+(?:\s*(?:rb|jt|K|M))?)"/i',
        ];

        foreach ($sharePatterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                $shares = $this->parseFormattedCount($m[1]);
                break;
            }
        }

        if ($shares === null && preg_match('/([\d\.,]+(?:\s*(?:rb|jt|K|M))?)\s+(?:kali dibagikan|dibagikan|shares)/i', $description, $m)) {
            $shares = $this->parseFormattedCount($m[1]);
        }

        return [
            'likes' => $likes,
            'comments' => $comments,
            'shares' => $shares,
        ];
    }

    /**
     * Parse counts from Facebook's /plugins/post.php embed HTML.
     *
     * @return array{likes: ?int, comments: ?int, shares: ?int}
     */
    protected function parseEmbedPluginHtml(string $html): array
    {
        $likes = null;
        $comments = null;
        $shares = null;

        if (preg_match('/class="[^"]*(?:_1g5v|_4arz)[^"]*"[^>]*>\s*<span[^>]*>([\d\.,]+(?:\s*(?:rb|jt|K|M))?)<\/span>/i', $html, $m)) {
            $likes = $this->parseFormattedCount($m[1]);
        } elseif (preg_match('/class="[^"]*(?:_1g5v|_4arz)[^"]*"[^>]*>([\d\.,]+(?:\s*(?:rb|jt|K|M))?)<\//i', $html, $m)) {
            $likes = $this->parseFormattedCount($m[1]);
        }

        if (preg_match('/>([\d\.,]+(?:\s*(?:rb|jt|K|M))?)\s*(?:Komentar|Comments)</i', $html, $m)) {
            $comments = $this->parseFormattedCount($m[1]);
        }

        if (preg_match('/>([\d\.,]+(?:\s*(?:rb|jt|K|M))?)\s*(?:Kali Dibagikan|Dibagikan|Shares|Share)</i', $html, $m)) {
            $shares = $this->parseFormattedCount($m[1]);
        }

        return [
            'likes' => $likes,
            'comments' => $comments,
            'shares' => $shares,
        ];
    }

    /**
     * Convert compact/formatted counts (e.g. "1.250", "1,2 rb", "2.5K") into integer.
     */
    protected function parseFormattedCount(string $raw): int
    {
        $clean = trim(strtolower($raw));

        if (preg_match('/^([\d\.,]+)\s*(rb|k)$/i', $clean, $m)) {
            $num = (float) str_replace(',', '.', $m[1]);

            return (int) round($num * 1000);
        }

        if (preg_match('/^([\d\.,]+)\s*(jt|m)$/i', $clean, $m)) {
            $num = (float) str_replace(',', '.', $m[1]);

            return (int) round($num * 1000000);
        }

        return (int) preg_replace('/[^\d]/', '', $clean);
    }

    protected function extractMetaContent(string $html, string $property): ?string
    {
        $quoted = preg_quote($property, '/');
        $patterns = [
            '/<meta[^>]+(?:property|name)=["\']'.$quoted.'["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<meta[^>]+content=["\']([^"\']+)["\'][^>]+(?:property|name)=["\']'.$quoted.'["\']/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        return null;
    }

    protected function extractHtmlTitle(string $html): ?string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            return html_entity_decode(trim($matches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return null;
    }

    protected function cleanFacebookText(?string $text): ?string
    {
        if (! $text) {
            return null;
        }

        $cleaned = preg_replace('/\s*\|\s*Facebook.*$/i', '', trim($text)) ?? trim($text);
        $cleaned = preg_replace('/^Facebook Marketplace\s*[-–:]\s*/i', '', $cleaned) ?? $cleaned;
        $cleaned = trim(preg_replace('/\s+/', ' ', $cleaned) ?? $cleaned);

        return $cleaned !== '' ? $cleaned : null;
    }

    protected function isGenericFacebookTitle(string $title): bool
    {
        $lower = strtolower(trim($title));
        $genericList = [
            'facebook',
            'masuk ke facebook',
            'log into facebook',
            'log in or sign up to view',
            'facebook marketplace',
            'marketplace',
            'error',
            'konten tidak ditemukan',
        ];

        foreach ($genericList as $generic) {
            if ($lower === $generic || str_starts_with($lower, 'masuk ke facebook') || str_starts_with($lower, 'log in')) {
                return true;
            }
        }

        return strlen($lower) < 4;
    }

    protected function generateFallbackTitle(string $url, string $postType): string
    {
        if (preg_match('/(?:item|posts|p|permalink)\/([a-zA-Z0-9_-]{4,25})/i', $url, $matches)) {
            $shortId = strtoupper(substr($matches[1], 0, 8));
        } else {
            $shortId = strtoupper(substr(dechex(crc32($url)), 0, 6));
        }

        return "Postingan {$postType} (#{$shortId})";
    }

    protected function inferCampaignTag(string $title): string
    {
        $lower = strtolower($title);

        if (str_contains($lower, 'orbit')) {
            return 'Telkomsel Orbit';
        }
        if (str_contains($lower, 'one') || str_contains($lower, 'keluarga')) {
            return 'Telkomsel One';
        }
        if (str_contains($lower, 'gamer') || str_contains($lower, 'gaming')) {
            return 'IndiHome Gamer';
        }
        if (str_contains($lower, '50 mbps') || str_contains($lower, '100 mbps')) {
            return 'Promo 50 Mbps';
        }
        if (str_contains($lower, 'bisnis') || str_contains($lower, 'umkm')) {
            return 'IndiHome Bisnis';
        }

        return 'Paket JITU';
    }

    protected function inferBannerSubtitle(string $title, string $campaignTag): string
    {
        return match ($campaignTag) {
            'Telkomsel Orbit' => 'Langsung Aktif Tanpa Kabel, Kuota Besar!',
            'Telkomsel One' => 'Satu Tagihan Praktis untuk Seluruh Keluarga!',
            'IndiHome Gamer' => 'Ping Stabil & Koneksi Fiber Cepat Seharian!',
            'Promo 50 Mbps' => 'Streaming & Bekerja Tanpa Buffering Seharian!',
            'IndiHome Bisnis' => 'Koneksi Stabil 24 Jam untuk Operasional Usaha!',
            default => 'Internet Lebih Cepat Harga Lebih Hemat!',
        };
    }
}

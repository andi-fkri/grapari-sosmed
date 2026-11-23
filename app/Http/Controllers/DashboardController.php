<?php

namespace App\Http\Controllers;

use App\Models\DailyMetric;
use App\Models\Grapari;
use App\Models\SocialContent;
use App\Services\FacebookPostExtractor;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $this->recalculateAllGrapariAndTrendMetrics();

        $graparis = Grapari::orderBy('rank')->get();
        $contents = SocialContent::with('grapari')
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->get();
        $featuredContent = $contents->firstWhere('is_featured', true) ?? $contents->first();
        $dailyMetrics = DailyMetric::orderBy('metric_date')->get();

        $summary = $this->buildSummaryMetrics($graparis);

        $topGrapari = $graparis->sortByDesc(fn (Grapari $g) => $g->likes + $g->comments + $g->direct_chats)->first()
            ?? $graparis->first();
        $activeTab = $request->query('tab', 'dashboard');

        $isBranchUser = $request->session()->get('grapari_role') === 'branch'
            && $request->session()->get('grapari_branch_id');
        $branchGrapariId = $isBranchUser ? (int) $request->session()->get('grapari_branch_id') : null;
        $branchGrapari = $branchGrapariId ? $graparis->firstWhere('id', $branchGrapariId) : null;
        $selectedGrapariId = $isBranchUser ? $branchGrapariId : $request->query('grapari');

        return view('dashboard', compact(
            'graparis',
            'contents',
            'featuredContent',
            'dailyMetrics',
            'summary',
            'topGrapari',
            'activeTab',
            'selectedGrapariId',
            'isBranchUser',
            'branchGrapari'
        ));
    }

    /**
     * Compute summary KPI metrics directly from the actual GraPARI totals (Likes, Komentar, Share).
     *
     * @param  Collection<int, Grapari>  $graparis
     * @return array<string, int|string>
     */
    protected function buildSummaryMetrics(Collection $graparis): array
    {
        $totalContents = (int) $graparis->sum('total_contents');
        $totalLikes = (int) $graparis->sum('likes');
        $totalComments = (int) $graparis->sum('comments');
        $totalShares = (int) $graparis->sum('direct_chats');

        return [
            'total_grapari' => $graparis->count(),
            'total_konten' => max(0, $totalContents),
            'total_likes' => max(0, $totalLikes),
            'total_komentar' => max(0, $totalComments),
            'total_direct_chat' => max(0, $totalShares),
            'total_share' => max(0, $totalShares),
            'growth_konten' => $totalContents > 0 ? '+100%' : '0%',
            'growth_likes' => $totalLikes > 0 ? '+100%' : '0%',
            'growth_komentar' => $totalComments > 0 ? '+100%' : '0%',
            'growth_direct_chat' => $totalShares > 0 ? '+100%' : '0%',
            'growth_share' => $totalShares > 0 ? '+100%' : '0%',
        ];
    }

    /**
     * Automatically extract title, likes, comments, and shares from a pasted Facebook URL.
     */
    public function fetchLinkMetrics(Request $request, FacebookPostExtractor $extractor): JsonResponse
    {
        $validated = $request->validate([
            'post_url' => 'required|url|max:500',
            'fb_cookie' => 'nullable|string|max:4000',
        ]);

        $extracted = $extractor->extractFromUrl(
            $validated['post_url'],
            $validated['fb_cookie'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => $extracted,
        ]);
    }

    /**
     * Store a new Facebook Marketplace / Personal post content item.
     * Supports pasting only the Facebook URL and auto-populating all details & metrics.
     */
    public function storeContent(Request $request, FacebookPostExtractor $extractor): RedirectResponse
    {
        $validated = $request->validate([
            'grapari_id' => 'required|exists:graparis,id',
            'post_url' => 'nullable|url|max:500',
            'fb_cookie' => 'nullable|string|max:4000',
            'title' => 'nullable|string|max:180',
            'campaign_tag' => 'nullable|string|max:50',
            'banner_subtitle' => 'nullable|string|max:120',
            'likes' => 'nullable|integer|min:0',
            'comments' => 'nullable|integer|min:0',
            'shares' => 'nullable|integer|min:0',
            'direct_chats' => 'nullable|integer|min:0',
            'set_featured' => 'nullable|boolean',
        ]);

        $extracted = ! empty($validated['post_url'])
            ? $extractor->extractFromUrl($validated['post_url'], $validated['fb_cookie'] ?? null)
            : null;

        $title = ! empty($validated['title'])
            ? $validated['title']
            : ($extracted['title'] ?? 'Promo IndiHome Paket JITU Terbaru untuk Anda!');

        $campaignTag = ! empty($validated['campaign_tag'])
            ? $validated['campaign_tag']
            : ($extracted['campaign_tag'] ?? 'Paket JITU');

        $bannerSubtitle = ! empty($validated['banner_subtitle'])
            ? $validated['banner_subtitle']
            : ($extracted['banner_subtitle'] ?? 'Internet Lebih Cepat Harga Lebih Hemat!');

        $likes = isset($validated['likes']) && $validated['likes'] !== ''
            ? (int) $validated['likes']
            : ($extracted['likes'] ?? 0);

        $comments = isset($validated['comments']) && $validated['comments'] !== ''
            ? (int) $validated['comments']
            : ($extracted['comments'] ?? 0);

        $sharesInput = $validated['shares'] ?? $validated['direct_chats'] ?? null;
        $shares = isset($sharesInput) && $sharesInput !== ''
            ? (int) $sharesInput
            : ($extracted['shares'] ?? 0);

        if (! empty($validated['set_featured'])) {
            SocialContent::query()->update(['is_featured' => false]);
        }

        $targetGrapariId = ($request->session()->get('grapari_role') === 'branch' && $request->session()->get('grapari_branch_id'))
            ? (int) $request->session()->get('grapari_branch_id')
            : (int) $validated['grapari_id'];

        SocialContent::create([
            'grapari_id' => $targetGrapariId,
            'post_url' => $validated['post_url'] ?? null,
            'title' => $title,
            'campaign_tag' => $campaignTag,
            'banner_subtitle' => $bannerSubtitle,
            'platform' => 'Facebook Marketplace',
            'likes' => $likes,
            'comments' => $comments,
            'direct_chats' => $shares,
            'status' => 'Published',
            'is_featured' => ! empty($validated['set_featured']),
            'published_at' => now(),
        ]);

        $this->recalculateAllGrapariAndTrendMetrics();

        return redirect()->route('dashboard', ['grapari' => $targetGrapariId])
            ->with('success', 'Konten Facebook Marketplace berhasil ditambahkan & seluruh grafik diperbarui!');
    }

    /**
     * Delete a content item and recalculate all GraPARI and chart metrics.
     */
    public function destroyContent(SocialContent $content): RedirectResponse
    {
        $grapariId = $content->grapari_id;
        $content->delete();
        $this->recalculateAllGrapariAndTrendMetrics();

        return redirect()->route('dashboard', ['grapari' => $grapariId])
            ->with('success', 'Konten berhasil dihapus & grafik telah diperbarui.');
    }

    /**
     * Re-sync a single content item's Likes, Comments, and Shares from its saved Facebook link.
     */
    public function syncContentMetrics(Request $request, SocialContent $content, FacebookPostExtractor $extractor): RedirectResponse
    {
        if (empty($content->post_url)) {
            return redirect()->route('dashboard', ['grapari' => $content->grapari_id])
                ->with('success', 'Konten tidak memiliki link Facebook untuk disinkronkan.');
        }

        $fbCookie = $request->input('fb_cookie');
        $updated = $this->syncSingleContentRecord($content, $extractor, is_string($fbCookie) ? $fbCookie : null);

        $msg = $updated
            ? "Metrik & grafik untuk \"{$content->title}\" berhasil diperbarui secara real-time dari Facebook!"
            : "Metrik untuk \"{$content->title}\" sudah sesuai (atau link membutuhkan Cookie login FB).";

        return redirect()->route('dashboard', ['grapari' => $content->grapari_id])
            ->with('success', $msg);
    }

    /**
     * Real-time background Auto-Sync endpoint that checks saved Facebook URLs and returns updated dashboard state.
     */
    public function syncAllMetrics(Request $request, FacebookPostExtractor $extractor): JsonResponse
    {
        $fbCookie = $request->input('fb_cookie');
        $updatedCount = $this->performBulkFacebookSync($extractor, is_string($fbCookie) ? $fbCookie : null);

        $this->recalculateAllGrapariAndTrendMetrics();

        $graparis = Grapari::orderBy('rank')->get();
        $contents = SocialContent::with('grapari')
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->get();
        $featuredContent = $contents->firstWhere('is_featured', true) ?? $contents->first();
        $dailyMetrics = DailyMetric::orderBy('metric_date')->get();
        $summary = $this->buildSummaryMetrics($graparis);

        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount,
            'synced_at' => now()->format('H:i:s'),
            'summary' => $summary,
            'featured_content' => $featuredContent,
            'graparis' => $graparis,
            'contents' => $contents,
            'daily_metrics' => $dailyMetrics,
        ]);
    }

    /**
     * Sync all non-demo Facebook URLs and update content & GraPARI aggregates if live numbers changed.
     */
    public function performBulkFacebookSync(FacebookPostExtractor $extractor, ?string $fbCookie = null): int
    {
        $contentsToSync = SocialContent::with('grapari')
            ->whereNotNull('post_url')
            ->where('post_url', '!=', '')
            ->orderByDesc('published_at')
            ->limit(15)
            ->get();

        $updatedCount = 0;

        foreach ($contentsToSync as $content) {
            if ($this->syncSingleContentRecord($content, $extractor, $fbCookie)) {
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * Sync a single SocialContent record with live Facebook metrics.
     */
    protected function syncSingleContentRecord(SocialContent $content, FacebookPostExtractor $extractor, ?string $fbCookie = null): bool
    {
        if (empty($content->post_url)) {
            return false;
        }

        $extracted = $extractor->extractFromUrl($content->post_url, $fbCookie);

        if (! $extracted['has_live_metrics']) {
            return false;
        }

        $hasChanges = $extracted['likes'] !== $content->likes
            || $extracted['comments'] !== $content->comments
            || $extracted['shares'] !== $content->direct_chats;

        $content->update([
            'likes' => $extracted['likes'],
            'comments' => $extracted['comments'],
            'direct_chats' => $extracted['shares'],
        ]);

        if ($hasChanges) {
            $this->recalculateAllGrapariAndTrendMetrics();
        }

        return $hasChanges;
    }

    /**
     * Recalculate every GraPARI's totals, average-per-content engagement rate,
     * and rebuild the 30-day real-time daily trend metrics grouped by actual content date.
     */
    public function recalculateAllGrapariAndTrendMetrics(): void
    {
        $allContents = SocialContent::all();
        $allGraparis = Grapari::orderBy('rank')->get();

        foreach ($allGraparis as $g) {
            $branchContents = $allContents->where('grapari_id', $g->id);
            $count = $branchContents->count();
            $likes = (int) $branchContents->sum('likes');
            $comments = (int) $branchContents->sum('comments');
            $shares = (int) $branchContents->sum('direct_chats');
            $branchInteractions = $likes + $comments + $shares;

            // Engagement rate based on average interactions per content: (Likes + Komentar + Share) / Total Konten
            $rate = $count > 0
                ? round($branchInteractions / $count, 1)
                : 0.0;

            $g->update([
                'total_contents' => $count,
                'likes' => $likes,
                'comments' => $comments,
                'direct_chats' => $shares,
                'chart_likes' => $likes,
                'chart_comments' => $comments,
                'chart_direct_chats' => $shares,
                'engagement_rate' => min(9999.9, $rate),
            ]);
        }

        $this->rebuildRealTimeDailyMetrics($allContents);
    }

    /**
     * Rebuild the monthly daily_metrics table for the current calendar month (1st to end of month),
     * aggregating likes, comments, and shares by the real date each content was published.
     *
     * @param  Collection<int, SocialContent>  $allContents
     */
    protected function rebuildRealTimeDailyMetrics(Collection $allContents): void
    {
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $today = Carbon::now()->startOfDay();
        $startOfMonth = $today->copy()->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        $groupedByDate = $allContents->groupBy(function (SocialContent $content) use ($today) {
            return ($content->published_at ?? $today)->toDateString();
        });

        DailyMetric::query()->delete();

        $rows = [];
        $nowTimestamp = now();

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startOfMonth->copy()->day($day);
            $dateStr = $date->toDateString();
            $label = $date->day.' '.$months[$date->month];

            /** @var Collection<int, SocialContent> $dayContents */
            $dayContents = $groupedByDate->get($dateStr, collect());

            $rows[] = [
                'metric_date' => $dateStr,
                'label' => $label,
                'likes' => (int) $dayContents->sum('likes'),
                'comments' => (int) $dayContents->sum('comments'),
                'direct_chats' => (int) $dayContents->sum('direct_chats'),
                'created_at' => $nowTimestamp,
                'updated_at' => $nowTimestamp,
            ];
        }

        DailyMetric::insert($rows);
    }

    public function resetDemo(): RedirectResponse
    {
        Artisan::call('db:seed', ['--force' => true]);

        return redirect()->route('dashboard')
            ->with('success', 'Seluruh konten telah dikosongkan dan data di-reset ke 0!');
    }
}

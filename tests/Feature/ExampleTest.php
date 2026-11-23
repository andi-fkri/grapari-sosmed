<?php

namespace Tests\Feature;

use App\Models\Grapari;
use App\Models\SocialContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_login_with_admin_credentials_and_logout(): void
    {
        $this->seed();

        $loginPage = $this->get(route('login'));
        $loginPage->assertOk();
        $loginPage->assertSee('Masuk ke Dashboard');

        $invalidResponse = $this->post(route('login.submit'), [
            'username' => 'admin',
            'password' => 'wrongpassword',
        ]);
        $invalidResponse->assertSessionHasErrors('login');

        $validResponse = $this->post(route('login.submit'), [
            'username' => 'admin',
            'password' => 'admin',
        ]);
        $validResponse->assertRedirect(route('dashboard'));
        $this->assertTrue(session('grapari_authenticated'));

        $logoutResponse = $this->post(route('logout'));
        $logoutResponse->assertRedirect(route('login'));
    }

    public function test_the_dashboard_returns_a_successful_response_with_seeded_data(): void
    {
        $this->seed();

        $response = $this->withSession(['grapari_authenticated' => true])->get('/');

        $response->assertStatus(200);
        $response->assertSee('GraPARI Social Media Monitoring');
        $response->assertSee('Northern Manado Region');
        $response->assertSee('GraPARI Manado');
        $response->assertSee('GraPARI Ternate');
        $response->assertSee('GraPARI Tomohon');
        $response->assertSee('GraPARI Bitung');
        $response->assertSee('GraPARI Airmadidi');
        $response->assertSee('GraPARI Tahuna');
        $response->assertSee('GraPARI Tobelo');
        $response->assertSee('GraPARI Obi');
        $response->assertSee('GraPARI Weda');
        $response->assertSee('GraPARI Labuha');
        $response->assertSee('Total Share');
        $response->assertSee('Auto-Sync');
        $response->assertSee('Belum Ada Konten');
        $response->assertSee('0.0%');
        $this->assertSame(0, SocialContent::count());
    }

    public function test_it_can_auto_fetch_metrics_from_a_facebook_post_link(): void
    {
        $response = $this->withSession(['grapari_authenticated' => true])
            ->postJson(route('contents.fetch-link'), [
                'post_url' => 'https://www.facebook.com/marketplace/item/1148920394817263/',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'post_url',
                    'title',
                    'campaign_tag',
                    'banner_subtitle',
                    'likes',
                    'comments',
                    'shares',
                    'post_type',
                    'source_mode',
                ],
            ]);
    }

    public function test_it_can_store_content_by_only_pasting_facebook_url(): void
    {
        $this->seed();

        Http::fake([
            '*' => Http::response(
                '<html><head><meta property="og:title" content="Promo IndiHome Paket JITU Manado" /></head><body><script type="application/json">{"reaction_count":{"count":142},"total_comment_count":29,"share_count":{"count":18}}</script></body></html>',
                200
            ),
        ]);

        $grapari = Grapari::firstOrFail();
        $initialContents = $grapari->total_contents;

        $fbUrl = 'https://www.facebook.com/share/p/19xKmLpQ7B/';

        $response = $this->withSession(['grapari_authenticated' => true])
            ->post(route('contents.store'), [
                'grapari_id' => $grapari->id,
                'post_url' => $fbUrl,
            ]);

        $response->assertRedirect(route('dashboard', ['grapari' => $grapari->id]));

        $created = SocialContent::where('post_url', $fbUrl)->first();
        $this->assertNotNull($created);
        $this->assertSame('Promo IndiHome Paket JITU Manado', $created->title);
        $this->assertSame(142, $created->likes);
        $this->assertSame(29, $created->comments);
        $this->assertSame(18, $created->direct_chats);

        $grapari->refresh();
        $this->assertSame($initialContents + 1, $grapari->total_contents);
    }

    public function test_auto_sync_updates_content_and_grapari_metrics_when_facebook_counts_change(): void
    {
        $this->seed();

        $grapari = Grapari::firstOrFail();
        $fbUrl = 'https://www.facebook.com/share/p/19xKmLpQ7B/';

        $content = SocialContent::create([
            'grapari_id' => $grapari->id,
            'post_url' => $fbUrl,
            'title' => 'Promo IndiHome Paket JITU Manado',
            'campaign_tag' => 'Paket JITU',
            'banner_subtitle' => 'Internet Lebih Cepat Harga Lebih Hemat!',
            'platform' => 'Facebook Marketplace',
            'likes' => 100,
            'comments' => 20,
            'direct_chats' => 10,
            'status' => 'Published',
            'is_featured' => true,
            'published_at' => now(),
        ]);

        Http::fake([
            '*' => Http::response(
                '<html><head><meta property="og:title" content="Promo IndiHome Paket JITU Manado" /></head><body><script type="application/json">{"reaction_count":{"count":155},"total_comment_count":35,"share_count":{"count":24}}</script></body></html>',
                200
            ),
        ]);

        $response = $this->withSession(['grapari_authenticated' => true])
            ->postJson(route('contents.sync-all'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'updated_count' => 1,
            ]);

        $content->refresh();
        $this->assertSame(155, $content->likes);
        $this->assertSame(35, $content->comments);
        $this->assertSame(24, $content->direct_chats);
    }
}

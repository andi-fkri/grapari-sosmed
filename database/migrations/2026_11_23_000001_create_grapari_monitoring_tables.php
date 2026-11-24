<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graparis', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('rank')->index();
            $table->string('name');
            $table->string('short_name');
            $table->string('region')->default('Cluster Northern Manado');
            $table->string('address')->nullable();
            $table->unsignedInteger('total_contents')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedInteger('direct_chats')->default(0);
            $table->unsignedInteger('chart_likes')->default(0);
            $table->unsignedInteger('chart_comments')->default(0);
            $table->unsignedInteger('chart_direct_chats')->default(0);
            $table->decimal('engagement_rate', 5, 1)->default(0);
            $table->timestamps();
        });

        Schema::create('social_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grapari_id')->constrained('graparis')->cascadeOnDelete();
            $table->string('post_url')->nullable();
            $table->string('title');
            $table->string('campaign_tag')->default('Paket JITU');
            $table->string('banner_subtitle')->default('Internet Lebih Cepat Harga Lebih Hemat!');
            $table->string('platform')->default('Facebook Marketplace');
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedInteger('direct_chats')->default(0);
            $table->string('status')->default('Published');
            $table->boolean('is_featured')->default(false);
            $table->dateTime('published_at');
            $table->timestamps();
        });

        Schema::create('daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('metric_date');
            $table->string('label');
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedInteger('direct_chats')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_metrics');
        Schema::dropIfExists('social_contents');
        Schema::dropIfExists('graparis');
    }
};

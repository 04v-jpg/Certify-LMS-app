<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\MeetingPack;

use App\Enums\MeetingPackStatus;
use App\Models\MeetingPack;
use App\UseCases\MeetingPack\IndexAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_meeting_packs_paginated(): void
    {
        MeetingPack::factory()->count(15)->create();

        $paginator = app(IndexAction::class)([]);

        $this->assertEquals(10, $paginator->perPage());
        $this->assertEquals(15, $paginator->total());
        $this->assertCount(10, $paginator->items());
    }

    public function test_can_filter_by_keyword(): void
    {
        MeetingPack::factory()->create(['name' => 'プレミアム面談パック', 'sort_order' => 1]);
        MeetingPack::factory()->create(['name' => 'ライト面談パック', 'sort_order' => 2]);

        $paginator = app(IndexAction::class)(['keyword' => 'プレミアム']);

        $this->assertEquals(1, $paginator->total());
        $this->assertEquals('プレミアム面談パック', $paginator->items()[0]->name);
    }

    public function test_can_filter_by_status(): void
    {
        MeetingPack::factory()->create(['name' => '下書きパック', 'status' => MeetingPackStatus::Draft, 'sort_order' => 1]);
        MeetingPack::factory()->create(['name' => '公開中パック', 'status' => MeetingPackStatus::Published, 'sort_order' => 2]);

        $paginator = app(IndexAction::class)(['status' => MeetingPackStatus::Published->value]);

        $this->assertEquals(1, $paginator->total());
        $this->assertEquals('公開中パック', $paginator->items()[0]->name);
    }

    public function test_sorts_by_sort_order(): void
    {
        MeetingPack::factory()->create(['name' => '二番目', 'sort_order' => 2]);
        MeetingPack::factory()->create(['name' => '一番目', 'sort_order' => 1]);

        $paginator = app(IndexAction::class)([]);

        $this->assertEquals('一番目', $paginator->items()[0]->name);
        $this->assertEquals('二番目', $paginator->items()[1]->name);
    }
}

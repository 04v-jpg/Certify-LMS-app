<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\MeetingPack;

use App\Enums\MeetingPackStatus;
use App\Models\MeetingPack;
use App\Models\User;
use App\UseCases\MeetingPack\StoreAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_store_meeting_pack_with_authenticated_user(): void
    {
        // 準備: 管理者（作成者）としてログイン状態を作る
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $validatedData = [
            'name' => 'プレミアム面談パック',
            'description' => 'テスト用の面談パック説明文です',
            'meeting_count' => 5,
            'price' => 15000,
            'stripe_price_id' => 'price_12345',
            'sort_order' => 1,
        ];

        // 実行: StoreAction を呼び出し
        $meetingPack = app(StoreAction::class)($validatedData);

        // 検証: インスタンスが正しく返され、DBに保存されていること
        $this->assertInstanceOf(MeetingPack::class, $meetingPack);
        $this->assertSame('プレミアム面談パック', $meetingPack->name);
        $this->assertSame(5, $meetingPack->meeting_count);
        $this->assertSame(15000, $meetingPack->price);
        $this->assertSame($admin->id, $meetingPack->created_by_user_id);
        $this->assertSame($admin->id, $meetingPack->updated_by_user_id);

        $this->assertDatabaseHas('meeting_packs', [
            'id' => $meetingPack->id,
            'name' => 'プレミアム面談パック',
            'created_by_user_id' => $admin->id,
        ]);
    }
}

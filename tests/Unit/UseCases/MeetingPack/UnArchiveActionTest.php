<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\MeetingPack;

use App\Enums\MeetingPackStatus;
use App\Exceptions\MeetingPack\MeetingPackInvalidTransitionException;
use App\Models\MeetingPack;
use App\UseCases\MeetingPack\UnArchiveAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnArchiveActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_unarchive_archived_meeting_pack(): void
    {
        // 準備: アーカイブ済みの面談パックを作成
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Archived,
        ]);

        // 実行: UnArchiveAction を呼び出し
        $updatedPack = app(UnArchiveAction::class)($meetingPack);

        // 検証: ステータスが下書き(Draft)に戻っていることを検証
        // ※もし Published に戻る仕様なら、ここを MeetingPackStatus::Published に変更してください
        $this->assertSame(MeetingPackStatus::Draft, $updatedPack->status);
        
        $this->assertDatabaseHas('meeting_packs', [
            'id' => $meetingPack->id,
            'status' => MeetingPackStatus::Draft->value,
        ]);
    }

    public function test_throws_exception_when_unarchiving_draft_meeting_pack(): void
    {
        // 準備: 下書きの面談パックを作成
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Draft,
        ]);

        // 例外が発生することを期待
        $this->expectException(MeetingPackInvalidTransitionException::class);

        // 実行
        app(UnArchiveAction::class)($meetingPack);
    }

    public function test_throws_exception_when_unarchiving_published_meeting_pack(): void
    {
        // 準備: 公開中の面談パックを作成
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Published,
        ]);

        // 例外が発生することを期待
        $this->expectException(MeetingPackInvalidTransitionException::class);

        // 実行
        app(UnArchiveAction::class)($meetingPack);
    }
}
<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\MeetingPack;

use App\Enums\MeetingPackStatus;
use App\Exceptions\MeetingPack\MeetingPackInvalidTransitionException;
use App\Models\MeetingPack;
use App\UseCases\MeetingPack\ArchiveAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchiveActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_archive_published_meeting_pack(): void
    {
        // 準備: 公開中の面談パックを作成
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Published,
        ]);

        // 実行: ArchiveAction を呼び出し
        $updatedPack = app(ArchiveAction::class)($meetingPack);

        // 検証: ステータスがアーカイブに変更されていること
        $this->assertSame(MeetingPackStatus::Archived, $updatedPack->status);
        $this->assertDatabaseHas('meeting_packs', [
            'id' => $meetingPack->id,
            'status' => MeetingPackStatus::Archived->value,
        ]);
    }

    public function test_throws_exception_when_archiving_draft_meeting_pack(): void
    {
        // 準備: 下書きの面談パックを作成
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Draft,
        ]);

        // 例外がスローされることを期待
        $this->expectException(MeetingPackInvalidTransitionException::class);

        // 実行: 下書きをアーカイブしようとする
        app(ArchiveAction::class)($meetingPack);
    }

    public function test_throws_exception_when_archiving_already_archived_meeting_pack(): void
    {
        // 準備: すでにアーカイブ済みの面談パックを作成
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Archived,
        ]);

        // 例外がスローされることを期待
        $this->expectException(MeetingPackInvalidTransitionException::class);

        // 実行: アーカイブ済みを再度アーカイブしようとする
        app(ArchiveAction::class)($meetingPack);
    }
}
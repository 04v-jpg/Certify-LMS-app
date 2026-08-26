<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\MeetingPack;

use App\Enums\MeetingPackStatus;
use App\Exceptions\MeetingPack\MeetingPackNotDeletableException;
use App\Models\MeetingPack;
use App\UseCases\MeetingPack\DestroyAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_delete_draft_meeting_pack(): void
    {
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Draft,
        ]);

        app(DestroyAction::class)($meetingPack);

        // 物理削除されている（DBからレコードが消えている）ことを検証
        $this->assertModelMissing($meetingPack);
    }

    public function test_throws_exception_when_deleting_published_meeting_pack(): void
    {
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Published,
        ]);

        $this->expectException(MeetingPackNotDeletableException::class);

        app(DestroyAction::class)($meetingPack);

        // 削除されずにDBに残っていることを検証
        $this->assertModelExists($meetingPack);
    }

    public function test_can_delete_archived_meeting_pack(): void
    {
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Archived,
        ]);

        app(DestroyAction::class)($meetingPack);

        // 物理削除されていることを検証
        $this->assertModelMissing($meetingPack);
    }
}
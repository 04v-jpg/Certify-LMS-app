<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\MeetingPack;

use App\Enums\MeetingPackStatus;
use App\Exceptions\MeetingPack\MeetingPackInvalidTransitionException;
use App\Models\MeetingPack;
use App\UseCases\MeetingPack\PublishAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_publish_draft_meeting_pack(): void
    {
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Draft,
        ]);

        $updatedPack = app(PublishAction::class)($meetingPack);

        // ステータスが公開中になっていること、およびDBに反映されていることを検証
        $this->assertSame(MeetingPackStatus::Published, $updatedPack->status);
        $this->assertDatabaseHas('meeting_packs', [
            'id' => $meetingPack->id,
            'status' => MeetingPackStatus::Published->value,
        ]);
    }

    public function test_throws_exception_when_publishing_already_published_meeting_pack(): void
    {
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Published,
        ]);

        $this->expectException(MeetingPackInvalidTransitionException::class);

        app(PublishAction::class)($meetingPack);
    }

    public function test_throws_exception_when_publishing_archived_meeting_pack(): void
    {
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Archived,
        ]);

        $this->expectException(MeetingPackInvalidTransitionException::class);

        app(PublishAction::class)($meetingPack);
    }
}
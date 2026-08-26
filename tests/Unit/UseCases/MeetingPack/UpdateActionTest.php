<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\MeetingPack;

use App\Models\MeetingPack;
use App\Models\User;
use App\UseCases\MeetingPack\UpdateAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_meeting_pack_with_authenticated_user(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        // ファクトリーに任せるか、明示的に admin->id を渡す
        $meetingPack = MeetingPack::factory()->create([
            'name' => '旧パック名',
            'description' => '旧説明文',
        ]);

        $validatedData = [
            'name' => '新パック名',
            'description' => '更新後の説明文です',
        ];

        $updatedPack = app(UpdateAction::class)($meetingPack, $validatedData);

        $this->assertSame('新パック名', $updatedPack->name);
        $this->assertSame('更新後の説明文です', $updatedPack->description);
        $this->assertSame($admin->id, $updatedPack->updated_by_user_id);

        $this->assertDatabaseHas('meeting_packs', [
            'id' => $meetingPack->id,
            'name' => '新パック名',
            'description' => '更新後の説明文です',
            'updated_by_user_id' => $admin->id,
        ]);
    }
}
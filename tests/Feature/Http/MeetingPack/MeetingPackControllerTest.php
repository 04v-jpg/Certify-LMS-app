<?php

declare(strict_types=1);

namespace Tests\Feature\Http\MeetingPack;

use App\Enums\MeetingPackStatus;
use App\Models\MeetingPack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingPackControllerTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // 1. 一覧表示（Index）のテスト
    // ==========================================
    public function test_admin_can_view_meeting_pack_index(): void
    {
        $admin = User::factory()->admin()->create();
        MeetingPack::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.meeting-packs.index'));

        $response->assertStatus(200);
    }

    // ==========================================
    // 2. 新規作成（Store）のテスト
    // ==========================================
    public function test_admin_can_store_meeting_pack(): void
    {
        $admin = User::factory()->admin()->create();

        $data = [
            'name' => '新規面談パック',
            'description' => '説明文です',
            'meeting_count' => 3,
            'price' => 9000,
            'sort_order' => 1,
        ];

        $response = $this->actingAs($admin)->post(route('admin.meeting-packs.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('meeting_packs', [
            'name' => '新規面談パック',
        ]);
    }

    public function test_store_requires_mandatory_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.meeting-packs.store'), [
            'name' => '',
            'meeting_count' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'meeting_count']);
    }

    // ==========================================
    // 3. 更新（Update）のテスト
    // ==========================================
    public function test_admin_can_update_meeting_pack(): void
    {
        $admin = User::factory()->admin()->create();
        $meetingPack = MeetingPack::factory()->create([
            'name' => '変更前パック名',
        ]);

        $data = [
            'name' => '変更後パック名',
            'description' => '更新後の説明文',
            'meeting_count' => 5,
            'price' => 12000,
            'sort_order' => 10,
        ];

        $response = $this->actingAs($admin)->patch(route('admin.meeting-packs.update', $meetingPack), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('meeting_packs', [
            'id' => $meetingPack->id,
            'name' => '変更後パック名',
        ]);
    }

    // ==========================================
    // 4. 削除（Destroy）のテスト
    // ==========================================
    public function test_admin_can_destroy_meeting_pack(): void
    {
        $admin = User::factory()->admin()->create();
        $meetingPack = MeetingPack::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.meeting-packs.destroy', $meetingPack));

        $response->assertRedirect();
        $this->assertDatabaseMissing('meeting_packs', [
            'id' => $meetingPack->id,
        ]);
    }

    // ==========================================
    // 5. ステータス変更アクションのテスト
    // ==========================================
    public function test_admin_can_publish_meeting_pack(): void
    {
        $admin = User::factory()->admin()->create();
        $meetingPack = MeetingPack::factory()->draft()->create();

        $response = $this->actingAs($admin)->post(route('admin.meeting-packs.publish', $meetingPack));

        $response->assertRedirect();
        $this->assertDatabaseHas('meeting_packs', [
            'id' => $meetingPack->id,
            'status' => MeetingPackStatus::Published->value,
        ]);
    }

    public function test_admin_can_archive_meeting_pack(): void
    {
        $admin = User::factory()->admin()->create();
        $meetingPack = MeetingPack::factory()->published()->create();

        $response = $this->actingAs($admin)->post(route('admin.meeting-packs.archive', $meetingPack));

        $response->assertRedirect();
        $this->assertDatabaseHas('meeting_packs', [
            'id' => $meetingPack->id,
            'status' => MeetingPackStatus::Archived->value,
        ]);
    }

    public function test_admin_can_unarchive_meeting_pack(): void
    {
        $admin = User::factory()->admin()->create();
        $meetingPack = MeetingPack::factory()->create([
            'status' => MeetingPackStatus::Archived->value,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.meeting-packs.unarchive', $meetingPack));

        $response->assertRedirect();
        $this->assertDatabaseHas('meeting_packs', [
            'id' => $meetingPack->id,
            'status' => MeetingPackStatus::Draft->value,
        ]);
    }

    // ==========================================
    // 6. 認可（Policy / 権限）のテスト
    // ==========================================
    public function test_regular_user_cannot_store_meeting_pack(): void
    {
        $user = User::factory()->create(); // 通常ユーザー

        $data = [
            'name' => '不正な登録パック',
            'meeting_count' => 3,
            'price' => 5000,
        ];

        $response = $this->actingAs($user)->post(route('admin.meeting-packs.store'), $data);

        $response->assertForbidden();
    }
}
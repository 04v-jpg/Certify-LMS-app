<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\MeetingPack;
use App\Models\User;

/**
 * 面談パックマスタの認可ポリシー。
 *
 * - admin: 全面談パック配下を CRUD 可
 * - coach: 担当資格配下のみ CRUD 不可
 * 面談パックマスタの管理権限を制御する。
 */
class MeetingPackPolicy
{
    public function viewAny(User $auth): bool
    {
        return $this->canManage($auth);
    }

    public function view(User $auth, MeetingPack $meetingPack): bool
    {
        return $this->canManage($auth, $meetingPack);
    }

    public function create(User $auth): bool
    {
        return $this->canManage($auth);
    }

    public function store(User $auth,?MeetingPack $meetingPack = null): bool
    {
        return $this->canManage($auth,$meetingPack);
    }

    public function edit(User $auth, ?MeetingPack $meetingPack = null): bool
    {
        return $this->canManage($auth, $meetingPack);
    }

    public function update(User $auth, ?MeetingPack $meetingPack = null): bool
    {
        return $this->canManage($auth, $meetingPack);
    }

    public function delete(User $auth, ?MeetingPack $meetingPack = null): bool
    {
        return $this->canManage($auth, $meetingPack);
    }

    public function publish(User $auth, ?MeetingPack $meetingPack = null): bool
    {
        return $this->canManage($auth, $meetingPack);
    }

    public function archive(User $auth, ?MeetingPack $meetingPack = null): bool
    {
        return $this->canManage($auth, $meetingPack);
    }

    public function unarchive(User $auth, ?MeetingPack $meetingPack = null): bool
    {
        return $this->canManage($auth, $meetingPack);
    }

    private function canManage(User $auth, ?MeetingPack $meetingPack = null): bool
    {
        return match ($auth->role) {
            UserRole::Admin => true,
            UserRole::Coach => false,
            default => false,
        };
    }
}

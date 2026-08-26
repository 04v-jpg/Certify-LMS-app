<?php

declare(strict_types=1);

namespace App\UseCases\MeetingPack;

use App\Enums\MeetingPackStatus;
use App\Exceptions\MeetingPack\MeetingPackNotDeletableException;
use App\Models\MeetingPack;
use Illuminate\Support\Facades\DB;

/**
 * MeetingPack の SoftDelete ユースケース。Draft 状態のみ削除可、Published 状態は削除拒否(先に下書きへ戻す必要がある)。
 */
final class DestroyAction
{
    /**
     * @throws MeetingPackNotDeletableException
     */
    public function __invoke(MeetingPack $meetingPack): void
    {
        if ($meetingPack->status === MeetingPackStatus::Published) {
            throw MeetingPackNotDeletableException::forMeetingPack();
        }

        DB::transaction(fn () => $meetingPack->delete());
    }
}

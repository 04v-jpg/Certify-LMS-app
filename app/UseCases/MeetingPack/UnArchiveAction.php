<?php

declare(strict_types=1);

namespace App\UseCases\MeetingPack;

use App\Enums\MeetingPackStatus;
use App\Exceptions\MeetingPack\MeetingPackInvalidTransitionException;
use App\Models\MeetingPack;
use Illuminate\Support\Facades\DB;

/**
 * MeetingPack のアーカイブからの復元ユースケース。Archived → Draft の遷移のみ許可する。
 */
final class UnArchiveAction
{
    /**
     * @throws MeetingPackInvalidTransitionException
     */
    public function __invoke(MeetingPack $meetingPack): MeetingPack
    {
        if ($meetingPack->status !== MeetingPackStatus::Archived) {
            throw MeetingPackInvalidTransitionException::forMeetingPack(
                $meetingPack->status,
                MeetingPackStatus::Draft,
            );
        }

        return DB::transaction(function () use ($meetingPack) {
            $meetingPack->update(['status' => MeetingPackStatus::Draft->value]);

            return $meetingPack->fresh();
        });
    }
}

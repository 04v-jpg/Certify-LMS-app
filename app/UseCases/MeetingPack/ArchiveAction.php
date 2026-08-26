<?php

declare(strict_types=1);

namespace App\UseCases\MeetingPack;

use App\Enums\MeetingPackStatus;
use App\Exceptions\MeetingPack\MeetingPackInvalidTransitionException;
use App\Models\MeetingPack;
use Illuminate\Support\Facades\DB;

/**
 * MeetingPack の下書きへの巻き戻しユースケース。Published → Archived の遷移のみ許可する。
 */
final class ArchiveAction
{
    /**
     * @throws MeetingPackInvalidTransitionException
     */
    public function __invoke(MeetingPack $meetingPack): MeetingPack
    {
        if ($meetingPack->status !== MeetingPackStatus::Published) {
            throw MeetingPackInvalidTransitionException::forMeetingPack(
                $meetingPack->status,
                MeetingPackStatus::Archived,
            );
        }

        return DB::transaction(function () use ($meetingPack) {
            $meetingPack->update(['status' => MeetingPackStatus::Archived->value]);

            return $meetingPack->fresh();
        });
    }
}

<?php

declare(strict_types=1);

namespace App\UseCases\MeetingPack;

use App\Enums\MeetingPackStatus;
use App\Exceptions\MeetingPack\MeetingPackInvalidTransitionException;
use App\Models\MeetingPack;
use Illuminate\Support\Facades\DB;

/**
 * MeetingPack の公開ユースケース。Draft → Published の遷移のみ許可する。
 */
final class PublishAction
{
    /**
     * @throws MeetingPackInvalidTransitionException
     */
    public function __invoke(MeetingPack $meetingPack): MeetingPack
    {
        if ($meetingPack->status !== MeetingPackStatus::Draft) {
            throw MeetingPackInvalidTransitionException::forMeetingPack(
                $meetingPack->status,
                MeetingPackStatus::Published,
            );
        }

        return DB::transaction(function () use ($meetingPack) {
            $meetingPack->update([
                'status' => MeetingPackStatus::Published->value,
            ]);

            return $meetingPack->fresh();
        });
    }
}

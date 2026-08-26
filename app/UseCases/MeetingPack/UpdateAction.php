<?php

declare(strict_types=1);

namespace App\UseCases\MeetingPack;

use App\Models\MeetingPack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * MeetingPack の更新ユースケース。name / description を更新する(status は別 Action で遷移)。
 */
final class UpdateAction
{
    /**
     * @param array{name: string, description?: ?string} $validated MeetingPack/UpdateRequest::rules() で検証済
     */
    public function __invoke(MeetingPack $meetingPack, array $validated): MeetingPack
    {
        return DB::transaction(function () use ($meetingPack, $validated) {
            $userId = Auth::id();
            $meetingPack->update(array_merge($validated,
                [
                    'updated_by_user_id' => $userId
                ]
            ));

            return $meetingPack->fresh();
        });
    }
}

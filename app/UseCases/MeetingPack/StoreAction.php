<?php

declare(strict_types=1);

namespace App\UseCases\MeetingPack;

use App\Models\MeetingPack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * MeetingPack(面談パックマスタ) の新規作成ユースケース。指定資格配下に紐付けて INSERT する。
 */
final class StoreAction
{
    /**
     * @param array{name: string, description?: ?string, meeting_count: int, price: int, stripe_price_id?: ?string, sort_order?: ?int} $validated
     */
    public function __invoke(array $validated): MeetingPack
    {
        return DB::transaction(function ()use ($validated) {
            $userId = Auth::id();

            return MeetingPack::create(array_merge($validated,
            [
                'created_by_user_id' => $userId,
                'updated_by_user_id' => $userId,
            ]));
        });
    }
}

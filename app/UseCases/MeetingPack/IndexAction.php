<?php

declare(strict_types=1);

namespace App\UseCases\MeetingPack;

use App\Models\MeetingPack;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 面談パックマスタの一覧を返すユースケース。
 * フィルタリング条件として、キーワード検索と状態フィルタを受け付ける。
 */

class IndexAction
{
    public function __invoke(array $filters): LengthAwarePaginator
    {
        $query = MeetingPack::query();

        // キーワード検索があれば条件追加
        if (!empty($filters['keyword'])) {
            $query->where('name', 'like', '%' . $filters['keyword'] . '%');
        }

        // 状態フィルタがあれば条件追加
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('sort_order', 'asc')->paginate(10)->appends($filters);
    }
}
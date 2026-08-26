<?php

declare(strict_types=1);

namespace App\Exceptions\MeetingPack;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * 公開中(Published)の面談パックに対して削除操作が試行された場合に throw される。
 *
 * 利用先: MeetingPack の DestroyAction。
 * static ファクトリ(forMeetingPack)経由で呼出側からはメッセージ文字列を渡さない。
 */
final class MeetingPackNotDeletableException extends ConflictHttpException
{
    public static function forMeetingPack(): self
    {
        return self::build('面談パック');
    }

    private static function build(string $entityLabel): self
    {
        return new self(sprintf(
            '公開中の%sは削除できません。先に下書きに戻してから削除してください。',
            $entityLabel,
        ));
    }

    private function __construct(string $message)
    {
        parent::__construct($message);
    }
}

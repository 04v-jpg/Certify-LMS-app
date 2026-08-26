<?php

declare(strict_types=1);

namespace App\Exceptions\MeetingPack;

use App\Enums\MeetingPackStatus;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * 面談パックの公開状態遷移(Draft ⇄ Published)が許可されない条件下で試行された場合に throw される。
 *
 * 利用先: MeetingPack の PublishAction / UnpublishAction。
 * Entity ごとの static ファクトリ(forMeetingPack)を経由して呼出側からはメッセージ文字列を渡さない。
 */
final class MeetingPackInvalidTransitionException extends ConflictHttpException
{
    public static function forMeetingPack(MeetingPackStatus $from, MeetingPackStatus $to): self
    {
        return self::build('面談パック', $from, $to);
    }

    private static function build(string $entityLabel, MeetingPackStatus $from, MeetingPackStatus $to): self
    {
        return new self(sprintf(
            '%sの現在の状態(%s)から%sへの遷移は許可されていません。',
            $entityLabel,
            $from->label(),
            $to->label(),
        ));
    }

    private function __construct(string $message)
    {
        parent::__construct($message);
    }
}

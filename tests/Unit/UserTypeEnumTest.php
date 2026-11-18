<?php

namespace Tests\Unit;

use App\Enums\UserTypeEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/** Each user type manages its own rank and everything below it. */
class UserTypeEnumTest extends TestCase
{
    /**
     * @return array<string, array{UserTypeEnum, UserTypeEnum, bool}>
     */
    public static function hierarchy(): array
    {
        $allowed = [
            'super_admin' => ['super_admin', 'admin', 'volunteer', 'donor'],
            'admin' => ['admin', 'volunteer', 'donor'],
            'volunteer' => ['volunteer', 'donor'],
            'donor' => ['donor'],
        ];

        $cases = [];

        foreach (UserTypeEnum::cases() as $actor) {
            foreach (UserTypeEnum::cases() as $target) {
                $cases["{$actor->value} -> {$target->value}"] = [$actor, $target, in_array($target->value, $allowed[$actor->value], true)];
            }
        }

        return $cases;
    }

    #[Test]
    #[DataProvider('hierarchy')]
    public function a_type_manages_its_own_rank_and_below(UserTypeEnum $actor, UserTypeEnum $target, bool $expected): void
    {
        $this->assertSame($expected, $actor->canManage($target));
    }
}

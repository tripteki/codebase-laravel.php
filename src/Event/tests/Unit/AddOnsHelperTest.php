<?php

namespace Modules\Event\Tests\Unit;

use Modules\Event\App\Enums\AddOnEnum;
use Modules\Event\App\Models\Event;
use Modules\Event\App\Support\AddOnsHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AddOnsHelperTest extends TestCase
{
    /**
     * @return void
     */
    public function test_module_values_use_defaults_when_raw_is_null(): void
    {
        $event = new Event;
        $event->setAttribute("add_ons_modules", null);

        $this->assertSame(
            AddOnEnum::defaultModuleValues(),
            AddOnsHelper::moduleValues($event),
        );
    }

    /**
     * @param list<string> $raw
     * @param list<string> $expected
     * @return void
     */
    #[DataProvider("moduleValuesProvider")]
    public function test_module_values_do_not_merge_defaults_when_raw_is_set(
        array $raw,
        array $expected,
    ): void {
        $event = new Event;
        $event->setAttribute("add_ons_modules", $raw);

        $this->assertSame($expected, AddOnsHelper::moduleValues($event));
    }

    /**
     * @return array<string, array{0: list<string>, 1: list<string>}>
     */
    public static function moduleValuesProvider(): array
    {
        return [
            "user only" => [
                [AddOnEnum::MODULES_USER->value],
                [AddOnEnum::MODULES_USER->value],
            ],
            "notification only" => [
                [AddOnEnum::MODULES_NOTIFICATION->value],
                [AddOnEnum::MODULES_NOTIFICATION->value],
            ],
            "empty list" => [
                [],
                [],
            ],
        ];
    }

    /**
     * @return void
     */
    public function test_normalize_module_values_do_not_merge_defaults(): void
    {
        $this->assertSame(
            [AddOnEnum::MODULES_NOTIFICATION->value],
            AddOnsHelper::normalizeModuleValues([
                AddOnEnum::MODULES_NOTIFICATION->value,
            ]),
        );
    }
}

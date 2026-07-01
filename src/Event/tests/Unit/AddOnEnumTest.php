<?php

namespace Modules\Event\Tests\Unit;

use Modules\Event\App\Enums\AddOnEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AddOnEnumTest extends TestCase
{
    /**
     * @return void
     */
    public function test_all_modules_are_toggleable(): void
    {
        foreach (AddOnEnum::modules() as $case) {
            $this->assertTrue($case->isToggleable());
        }
    }

    /**
     * @return void
     */
    public function test_default_auth_features_are_toggleable(): void
    {
        foreach (AddOnEnum::defaultFeatureValues() as $value) {
            $case = AddOnEnum::from($value);

            $this->assertTrue($case->isToggleable());
        }
    }

    /**
     * @return void
     */
    public function test_toggleable_features_match_catalog(): void
    {
        $this->assertSame(AddOnEnum::featureValues(), AddOnEnum::toggleableFeatureValues());
    }

    /**
     * @return void
     */
    public function test_toggleable_modules_match_catalog(): void
    {
        $this->assertSame(AddOnEnum::moduleValues(), AddOnEnum::toggleableModuleValues());
    }

    /**
     * @return void
     */
    public function test_only_mailing_has_feature_configuration(): void
    {
        foreach (AddOnEnum::features() as $case) {
            $expected = $case === AddOnEnum::FEATURES_MAILING;

            $this->assertSame($expected, $case->hasFeatureConfiguration());
        }
    }

    /**
     * @param string $value
     * @param bool $isFeature
     * @param bool $isModule
     * @return void
     */
    #[DataProvider("caseClassificationProvider")]
    public function test_case_classification(string $value, bool $isFeature, bool $isModule): void
    {
        $case = AddOnEnum::from($value);

        $this->assertSame($isFeature, $case->isFeature());
        $this->assertSame($isModule, $case->isModule());
    }

    /**
     * @return array<string, array{0: string, 1: bool, 2: bool}>
     */
    public static function caseClassificationProvider(): array
    {
        return [
            "login" => [AddOnEnum::FEATURES_AUTH_LOGIN->value, true, false],
            "registration" => [AddOnEnum::FEATURES_AUTH_REGISTRATION->value, true, false],
            "passwordless" => [AddOnEnum::FEATURES_AUTH_PASSWORDLESS->value, true, false],
            "multi language" => [AddOnEnum::FEATURES_MULTI_LANGUAGE->value, true, false],
            "import" => [AddOnEnum::FEATURES_IMPORT->value, true, false],
            "export" => [AddOnEnum::FEATURES_EXPORT->value, true, false],
            "mailing" => [AddOnEnum::FEATURES_MAILING->value, true, false],
            "user module" => [AddOnEnum::MODULES_USER->value, false, true],
            "acl module" => [AddOnEnum::MODULES_ACL->value, false, true],
            "log module" => [AddOnEnum::MODULES_LOG->value, false, true],
            "notification module" => [AddOnEnum::MODULES_NOTIFICATION->value, false, true],
        ];
    }
}

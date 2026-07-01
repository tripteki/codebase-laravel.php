<?php

namespace Modules\Event\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\Event\App\Enums\AddOnEnum;
use Tests\TestCase;

class EventAddOnGatingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected User $user;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutAdminApiMiddleware();
        $this->enablePermissionTeams();

        $this->artisan("db:seed", ["--class" => AclSeeder::class]);
    }

    /**
     * @return void
     */
    public function test_get_v1_add_ons_variables(): void
    {
        $this->createTenantEvent("gated-event", [
            "title" => "Gated Event",
            "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
            "add_ons_features" => [AddOnEnum::FEATURES_EXPORT->value],
        ]);

        $this->getJson("/api/v1/gated-event/add-ons/variables")
            ->assertStatus(200)
            ->assertJsonPath("data.modules", [
                AddOnEnum::MODULES_USER->value,
            ])
            ->assertJsonPath("data.features", [
                AddOnEnum::FEATURES_EXPORT->value,
            ])
            ->assertJsonStructure([
                "data" => [
                    "features",
                    "modules",
                    "branding" => [
                        "title",
                        "icon_url",
                        "favicon_ico_url",
                        "favicon_png_url",
                        "brand_light_url",
                        "brand_dark_url",
                    ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function test_post_v1_auth_login_returns_403_when_login_disabled(): void
    {
        $this->tenantId = "no-login-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Login Event",
            "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
            "add_ons_features" => [AddOnEnum::FEATURES_EXPORT->value],
        ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->assignTenantRole($this->user, RoleEnum::ADMIN->value, $this->tenantId);

        $this->postJson("/api/v1/auth/login", [
            "identifierKey" => "email",
            "identifierValue" => $this->user->email,
            "password" => "Password123!",
            "tenant" => $this->tenantId,
        ])->assertStatus(403);
    }

    /**
     * @return void
     */
    public function test_post_v1_auth_register_returns_403_when_registration_disabled(): void
    {
        $this->tenantId = "no-register-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Register Event",
            "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
            "add_ons_features" => [AddOnEnum::FEATURES_EXPORT->value],
        ]);

        $this->postJson("/api/v1/auth/register", [
            "name" => "guestuser",
            "full_name" => "Guest User",
            "email" => "guest-no-register@example.com",
            "password" => "Password123!",
            "password_confirmation" => "Password123!",
            "tenant" => $this->tenantId,
        ])->assertStatus(403);
    }

    /**
     * @return void
     */
    public function test_post_admin_users_import_returns_403_when_import_disabled(): void
    {
        $this->tenantId = "no-import-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Import Event",
            "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
            "add_ons_features" => $this->tenantAuthFeatures([
                AddOnEnum::FEATURES_EXPORT->value,
            ]),
        ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->assignTenantRole($this->user, RoleEnum::ADMIN->value, $this->tenantId);

        $this->authPost(
            $this->adminApi("/users/import"),
            $this->loginToken($this->user, "Password123!"),
        )->assertStatus(403);
    }

    /**
     * @return void
     */
    public function test_post_admin_users_export_returns_403_when_export_disabled(): void
    {
        $this->tenantId = "no-export-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Export Event",
            "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
            "add_ons_features" => $this->tenantAuthFeatures([
                AddOnEnum::FEATURES_IMPORT->value,
            ]),
        ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->assignTenantRole($this->user, RoleEnum::ADMIN->value, $this->tenantId);

        $this->authPost(
            $this->adminApi("/users/export"),
            $this->loginToken($this->user, "Password123!"),
        )->assertStatus(403);
    }

    /**
     * @return void
     */
    public function test_post_admin_roles_import_returns_403_when_import_disabled(): void
    {
        $this->tenantId = "no-role-import-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Role Import Event",
            "add_ons_modules" => [
                AddOnEnum::MODULES_USER->value,
                AddOnEnum::MODULES_ACL->value,
            ],
            "add_ons_features" => $this->tenantAuthFeatures([
                AddOnEnum::FEATURES_EXPORT->value,
            ]),
        ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->assignTenantRole($this->user, RoleEnum::ADMIN->value, $this->tenantId);

        $this->authPost(
            $this->adminApi("/roles/import"),
            $this->loginToken($this->user, "Password123!"),
        )->assertStatus(403);
    }

    /**
     * @return void
     */
    public function test_post_admin_roles_export_returns_403_when_export_disabled(): void
    {
        $this->tenantId = "no-role-export-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Role Export Event",
            "add_ons_modules" => [
                AddOnEnum::MODULES_USER->value,
                AddOnEnum::MODULES_ACL->value,
            ],
            "add_ons_features" => $this->tenantAuthFeatures([
                AddOnEnum::FEATURES_IMPORT->value,
            ]),
        ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->assignTenantRole($this->user, RoleEnum::ADMIN->value, $this->tenantId);

        $this->authPost(
            $this->adminApi("/roles/export"),
            $this->loginToken($this->user, "Password123!"),
        )->assertStatus(403);
    }

    /**
     * @return void
     */
    public function test_get_tenant_settings_variables_returns_theme_variables(): void
    {
        $this->createTenantEvent("tenant-theme-event", [
            "title" => "Tenant Theme Event",
            "add_ons_features" => [AddOnEnum::FEATURES_EXPORT->value],
        ]);

        $this->getJson("/api/v1/tenant-theme-event/settings/variables")
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    "COLOR_PRIMARY",
                    "COLOR_SECONDARY",
                    "COLOR_TERTIARY",
                    "EMAIL_SERVER",
                ],
            ]);
    }

    /**
     * @return void
     */
    public function test_get_v1_notifications_returns_403_when_notification_module_disabled(): void
    {
        $this->tenantId = "no-notification-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Notification Event",
            "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
            "add_ons_features" => $this->tenantAuthFeatures([
                AddOnEnum::FEATURES_EXPORT->value,
            ]),
        ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->assignTenantRole($this->user, RoleEnum::ADMIN->value, $this->tenantId);

        $this->authGet(
            "/api/v1/notifications",
            $this->loginToken($this->user, "Password123!"),
        )->assertStatus(403);
    }

    /**
     * @return void
     */
    public function test_post_v1_auth_register_auto_verifies_when_mailing_disabled(): void
    {
        Mail::fake();

        $this->tenantId = "no-mailing-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Mailing Event",
            "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
            "add_ons_features" => [
                AddOnEnum::FEATURES_AUTH_REGISTRATION->value,
                AddOnEnum::FEATURES_EXPORT->value,
            ],
        ]);

        $response = $this->postJson("/api/v1/auth/register", [
            "name" => "guestuser",
            "full_name" => "Guest User",
            "email" => "guest@example.com",
            "password" => "Password123!",
            "password_confirmation" => "Password123!",
            "tenant" => $this->tenantId,
        ]);

        $response->assertStatus(201);

        $user = User::query()->where("email", "guest@example.com")->first();

        $this->assertNotNull($user);
        $this->assertNotNull($user->email_verified_at);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function test_post_v1_auth_email_verification_notification_returns_403_when_mailing_disabled(): void
    {
        Mail::fake();

        $this->tenantId = "no-mailing-verify-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Mailing Verify Event",
            "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
            "add_ons_features" => $this->tenantAuthFeatures([
                AddOnEnum::FEATURES_EXPORT->value,
            ]),
        ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => null,
            "tenant_id" => $this->tenantId,
        ]);

        $this->assignTenantRole($this->user, RoleEnum::ADMIN->value, $this->tenantId);

        $this->authPost(
            "/api/v1/auth/email/verification-notification",
            $this->loginToken($this->user, "Password123!"),
        )->assertStatus(403);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function test_post_v1_auth_forgot_password_returns_403_when_mailing_disabled(): void
    {
        Mail::fake();

        $this->tenantId = "no-mailing-reset-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Mailing Reset Event",
            "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
            "add_ons_features" => $this->tenantAuthFeatures([
                AddOnEnum::FEATURES_EXPORT->value,
            ]),
        ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->postJson("/api/v1/auth/forgot-password", [
            "email" => $this->user->email,
            "tenant" => $this->tenantId,
        ])->assertStatus(403);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function test_post_v1_auth_forgot_password_returns_403_when_passwordless(): void
    {
        Mail::fake();

        $this->tenantId = "passwordless-reset-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "Passwordless Reset Event",
            "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
            "add_ons_features" => [
                AddOnEnum::FEATURES_AUTH_PASSWORDLESS->value,
                AddOnEnum::FEATURES_MAILING->value,
            ],
        ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->postJson("/api/v1/auth/forgot-password", [
            "email" => $this->user->email,
            "tenant" => $this->tenantId,
        ])->assertStatus(403);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function test_post_admin_permissions_import_returns_403_when_import_disabled(): void
    {
        $this->tenantId = "no-permission-import-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Permission Import Event",
            "add_ons_modules" => [
                AddOnEnum::MODULES_USER->value,
                AddOnEnum::MODULES_ACL->value,
            ],
            "add_ons_features" => $this->tenantAuthFeatures([
                AddOnEnum::FEATURES_EXPORT->value,
            ]),
        ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->assignTenantRole($this->user, RoleEnum::ADMIN->value, $this->tenantId);

        $this->authPost(
            $this->adminApi("/permissions/import"),
            $this->loginToken($this->user, "Password123!"),
        )->assertStatus(403);
    }

    /**
     * @return void
     */
    public function test_post_admin_permissions_export_returns_403_when_export_disabled(): void
    {
        $this->tenantId = "no-permission-export-event";
        $this->createTenantEvent($this->tenantId, [
            "title" => "No Permission Export Event",
            "add_ons_modules" => [
                AddOnEnum::MODULES_USER->value,
                AddOnEnum::MODULES_ACL->value,
            ],
            "add_ons_features" => $this->tenantAuthFeatures([
                AddOnEnum::FEATURES_IMPORT->value,
            ]),
        ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->assignTenantRole($this->user, RoleEnum::ADMIN->value, $this->tenantId);

        $this->authPost(
            $this->adminApi("/permissions/export"),
            $this->loginToken($this->user, "Password123!"),
        )->assertStatus(403);
    }

    /**
     * @return void
     */
    public function test_tenant_api_uses_fallback_locale_when_multi_language_disabled(): void
    {
        $this->createTenantEvent("mono-lang-event", [
            "title" => "Mono Lang Event",
            "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
            "add_ons_features" => [AddOnEnum::FEATURES_EXPORT->value],
        ]);

        $this->withHeader("Accept-Language", "id")
            ->getJson("/api/v1/mono-lang-event/add-ons/variables")
            ->assertStatus(200);

        $this->assertSame(config("app.fallback_locale"), app()->getLocale());
    }

    /**
     * @param list<string> $features
     * @return list<string>
     */
    private function tenantAuthFeatures(array $features = []): array
    {
        return array_values(array_unique(array_merge([
            AddOnEnum::FEATURES_AUTH_LOGIN->value,
            AddOnEnum::FEATURES_AUTH_REGISTRATION->value,
        ], $features)));
    }
}

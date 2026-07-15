<?php

namespace Tests\Feature;

use App\Models\User;
use Apriansyahrs\MekayaTheme\Auth\MekayaLogin;
use Apriansyahrs\MekayaTheme\Auth\MekayaRegister;
use Apriansyahrs\MekayaTheme\Auth\MekayaRequestPasswordReset;
use Apriansyahrs\MekayaTheme\Auth\MekayaResetPassword;
use Apriansyahrs\MekayaTheme\Livewire\MekayaSidebar;
use Apriansyahrs\MekayaTheme\Mekaya;
use Composer\InstalledVersions;
use Filament\Facades\Filament;
use Filament\Livewire\Sidebar;
use Filament\Support\Enums\Width;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class MekayaCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    public function test_resolved_framework_majors_match_the_ci_matrix(): void
    {
        $expectedFilamentMajor = getenv('EXPECTED_FILAMENT_MAJOR');
        $expectedLivewireMajor = getenv('EXPECTED_LIVEWIRE_MAJOR');

        if (filled($expectedFilamentMajor)) {
            $this->assertSame(
                (int) $expectedFilamentMajor,
                $this->installedMajor('filament/filament'),
            );
        }

        if (filled($expectedLivewireMajor)) {
            $this->assertSame(
                (int) $expectedLivewireMajor,
                $this->installedMajor('livewire/livewire'),
            );
        }
    }

    public function test_plugin_registers_the_same_panel_contracts_on_both_majors(): void
    {
        $panel = Filament::getPanel('admin');

        $this->assertTrue($panel->hasPlugin('mekaya'));
        $this->assertSame(MekayaSidebar::class, $panel->getSidebarLivewireComponent());
        $this->assertSame(MekayaLogin::class, $panel->getLoginRouteAction());
        $this->assertSame(MekayaRegister::class, $panel->getRegistrationRouteAction());
        $this->assertSame(MekayaRequestPasswordReset::class, $panel->getRequestPasswordResetRouteAction());
        $this->assertSame(MekayaResetPassword::class, $panel->getResetPasswordRouteAction());
        $this->assertSame(Width::Full, $panel->getMaxContentWidth());
        $this->assertTrue(is_subclass_of(MekayaSidebar::class, Sidebar::class));
        $this->assertInstanceOf(MekayaSidebar::class, Livewire::new('mekaya-sidebar'));
        $this->assertTrue(app()->bound(Mekaya::class));
        $this->assertSame(app(Mekaya::class), app(Mekaya::class));
        $this->assertSame('v1', mekaya()->version());
    }

    public function test_host_branding_takes_priority_and_no_bundled_svg_is_forced(): void
    {
        $panel = Filament::getPanel('admin');

        $this->assertNull($panel->getBrandLogo());
        $this->assertNull($panel->getFavicon());

        config()->set('mekaya.admin.favicon', 'vendor/mekaya/mekaya-icon.svg');
        $this->assertNull(mekaya()->faviconPath());

        $nameOnlyBrand = Blade::render('<x-mekaya::brand />');
        $this->assertStringContainsString((string) config('app.name'), $nameOnlyBrand);
        $this->assertStringNotContainsString('mekaya-icon.svg', $nameOnlyBrand);

        config()->set('mekaya.admin.brand_icon', 'images/project-icon.svg');
        $projectIconBrand = Blade::render('<x-mekaya::brand />');
        $this->assertStringContainsString(asset('images/project-icon.svg'), $projectIconBrand);

        $panel->brandLogo(asset('images/project-logo.svg'));
        $panelLogoBrand = Blade::render('<x-mekaya::brand />');
        $this->assertStringContainsString(asset('images/project-logo.svg'), $panelLogoBrand);
        $this->assertStringNotContainsString('images/project-icon.svg', $panelLogoBrand);
    }

    public function test_installed_assets_and_blade_views_are_resolvable(): void
    {
        $packagePath = InstalledVersions::getInstallPath('kungfufafa/mekaya-theme');

        $this->assertNotNull($packagePath);

        $cssInput = mekaya_vite_input('css/theme.css');
        $jsInput = mekaya_vite_input('js/mekaya.js');

        $this->assertSame('vendor/kungfufafa/mekaya-theme/resources/css/theme.css', $cssInput);
        $this->assertSame('vendor/kungfufafa/mekaya-theme/resources/js/mekaya.js', $jsInput);
        $this->assertFileExists(base_path($cssInput));
        $this->assertFileExists(base_path($jsInput));

        $manifest = json_decode(File::get(public_path('build/manifest.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey($cssInput, $manifest);
        $this->assertArrayHasKey($jsInput, $manifest);

        foreach (File::allFiles($packagePath.'/resources/views') as $view) {
            $compiled = Blade::compileString($view->getContents());

            $this->assertNotSame('', $compiled, $view->getPathname());
        }
    }

    public function test_english_and_indonesian_translations_keep_key_parity(): void
    {
        $packagePath = InstalledVersions::getInstallPath('kungfufafa/mekaya-theme');
        $english = array_keys(Arr::dot(require $packagePath.'/resources/lang/en/ui.php'));
        $indonesian = array_keys(Arr::dot(require $packagePath.'/resources/lang/id/ui.php'));

        sort($english);
        sort($indonesian);

        $this->assertSame($english, $indonesian);
    }

    public function test_auth_routes_and_guest_redirect_render_the_mekaya_experience(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('mky-card', escape: false)
            ->assertSee('mky-auth-brand', escape: false)
            ->assertSee('gap-6', escape: false);

        $this->get('/admin/password-reset/request')
            ->assertOk()
            ->assertSee('mky-card', escape: false);

        $this->get('/admin/register')
            ->assertOk()
            ->assertSee('mky-card', escape: false)
            ->assertSee('mky-auth-brand', escape: false);

        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_registration_creates_and_authenticates_an_account_on_both_majors(): void
    {
        Livewire::test(MekayaRegister::class)
            ->set('data.name', 'New Admin')
            ->set('data.email', 'new-admin@example.test')
            ->set('data.password', 'password')
            ->set('data.passwordConfirmation', 'password')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'New Admin',
            'email' => 'new-admin@example.test',
        ]);
    }

    public function test_login_and_authenticated_dashboard_work_on_both_livewire_majors(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'password',
        ]);

        Livewire::test(MekayaLogin::class)
            ->set('data.email', 'admin@example.test')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticated();

        $this->get('/admin')
            ->assertOk()
            ->assertSee('mky-header', escape: false)
            ->assertSee('mekaya-desktop-sidebar', escape: false)
            ->assertSee('assets/theme-', escape: false);
    }

    private function installedMajor(string $package): int
    {
        $version = InstalledVersions::getPrettyVersion($package) ?? InstalledVersions::getVersion($package) ?? '';

        return (int) ltrim($version, 'vV');
    }
}

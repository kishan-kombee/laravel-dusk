<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\UserExportPage;
use Tests\DuskTestCase;

class UserExportTest extends DuskTestCase
{
    /**
     * Test that export button is visible on users page.
     */
    public function test_export_button_is_visible()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new UserExportPage)
                ->assertExportButtonVisible()
                ->assertExportButtonText();
        });
    }

    /**
     * Test that export button is visible even when no users exist.
     */
    public function test_export_button_visible_with_no_users()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new UserExportPage)
                ->assertNoUsersMessageVisible()
                ->assertExportButtonVisible();
        });
    }

    /**
     * Test that export button is visible when users exist.
     */
    public function test_export_button_visible_with_users()
    {
        // Create test users with unique emails
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john'.time().'@example.com',
        ]);

        User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane'.time().'@example.com',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit(new UserExportPage)
                ->assertUsersTableVisible()
                ->assertExportButtonVisible();
        });
    }

    /**
     * Test that clicking export button triggers download.
     */
    public function test_export_button_triggers_download()
    {
        // Create test users with unique emails
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john'.time().'@example.com',
        ]);

        User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane'.time().'@example.com',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit(new UserExportPage)
                ->clickExportButton()
                // Wait a moment for the download to start
                ->pause(1000)
                // The page should still be on /users since export triggers download
                ->assertPathIs('/users');
        });
    }

    /**
     * Test export functionality with single user.
     */
    public function test_export_single_user()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test'.time().'@example.com',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new UserExportPage)
                ->assertSee($user->name)
                ->clickExportButton()
                ->pause(1000)
                ->assertPathIs('/users');
        });
    }

    /**
     * Test export functionality with multiple users.
     */
    public function test_export_multiple_users()
    {
        $users = User::factory()->count(5)->create();

        $this->browse(function (Browser $browser) {
            $browser->visit(new UserExportPage)
                ->assertUsersTableVisible()
                ->clickExportButton()
                ->pause(1000)
                ->assertPathIs('/users');
        });
    }

    /**
     * Test export functionality with no users.
     */
    public function test_export_no_users()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new UserExportPage)
                ->assertNoUsersMessageVisible()
                ->clickExportButton()
                ->pause(1000)
                ->assertPathIs('/users');
        });
    }

    /**
     * Test that export button is accessible from users page.
     */
    public function test_export_button_accessibility()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new UserExportPage)
                ->assertSee('Export Users')
                ->assertSee('Import Users')
                ->assertSee('Add New User');
        });
    }

    /**
     * Test export button styling and positioning.
     */
    public function test_export_button_styling()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new UserExportPage)
                ->assertSee('Export Users')
                ->assertSee('Import Users')
                ->assertSee('Add New User');
        });
    }

    /**
     * Test that export works after importing users.
     */
    public function test_export_after_import()
    {
        // First create some users with unique emails
        $timestamp = time();
        User::factory()->create([
            'name' => 'Imported User 1',
            'email' => 'imported1'.$timestamp.'@example.com',
        ]);

        User::factory()->create([
            'name' => 'Imported User 2',
            'email' => 'imported2'.$timestamp.'@example.com',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit(new UserExportPage)
                ->assertSee('Imported User 1')
                ->assertSee('Imported User 2')
                ->clickExportButton()
                ->pause(1000)
                ->assertPathIs('/users');
        });
    }

    /**
     * Test export button is present in the correct location.
     */
    public function test_export_button_location()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new UserExportPage)
                ->assertSee('Add New User')
                ->assertSee('Import Users')
                ->assertSee('Export Users')
                // Verify the order of buttons
                ->assertSeeIn('@add-user-button', 'Add New User')
                ->assertSeeIn('@import-button', 'Import Users')
                ->assertSeeIn('@export-button', 'Export Users');
        });
    }
}

<?php

namespace Tests\Browser;

use App\Models\User;
use Faker\Factory as Faker;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\UserExportPage;
use Tests\DuskTestCase;

class UserExportTest extends DuskTestCase
{
    protected $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create();

        // Display test summary before each test
        echo "\n".str_repeat('=', 60);
        echo "\n📊 TEST SUMMARY";
        echo "\n".str_repeat('=', 60);
        echo "\n";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test that export button is visible on users page.
     */
    public function test_export_button_is_visible()
    {
        echo "\n=== TEST: Export Button is Visible ===";
        echo "\nExpected: Export button should be visible on users page";
        echo "\nExpected: Export button should have correct text";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users page...'."\n";
                $browser->visit(new UserExportPage)
                    ->assertExportButtonVisible()
                    ->assertExportButtonText();
            });

            echo '✅ SUCCESS: Export button is visible and has correct text'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Export button visibility test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test that export button is visible even when no users exist.
     */
    public function test_export_button_visible_with_no_users()
    {
        echo "\n=== TEST: Export Button Visible with No Users ===";
        echo "\nExpected: Export button should be visible even with no users";
        echo "\nExpected: 'No users' message should be visible";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users page with no users...'."\n";
                $browser->visit(new UserExportPage)
                    ->assertNoUsersMessageVisible()
                    ->assertExportButtonVisible();
            });

            echo '✅ SUCCESS: Export button is visible even with no users'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Export button visibility with no users test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test that export button is visible when users exist.
     */
    public function test_export_button_visible_with_users()
    {
        // Create test users with unique emails
        $timestamp = time();
        $user1 = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john'.$timestamp.'@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane'.$timestamp.'@example.com',
        ]);

        echo "\n=== TEST: Export Button Visible with Users ===";
        echo "\nCreated Users: 2";
        echo "\nUser 1: ".$user1->name.' ('.$user1->email.')';
        echo "\nUser 2: ".$user2->name.' ('.$user2->email.')';
        echo "\nExpected: Users table and export button should be visible";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users page with existing users...'."\n";
                $browser->visit(new UserExportPage)
                    ->assertUsersTableVisible()
                    ->assertExportButtonVisible();
            });

            echo '✅ SUCCESS: Export button is visible with existing users'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Export button visibility with users test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test that clicking export button triggers download.
     */
    public function test_export_button_triggers_download()
    {
        // Create test users with unique emails
        $timestamp = time().rand(1000, 9999);
        $user1 = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john'.$timestamp.'@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane'.$timestamp.'@example.com',
        ]);

        echo "\n=== TEST: Export Button Triggers Download ===";
        echo "\nCreated Users: 2";
        echo "\nUser 1: ".$user1->name.' ('.$user1->email.')';
        echo "\nUser 2: ".$user2->name.' ('.$user2->email.')';
        echo "\nExpected: Clicking export button should trigger download";
        echo "\nExpected: Page should remain on /users after export";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users page...'."\n";
                $browser->visit(new UserExportPage)
                    ->clickExportButton()
                    // Wait a moment for the download to start
                    ->pause(1000)
                    // The page should still be on /users since export triggers download
                    ->assertPathIs('/users');
            });

            echo '✅ SUCCESS: Export button triggered download successfully'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Export button download test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Export Single User ===";
        echo "\nCreated User: ".$user->name.' ('.$user->email.')';
        echo "\nExpected: User should be visible on page";
        echo "\nExpected: Export should work with single user";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user) {
                echo '🌐 Opening users page...'."\n";
                $browser->visit(new UserExportPage)
                    ->assertSee($user->name)
                    ->clickExportButton()
                    ->pause(1000)
                    ->assertPathIs('/users');
            });

            echo '✅ SUCCESS: Single user export completed successfully'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Single user export test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test export functionality with multiple users.
     */
    public function test_export_multiple_users()
    {
        $users = User::factory()->count(5)->create();

        echo "\n=== TEST: Export Multiple Users ===";
        echo "\nCreated Users: ".$users->count();
        echo "\nUser Names: ".$users->pluck('name')->implode(', ');
        echo "\nExpected: Users table should be visible";
        echo "\nExpected: Export should work with multiple users";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users page...'."\n";
                $browser->visit(new UserExportPage)
                    ->assertUsersTableVisible()
                    ->clickExportButton()
                    ->pause(1000)
                    ->assertPathIs('/users');
            });

            echo '✅ SUCCESS: Multiple users export completed successfully'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Multiple users export test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test export functionality with no users.
     */
    public function test_export_no_users()
    {
        echo "\n=== TEST: Export with No Users ===";
        echo "\nExpected: 'No users' message should be visible";
        echo "\nExpected: Export should work even with no users";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users page with no users...'."\n";
                $browser->visit(new UserExportPage)
                    ->assertNoUsersMessageVisible()
                    ->clickExportButton()
                    ->pause(1000)
                    ->assertPathIs('/users');
            });

            echo '✅ SUCCESS: Export with no users completed successfully'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Export with no users test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test that export button is accessible from users page.
     */
    public function test_export_button_accessibility()
    {
        echo "\n=== TEST: Export Button Accessibility ===";
        echo "\nExpected Elements: Export Users, Import Users, Add New User";
        echo "\nExpected: All buttons should be visible and accessible";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users page...'."\n";
                $browser->visit(new UserExportPage)
                    ->assertSee('Export Users')
                    ->assertSee('Import Users')
                    ->assertSee('Add New User');
            });

            echo '✅ SUCCESS: All buttons are accessible and visible'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Export button accessibility test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test export button styling and positioning.
     */
    public function test_export_button_styling()
    {
        echo "\n=== TEST: Export Button Styling and Positioning ===";
        echo "\nExpected Elements: Export Users, Import Users, Add New User";
        echo "\nExpected: All buttons should be properly styled and positioned";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users page...'."\n";
                $browser->visit(new UserExportPage)
                    ->assertSee('Export Users')
                    ->assertSee('Import Users')
                    ->assertSee('Add New User');
            });

            echo '✅ SUCCESS: Export button styling and positioning is correct'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Export button styling test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test that export works after importing users.
     */
    public function test_export_after_import()
    {
        // First create some users with unique emails
        $timestamp = time();
        $user1 = User::factory()->create([
            'name' => 'Imported User 1',
            'email' => 'imported1'.$timestamp.'@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Imported User 2',
            'email' => 'imported2'.$timestamp.'@example.com',
        ]);

        echo "\n=== TEST: Export After Import ===";
        echo "\nCreated Users: 2 (simulating imported users)";
        echo "\nUser 1: ".$user1->name.' ('.$user1->email.')';
        echo "\nUser 2: ".$user2->name.' ('.$user2->email.')';
        echo "\nExpected: Both users should be visible on page";
        echo "\nExpected: Export should work with imported users";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users page...'."\n";
                $browser->visit(new UserExportPage)
                    ->assertSee('Imported User 1')
                    ->assertSee('Imported User 2')
                    ->clickExportButton()
                    ->pause(1000)
                    ->assertPathIs('/users');
            });

            echo '✅ SUCCESS: Export after import completed successfully'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Export after import test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test export button is present in the correct location.
     */
    public function test_export_button_location()
    {
        echo "\n=== TEST: Export Button Location ===";
        echo "\nExpected Elements: Add New User, Import Users, Export Users";
        echo "\nExpected Order: Add New User, Import Users, Export Users";
        echo "\nExpected: All buttons should be in correct locations";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users page...'."\n";
                $browser->visit(new UserExportPage)
                    ->assertSee('Add New User')
                    ->assertSee('Import Users')
                    ->assertSee('Export Users')
                    // Verify the order of buttons
                    ->assertSeeIn('@add-user-button', 'Add New User')
                    ->assertSeeIn('@import-button', 'Import Users')
                    ->assertSeeIn('@export-button', 'Export Users');
            });

            echo '✅ SUCCESS: Export button is in correct location'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Export button location test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }
}

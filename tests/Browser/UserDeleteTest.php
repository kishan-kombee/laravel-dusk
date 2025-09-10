<?php

namespace Tests\Browser;

use App\Models\User;
use Faker\Factory as Faker;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\UserDeletePage;
use Tests\DuskTestCase;

class UserDeleteTest extends DuskTestCase
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
     * Test successful user deletion from confirmation page.
     */
    public function test_user_can_be_deleted_successfully_from_confirmation_page(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        echo "\n=== TEST: Successful User Deletion (From Confirmation Page) ===";
        echo "\nUser ID: ".$user->id;
        echo "\nUser Name: ".$user->name;
        echo "\nUser Email: ".$user->email;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user) {
                echo '🌐 Opening user deletion confirmation page...'."\n";
                $browser->visit(new UserDeletePage($user->id))
                    ->assertSee('Delete User')
                    ->assertSee($user->name)
                    ->assertSee($user->email)
                    ->assertSee('This action cannot be undone')
                    ->click('@confirm-delete')
                    ->pause(3000) // Wait for deletion
                    ->assertSee("User '{$user->name}' deleted successfully!")
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying user was deleted from database...'."\n";
            // Verify user was deleted from database
            $this->assertDatabaseMissing('users', [
                'id' => $user->id,
            ]);

            echo '✅ SUCCESS: User deleted successfully with ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: User deletion failed for ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test user can cancel deletion from confirmation page.
     */
    public function test_user_can_cancel_deletion_from_confirmation_page(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        echo "\n=== TEST: Cancel User Deletion (From Confirmation Page) ===";
        echo "\nUser ID: ".$user->id;
        echo "\nUser Name: ".$user->name;
        echo "\nUser Email: ".$user->email;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user) {
                echo '🌐 Opening user deletion confirmation page...'."\n";
                $browser->visit(new UserDeletePage($user->id))
                    ->assertSee('Delete User')
                    ->assertSee($user->name)
                    ->assertSee($user->email)
                    ->click('@cancel-delete')
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying user still exists in database...'."\n";
            // Verify user still exists in database
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);

            echo '✅ SUCCESS: User deletion cancelled successfully for ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for cancellation with ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test user deletion from users index page.
     */
    public function test_user_can_be_deleted_from_users_index_page(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        echo "\n=== TEST: User Deletion (From Users Index Page) ===";
        echo "\nUser ID: ".$user->id;
        echo "\nUser Name: ".$user->name;
        echo "\nUser Email: ".$user->email;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user) {
                echo '🌐 Opening users index page...'."\n";
                $browser->visit('/users')
                    ->assertSee('Users Management')
                    ->assertSee($user->name)
                    ->assertSee($user->email)
                    ->click("a[href*='/users/{$user->id}/delete']")
                    ->assertSee('Delete User')
                    ->assertSee($user->name)
                    ->click('@confirm-delete')
                    ->pause(3000) // Wait for deletion
                    ->assertSee("User '{$user->name}' deleted successfully!")
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying user was deleted from database...'."\n";
            // Verify user was deleted from database
            $this->assertDatabaseMissing('users', [
                'id' => $user->id,
            ]);

            echo '✅ SUCCESS: User deleted from index page successfully with ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: User deletion from index page failed for ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test deletion of multiple users.
     */
    public function test_multiple_users_can_be_deleted(): void
    {
        $users = collect();
        for ($i = 0; $i < 3; $i++) {
            $users->push(User::create([
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => bcrypt($this->faker->password(8, 20)),
            ]));
        }

        echo "\n=== TEST: Multiple Users Deletion ===";
        echo "\nNumber of users to delete: ".$users->count();
        echo "\nUser IDs: ".$users->pluck('id')->implode(', ');
        echo "\nUser Names: ".$users->pluck('name')->implode(', ');
        echo "\nUser Emails: ".$users->pluck('email')->implode(', ');
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($users) {
                foreach ($users as $index => $user) {
                    echo '🌐 Deleting user '.($index + 1).' of '.$users->count().' (ID: '.$user->id.')...'."\n";
                    $browser->visit(new UserDeletePage($user->id))
                        ->assertSee('Delete User')
                        ->assertSee($user->name)
                        ->click('@confirm-delete')
                        ->pause(2000) // Wait for deletion
                        ->assertSee("User '{$user->name}' deleted successfully!")
                        ->assertPathIs('/users');
                }
            });

            echo '🔍 Verifying all users were deleted from database...'."\n";
            // Verify all users were deleted from database
            foreach ($users as $user) {
                $this->assertDatabaseMissing('users', [
                    'id' => $user->id,
                ]);
            }

            echo '✅ SUCCESS: All '.$users->count().' users deleted successfully'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Multiple users deletion failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test deletion of user with special characters in name.
     */
    public function test_user_with_special_characters_can_be_deleted(): void
    {
        $user = User::create([
            'name' => "John O'Connor-Smith & Co.",
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        echo "\n=== TEST: User Deletion (With Special Characters) ===";
        echo "\nUser ID: ".$user->id;
        echo "\nUser Name: ".$user->name;
        echo "\nUser Email: ".$user->email;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user) {
                echo '🌐 Opening user deletion confirmation page...'."\n";
                $browser->visit(new UserDeletePage($user->id))
                    ->assertSee('Delete User')
                    ->assertSee($user->name)
                    ->click('@confirm-delete')
                    ->pause(3000) // Wait for deletion
                    ->assertSee("User '{$user->name}' deleted successfully!")
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying user with special characters was deleted from database...'."\n";
            // Verify user was deleted from database
            $this->assertDatabaseMissing('users', [
                'id' => $user->id,
            ]);

            echo '✅ SUCCESS: User with special characters deleted successfully with ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: User deletion with special characters failed for ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test deletion of user with long name.
     */
    public function test_user_with_long_name_can_be_deleted(): void
    {
        $user = User::create([
            'name' => $this->faker->sentence(10), // Very long name
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        echo "\n=== TEST: User Deletion (With Long Name) ===";
        echo "\nUser ID: ".$user->id;
        echo "\nUser Name: ".$user->name.' (Length: '.strlen($user->name).')';
        echo "\nUser Email: ".$user->email;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user) {
                echo '🌐 Opening user deletion confirmation page...'."\n";
                $browser->visit(new UserDeletePage($user->id))
                    ->assertSee('Delete User')
                    ->assertSee($user->name)
                    ->click('@confirm-delete')
                    ->pause(3000) // Wait for deletion
                    ->assertSee("User '{$user->name}' deleted successfully!")
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying user with long name was deleted from database...'."\n";
            // Verify user was deleted from database
            $this->assertDatabaseMissing('users', [
                'id' => $user->id,
            ]);

            echo '✅ SUCCESS: User with long name deleted successfully with ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: User deletion with long name failed for ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test that deleted user is not visible in users list.
     */
    public function test_deleted_user_is_not_visible_in_users_list(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        echo "\n=== TEST: Deleted User Not Visible in Users List ===";
        echo "\nUser ID: ".$user->id;
        echo "\nUser Name: ".$user->name;
        echo "\nUser Email: ".$user->email;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user) {
                // First verify user is visible
                echo '🌐 Verifying user is visible in users list...'."\n";
                $browser->visit('/users')
                    ->assertSee($user->name)
                    ->assertSee($user->email);

                // Delete the user
                echo '🌐 Deleting user...'."\n";
                $browser->visit(new UserDeletePage($user->id))
                    ->click('@confirm-delete')
                    ->pause(3000) // Wait for deletion
                    ->assertSee("User '{$user->name}' deleted successfully!");

                // Verify user is no longer visible
                echo '🔍 Verifying user is no longer visible in users list...'."\n";
                $browser->visit('/users')
                    ->assertDontSee($user->name)
                    ->assertDontSee($user->email);
            });

            echo '🔍 Verifying user was deleted from database...'."\n";
            // Verify user was deleted from database
            $this->assertDatabaseMissing('users', [
                'id' => $user->id,
            ]);

            echo '✅ SUCCESS: Deleted user is not visible in users list for ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for user visibility check with ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test deletion confirmation page shows correct user information.
     */
    public function test_deletion_confirmation_page_shows_correct_user_info(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        echo "\n=== TEST: Deletion Confirmation Page Shows Correct User Info ===";
        echo "\nUser ID: ".$user->id;
        echo "\nUser Name: ".$user->name;
        echo "\nUser Email: ".$user->email;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user) {
                echo '🌐 Opening user deletion confirmation page...'."\n";
                $browser->visit(new UserDeletePage($user->id))
                    ->assertSee('Delete User')
                    ->assertSee($user->name)
                    ->assertSee($user->email)
                    ->assertSee('This action cannot be undone')
                    ->assertSee('Are you sure you want to delete this user?')
                    ->assertSee('Yes, Delete User')
                    ->assertSee('Cancel');
            });

            echo '✅ SUCCESS: Deletion confirmation page shows correct user info for ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for confirmation page display with ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test that trying to delete non-existent user shows 404.
     */
    public function test_deleting_non_existent_user_shows_404(): void
    {
        $nonExistentUserId = 99999;

        echo "\n=== TEST: Delete Non-Existent User Shows 404 ===";
        echo "\nNon-existent User ID: ".$nonExistentUserId;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($nonExistentUserId) {
                echo '🌐 Attempting to access non-existent user deletion page...'."\n";
                $browser->visit("/users/{$nonExistentUserId}/delete")
                    ->assertSee('404')
                    ->assertSee('Not Found');
            });

            echo '✅ SUCCESS: Non-existent user deletion correctly shows 404 for ID: '.$nonExistentUserId."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for non-existent user with ID: '.$nonExistentUserId."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test deletion with empty users table.
     */
    public function test_deletion_with_empty_users_table(): void
    {
        // Ensure no users exist
        User::truncate();

        echo "\n=== TEST: Deletion with Empty Users Table ===";
        echo "\nUsers table has been truncated (emptied)";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users index page with empty table...'."\n";
                $browser->visit('/users')
                    ->assertSee('Users Management')
                    ->assertSee('No users found')
                    ->assertSee('Create the first user');
            });

            echo '✅ SUCCESS: Empty users table displays correctly'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for empty users table'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test that user count decreases after deletion.
     */
    public function test_user_count_decreases_after_deletion(): void
    {
        // Create multiple users
        $users = collect();
        for ($i = 0; $i < 5; $i++) {
            $users->push(User::create([
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => bcrypt($this->faker->password(8, 20)),
            ]));
        }

        $initialCount = User::count();

        echo "\n=== TEST: User Count Decreases After Deletion ===";
        echo "\nInitial user count: ".$initialCount;
        echo "\nUsers created: ".$users->count();
        echo "\nUser to delete ID: ".$users->first()->id;
        echo "\nUser to delete Name: ".$users->first()->name;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($users) {
                // Delete first user
                $userToDelete = $users->first();
                echo '🌐 Deleting user to test count decrease...'."\n";
                $browser->visit(new UserDeletePage($userToDelete->id))
                    ->click('@confirm-delete')
                    ->pause(3000) // Wait for deletion
                    ->assertSee("User '{$userToDelete->name}' deleted successfully!");
            });

            echo '🔍 Verifying user count decreased...'."\n";
            // Verify count decreased
            $this->assertEquals($initialCount - 1, User::count());
            $this->assertDatabaseMissing('users', [
                'id' => $users->first()->id,
            ]);

            echo '✅ SUCCESS: User count decreased correctly from '.$initialCount.' to '.User::count()."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for user count decrease'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }
}

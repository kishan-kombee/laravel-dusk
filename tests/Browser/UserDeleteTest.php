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

        $this->browse(function (Browser $browser) use ($user) {
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

        // Verify user was deleted from database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
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

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new UserDeletePage($user->id))
                ->assertSee('Delete User')
                ->assertSee($user->name)
                ->assertSee($user->email)
                ->click('@cancel-delete')
                ->assertPathIs('/users');
        });

        // Verify user still exists in database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
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

        $this->browse(function (Browser $browser) use ($user) {
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

        // Verify user was deleted from database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
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

        $this->browse(function (Browser $browser) use ($users) {
            foreach ($users as $user) {
                $browser->visit(new UserDeletePage($user->id))
                    ->assertSee('Delete User')
                    ->assertSee($user->name)
                    ->click('@confirm-delete')
                    ->pause(2000) // Wait for deletion
                    ->assertSee("User '{$user->name}' deleted successfully!")
                    ->assertPathIs('/users');
            }
        });

        // Verify all users were deleted from database
        foreach ($users as $user) {
            $this->assertDatabaseMissing('users', [
                'id' => $user->id,
            ]);
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

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new UserDeletePage($user->id))
                ->assertSee('Delete User')
                ->assertSee($user->name)
                ->click('@confirm-delete')
                ->pause(3000) // Wait for deletion
                ->assertSee("User '{$user->name}' deleted successfully!")
                ->assertPathIs('/users');
        });

        // Verify user was deleted from database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
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

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new UserDeletePage($user->id))
                ->assertSee('Delete User')
                ->assertSee($user->name)
                ->click('@confirm-delete')
                ->pause(3000) // Wait for deletion
                ->assertSee("User '{$user->name}' deleted successfully!")
                ->assertPathIs('/users');
        });

        // Verify user was deleted from database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
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

        $this->browse(function (Browser $browser) use ($user) {
            // First verify user is visible
            $browser->visit('/users')
                ->assertSee($user->name)
                ->assertSee($user->email);

            // Delete the user
            $browser->visit(new UserDeletePage($user->id))
                ->click('@confirm-delete')
                ->pause(3000) // Wait for deletion
                ->assertSee("User '{$user->name}' deleted successfully!");

            // Verify user is no longer visible
            $browser->visit('/users')
                ->assertDontSee($user->name)
                ->assertDontSee($user->email);
        });

        // Verify user was deleted from database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
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

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new UserDeletePage($user->id))
                ->assertSee('Delete User')
                ->assertSee($user->name)
                ->assertSee($user->email)
                ->assertSee('This action cannot be undone')
                ->assertSee('Are you sure you want to delete this user?')
                ->assertSee('Yes, Delete User')
                ->assertSee('Cancel');
        });
    }

    /**
     * Test that trying to delete non-existent user shows 404.
     */
    public function test_deleting_non_existent_user_shows_404(): void
    {
        $nonExistentUserId = 99999;

        $this->browse(function (Browser $browser) use ($nonExistentUserId) {
            $browser->visit("/users/{$nonExistentUserId}/delete")
                ->assertSee('404')
                ->assertSee('Not Found');
        });
    }

    /**
     * Test deletion with empty users table.
     */
    public function test_deletion_with_empty_users_table(): void
    {
        // Ensure no users exist
        User::truncate();

        $this->browse(function (Browser $browser) {
            $browser->visit('/users')
                ->assertSee('Users Management')
                ->assertSee('No users found')
                ->assertSee('Create the first user');
        });
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

        $this->browse(function (Browser $browser) use ($users) {
            // Delete first user
            $userToDelete = $users->first();
            $browser->visit(new UserDeletePage($userToDelete->id))
                ->click('@confirm-delete')
                ->pause(3000) // Wait for deletion
                ->assertSee("User '{$userToDelete->name}' deleted successfully!");
        });

        // Verify count decreased
        $this->assertEquals($initialCount - 1, User::count());
        $this->assertDatabaseMissing('users', [
            'id' => $users->first()->id,
        ]);
    }
}

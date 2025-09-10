<?php

namespace Tests\Browser;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\UserUpdatePage;
use Tests\DuskTestCase;

class UserUpdateTest extends DuskTestCase
{
    protected $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create();
    }

    /**
     * Test successful user update without password change.
     */
    public function test_user_can_update_successfully_without_password(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        $updatedData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ];

        $this->browse(function (Browser $browser) use ($user, $updatedData) {
            $browser->visit(new UserUpdatePage($user->id))
                ->assertSee('Update User')
                ->type('@name', $updatedData['name'])
                ->type('@email', $updatedData['email'])
                ->click('@submit')
                ->pause(3000) // Wait for form submission
                ->assertSee('User updated successfully!')
                ->assertPathIs('/users/'.$user->id.'/edit');
        });

        // Verify user was updated in database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
        ]);
    }

    /**
     * Test successful user update with password change.
     */
    public function test_user_can_update_successfully_with_password(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        $updatedData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(8, 20),
        ];

        $this->browse(function (Browser $browser) use ($user, $updatedData) {
            $browser->visit(new UserUpdatePage($user->id))
                ->assertSee('Update User')
                ->type('@name', $updatedData['name'])
                ->type('@email', $updatedData['email'])
                ->type('@password', $updatedData['password'])
                ->type('@password_confirmation', $updatedData['password'])
                ->click('@submit')
                ->pause(3000) // Wait for form submission
                ->assertSee('User updated successfully!')
                ->assertPathIs('/users/'.$user->id.'/edit');
        });

        // Verify user was updated in database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
        ]);

        // Verify password was updated
        $updatedUser = User::find($user->id);
        $this->assertTrue(Hash::check($updatedData['password'], $updatedUser->password));
    }

    /**
     * Test update fails with invalid email format.
     */
    public function test_update_fails_with_invalid_email(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        $updatedData = [
            'name' => $this->faker->name(),
            'email' => 'invalid-email-format',
        ];

        $this->browse(function (Browser $browser) use ($user, $updatedData) {
            $browser->visit(new UserUpdatePage($user->id))
                ->type('@name', $updatedData['name'])
                ->type('@email', $updatedData['email'])
                ->click('@submit')
                ->assertSee('The email must be a valid email address')
                ->assertPathIs('/users/'.$user->id.'/edit');
        });

        // Verify user was not updated in database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
        ]);
    }

    /**
     * Test update fails with duplicate email.
     */
    public function test_update_fails_with_duplicate_email(): void
    {
        $existingUser = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        $userToUpdate = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        $updatedData = [
            'name' => $this->faker->name(),
            'email' => $existingUser->email, // Same email as existing user
        ];

        $this->browse(function (Browser $browser) use ($userToUpdate, $updatedData) {
            $browser->visit(new UserUpdatePage($userToUpdate->id))
                ->type('@name', $updatedData['name'])
                ->type('@email', $updatedData['email'])
                ->click('@submit')
                ->assertSee('The email has already been taken')
                ->assertPathIs('/users/'.$userToUpdate->id.'/edit');
        });

        // Verify user was not updated in database
        $this->assertDatabaseMissing('users', [
            'id' => $userToUpdate->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
        ]);
    }

    /**
     * Test update fails with password confirmation mismatch.
     */
    public function test_update_fails_with_password_mismatch(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        $updatedData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(8, 20),
            'password_confirmation' => $this->faker->password(8, 20), // Different password
        ];

        $this->browse(function (Browser $browser) use ($user, $updatedData) {
            $browser->visit(new UserUpdatePage($user->id))
                ->type('@name', $updatedData['name'])
                ->type('@email', $updatedData['email'])
                ->type('@password', $updatedData['password'])
                ->type('@password_confirmation', $updatedData['password_confirmation'])
                ->click('@submit')
                ->assertSee('The password confirmation does not match')
                ->assertPathIs('/users/'.$user->id.'/edit');
        });

        // Verify user was not updated in database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
        ]);
    }

    /**
     * Test update fails with missing required fields.
     */
    public function test_update_fails_with_missing_fields(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new UserUpdatePage($user->id))
                ->clear('@name')
                ->clear('@email')
                ->click('@submit')
                ->assertSee('The name field is required')
                ->assertSee('The email field is required')
                ->assertPathIs('/users/'.$user->id.'/edit');
        });
    }

    /**
     * Test update fails with short password when password is provided.
     */
    public function test_update_fails_with_short_password(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        $updatedData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(1, 7), // Short password (less than 8 characters)
        ];

        $this->browse(function (Browser $browser) use ($user, $updatedData) {
            $browser->visit(new UserUpdatePage($user->id))
                ->type('@name', $updatedData['name'])
                ->type('@email', $updatedData['email'])
                ->type('@password', $updatedData['password'])
                ->type('@password_confirmation', $updatedData['password'])
                ->click('@submit')
                ->assertSee('The password must be at least 8 characters')
                ->assertPathIs('/users/'.$user->id.'/edit');
        });

        // Verify user was not updated in database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
        ]);
    }

    /**
     * Test that user can update only name without changing email.
     */
    public function test_user_can_update_only_name(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        $originalEmail = $user->email;
        $updatedName = $this->faker->name();

        $this->browse(function (Browser $browser) use ($user, $updatedName) {
            $browser->visit(new UserUpdatePage($user->id))
                ->clear('@name')
                ->type('@name', $updatedName)
                ->click('@submit')
                ->pause(3000) // Wait for form submission
                ->assertSee('User updated successfully!')
                ->assertPathIs('/users/'.$user->id.'/edit');
        });

        // Verify only name was updated, email remains the same
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updatedName,
            'email' => $originalEmail,
        ]);
    }

    /**
     * Test that user can update only email without changing name.
     */
    public function test_user_can_update_only_email(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        $originalName = $user->name;
        $updatedEmail = $this->faker->unique()->safeEmail();

        $this->browse(function (Browser $browser) use ($user, $updatedEmail) {
            $browser->visit(new UserUpdatePage($user->id))
                ->clear('@email')
                ->type('@email', $updatedEmail)
                ->click('@submit')
                ->pause(3000) // Wait for form submission
                ->assertSee('User updated successfully!')
                ->assertPathIs('/users/'.$user->id.'/edit');
        });

        // Verify only email was updated, name remains the same
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $originalName,
            'email' => $updatedEmail,
        ]);
    }

    /**
     * Test that user can go back to users list.
     */
    public function test_user_can_go_back_to_users_list(): void
    {
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password(8, 20)),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new UserUpdatePage($user->id))
                ->click('@back-button')
                ->assertPathIs('/users');
        });
    }
}

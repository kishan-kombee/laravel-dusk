<?php

namespace Tests\Browser;

use App\Models\User;
use Faker\Factory as Faker;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\RegisterPage;
use Tests\DuskTestCase;

class UserRegistrationTest extends DuskTestCase
{
    protected $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create();
    }

    /**
     * Test successful user registration.
     */
    public function test_user_can_register_successfully(): void
    {
        $userData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(8, 20),
        ];

        $this->browse(function (Browser $browser) use ($userData) {
            $browser->visit('/register')
                ->assertSee('Register')
                ->type('name', $userData['name'])
                ->type('email', $userData['email'])
                ->type('password', $userData['password'])
                ->type('password_confirmation', $userData['password'])
                ->click('button[type="submit"]')
                ->pause(3000) // Wait for form submission
                ->assertSee('Registration successful!')
                ->assertPathIs('/register');
        });

        // Verify user was created in database
        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    /**
     * Test registration with invalid email.
     */
    public function test_registration_fails_with_invalid_email(): void
    {
        $userData = [
            'name' => $this->faker->name(),
            'email' => 'invalid-email-format',
            'password' => $this->faker->password(8, 20),
        ];

        $this->browse(function (Browser $browser) use ($userData) {
            $browser->visit(new RegisterPage)
                ->type('@name', $userData['name'])
                ->type('@email', $userData['email'])
                ->type('@password', $userData['password'])
                ->type('@password_confirmation', $userData['password'])
                ->click('@submit')
                ->assertSee('The email must be a valid email address')
                ->assertPathIs('/register');
        });

        // Verify user was not created in database
        $this->assertDatabaseMissing('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    /**
     * Test registration with password confirmation mismatch.
     */
    public function test_registration_fails_with_password_mismatch(): void
    {
        $userData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(8, 20),
            'password_confirmation' => $this->faker->password(8, 20), // Different password
        ];

        $this->browse(function (Browser $browser) use ($userData) {
            $browser->visit(new RegisterPage)
                ->type('@name', $userData['name'])
                ->type('@email', $userData['email'])
                ->type('@password', $userData['password'])
                ->type('@password_confirmation', $userData['password_confirmation'])
                ->click('@submit')
                ->assertSee('The password confirmation does not match')
                ->assertPathIs('/register');
        });

        // Verify user was not created in database
        $this->assertDatabaseMissing('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    /**
     * Test registration with duplicate email.
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        $existingUserData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(8, 20),
        ];

        $newUserData = [
            'name' => $this->faker->name(),
            'email' => $existingUserData['email'], // Same email
            'password' => $this->faker->password(8, 20),
        ];

        // Create existing user
        User::create([
            'name' => $existingUserData['name'],
            'email' => $existingUserData['email'],
            'password' => bcrypt($existingUserData['password']),
        ]);

        $this->browse(function (Browser $browser) use ($newUserData) {
            $browser->visit(new RegisterPage)
                ->type('@name', $newUserData['name'])
                ->type('@email', $newUserData['email'])
                ->type('@password', $newUserData['password'])
                ->type('@password_confirmation', $newUserData['password'])
                ->click('@submit')
                ->assertSee('The email has already been taken')
                ->assertPathIs('/register');
        });

        // Verify only one user exists with this email
        $this->assertEquals(1, User::where('email', $existingUserData['email'])->count());
    }

    /**
     * Test registration with missing required fields.
     */
    public function test_registration_fails_with_missing_fields(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->click('@submit')
                ->assertSee('The name field is required')
                ->assertSee('The email field is required')
                ->assertSee('The password field is required')
                ->assertPathIs('/register');
        });
    }

    /**
     * Test registration with short password.
     */
    public function test_registration_fails_with_short_password(): void
    {
        $userData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(1, 7), // Short password (less than 8 characters)
        ];

        $this->browse(function (Browser $browser) use ($userData) {
            $browser->visit(new RegisterPage)
                ->type('@name', $userData['name'])
                ->type('@email', $userData['email'])
                ->type('@password', $userData['password'])
                ->type('@password_confirmation', $userData['password'])
                ->click('@submit')
                ->assertSee('The password must be at least 8 characters')
                ->assertPathIs('/register');
        });
    }
}

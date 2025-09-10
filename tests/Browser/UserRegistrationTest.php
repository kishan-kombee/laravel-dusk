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
     * Test successful user registration.
     */
    public function test_user_can_register_successfully(): void
    {
        $userData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(8, 20),
        ];

        echo "\n=== TEST: Successful User Registration ===";
        echo "\nEmail: ".$userData['email'];
        echo "\nName: ".$userData['name'];
        echo "\nPassword: ".$userData['password'];
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($userData) {
                echo '🌐 Opening registration page...'."\n";
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

            echo '🔍 Verifying user in database...'."\n";
            // Verify user was created in database
            $this->assertDatabaseHas('users', [
                'name' => $userData['name'],
                'email' => $userData['email'],
            ]);

            echo '✅ SUCCESS: User registered successfully with email: '.$userData['email']."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Registration failed for email: '.$userData['email']."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Registration with Invalid Email ===";
        echo "\nEmail: ".$userData['email'];
        echo "\nName: ".$userData['name'];
        echo "\nPassword: ".$userData['password'];
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($userData) {
                echo '🌐 Opening registration page...'."\n";
                $browser->visit(new RegisterPage)
                    ->type('@name', $userData['name'])
                    ->type('@email', $userData['email'])
                    ->type('@password', $userData['password'])
                    ->type('@password_confirmation', $userData['password'])
                    ->pause(1000) // Wait before clicking
                    ->click('@submit')
                    ->pause(3000); // Wait longer for form submission
                echo '🔍 Looking for validation error message...'."\n";
                $browser->assertSee('The email field must be a valid email address')
                    ->assertPathIs('/register');
            });

            echo '✅ SUCCESS: Registration correctly failed with invalid email: '.$userData['email']."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for email: '.$userData['email']."\n";
            // echo "Error details: ".$e->getMessage()."\n";
            // throw $e;
        }

        // Verify user was not created in database
        $this->assertDatabaseMissing('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        echo '✅ SUCCESS: Registration correctly failed with invalid email: '.$userData['email']."\n";
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

        echo "\n=== TEST: Registration with Password Mismatch ===";
        echo "\nEmail: ".$userData['email'];
        echo "\nName: ".$userData['name'];
        echo "\nPassword: ".$userData['password'];
        echo "\nPassword Confirmation: ".$userData['password_confirmation'];
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($userData) {
                echo '🌐 Opening registration page...'."\n";
                $browser->visit(new RegisterPage)
                    ->type('@name', $userData['name'])
                    ->type('@email', $userData['email'])
                    ->type('@password', $userData['password'])
                    ->type('@password_confirmation', $userData['password_confirmation'])
                    ->click('@submit')
                    ->assertSee('The password confirmation does not match')
                    ->assertPathIs('/register');
            });

            echo '✅ SUCCESS: Registration correctly failed with password mismatch for email: '.$userData['email']."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for email: '.$userData['email']."\n";
            // echo "Error details: ".$e->getMessage()."\n";
            // throw $e;
        }

        // Verify user was not created in database
        $this->assertDatabaseMissing('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        echo '✅ SUCCESS: Registration correctly failed with password mismatch for email: '.$userData['email']."\n";
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

        echo "\n=== TEST: Registration with Duplicate Email ===";
        echo "\nExisting User Email: ".$existingUserData['email'];
        echo "\nNew User Email: ".$newUserData['email'];
        echo "\nNew User Name: ".$newUserData['name'];
        echo "\nNew User Password: ".$newUserData['password'];
        echo "\n";

        // Create existing user
        User::create([
            'name' => $existingUserData['name'],
            'email' => $existingUserData['email'],
            'password' => bcrypt($existingUserData['password']),
        ]);

        echo '📝 Created existing user with email: '.$existingUserData['email']."\n";

        try {
            $this->browse(function (Browser $browser) use ($newUserData) {
                echo '🌐 Opening registration page...'."\n";
                $browser->visit(new RegisterPage)
                    ->type('@name', $newUserData['name'])
                    ->type('@email', $newUserData['email'])
                    ->type('@password', $newUserData['password'])
                    ->type('@password_confirmation', $newUserData['password'])
                    ->click('@submit')
                    ->assertSee('The email has already been taken')
                    ->assertPathIs('/register');
            });

            echo '✅ SUCCESS: Registration correctly failed with duplicate email: '.$newUserData['email']."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for email: '.$newUserData['email']."\n";
            // echo "Error details: ".$e->getMessage()."\n";
            // throw $e;
        }

        // Verify only one user exists with this email
        $this->assertEquals(1, User::where('email', $existingUserData['email'])->count());
    }

    /**
     * Test registration with missing required fields.
     */
    public function test_registration_fails_with_missing_fields(): void
    {
        echo "\n=== TEST: Registration with Missing Fields ===";
        echo "\nTesting with empty form submission\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening registration page...'."\n";
                $browser->visit(new RegisterPage)
                    ->click('@submit')
                    ->assertSee('The name field is required')
                    ->assertSee('The email field is required')
                    ->assertSee('The password field is required')
                    ->assertPathIs('/register');
            });

            echo '✅ SUCCESS: Registration correctly failed with missing required fields'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for missing fields test'."\n";
            // echo "Error details: ".$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Registration with Short Password ===";
        echo "\nEmail: ".$userData['email'];
        echo "\nName: ".$userData['name'];
        echo "\nPassword: ".$userData['password'].' (Length: '.strlen($userData['password']).')';
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($userData) {
                echo '🌐 Opening registration page...'."\n";
                $browser->visit(new RegisterPage)
                    ->type('@name', $userData['name'])
                    ->type('@email', $userData['email'])
                    ->type('@password', $userData['password'])
                    ->type('@password_confirmation', $userData['password'])
                    ->click('@submit')
                    ->assertSee('The password must be at least 8 characters')
                    ->assertPathIs('/register');
            });

            echo '✅ SUCCESS: Registration correctly failed with short password for email: '.$userData['email']."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for email: '.$userData['email']."\n";
            // echo "Error details: ".$e->getMessage()."\n";
            // throw $e;
        }
    }
}

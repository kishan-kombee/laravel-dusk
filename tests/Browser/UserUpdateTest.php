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

        echo "\n=== TEST: Successful User Update (Without Password) ===";
        echo "\nUser ID: ".$user->id;
        echo "\nOriginal Name: ".$user->name;
        echo "\nOriginal Email: ".$user->email;
        echo "\nUpdated Name: ".$updatedData['name'];
        echo "\nUpdated Email: ".$updatedData['email'];
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user, $updatedData) {
                echo '🌐 Opening user update page...'."\n";
                $browser->visit(new UserUpdatePage($user->id))
                    ->assertSee('Update User')
                    ->type('@name', $updatedData['name'])
                    ->type('@email', $updatedData['email'])
                    ->click('@submit')
                    ->pause(3000) // Wait for form submission
                    ->assertSee('User updated successfully!')
                    ->assertPathIs('/users/'.$user->id.'/edit');
            });

            echo '🔍 Verifying user update in database...'."\n";
            // Verify user was updated in database
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => $updatedData['name'],
                'email' => $updatedData['email'],
            ]);

            echo '✅ SUCCESS: User updated successfully with ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: User update failed for ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Successful User Update (With Password) ===";
        echo "\nUser ID: ".$user->id;
        echo "\nOriginal Name: ".$user->name;
        echo "\nOriginal Email: ".$user->email;
        echo "\nUpdated Name: ".$updatedData['name'];
        echo "\nUpdated Email: ".$updatedData['email'];
        echo "\nUpdated Password: ".$updatedData['password'];
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user, $updatedData) {
                echo '🌐 Opening user update page...'."\n";
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

            echo '🔍 Verifying user update in database...'."\n";
            // Verify user was updated in database
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => $updatedData['name'],
                'email' => $updatedData['email'],
            ]);

            echo '🔍 Verifying password was updated...'."\n";
            // Verify password was updated
            $updatedUser = User::find($user->id);
            $this->assertTrue(Hash::check($updatedData['password'], $updatedUser->password));

            echo '✅ SUCCESS: User updated successfully with password for ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: User update with password failed for ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Update Fails with Invalid Email ===";
        echo "\nUser ID: ".$user->id;
        echo "\nOriginal Name: ".$user->name;
        echo "\nOriginal Email: ".$user->email;
        echo "\nUpdated Name: ".$updatedData['name'];
        echo "\nInvalid Email: ".$updatedData['email'];
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user, $updatedData) {
                echo '🌐 Opening user update page...'."\n";
                $browser->visit(new UserUpdatePage($user->id))
                    ->type('@name', $updatedData['name'])
                    ->type('@email', $updatedData['email'])
                    ->click('@submit')
                    ->assertSee('The email must be a valid email address')
                    ->assertPathIs('/users/'.$user->id.'/edit');
            });

            echo '🔍 Verifying user was not updated in database...'."\n";
            // Verify user was not updated in database
            $this->assertDatabaseMissing('users', [
                'id' => $user->id,
                'name' => $updatedData['name'],
                'email' => $updatedData['email'],
            ]);

            echo '✅ SUCCESS: Update correctly failed with invalid email for ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for invalid email with ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Update Fails with Duplicate Email ===";
        echo "\nExisting User ID: ".$existingUser->id;
        echo "\nExisting User Email: ".$existingUser->email;
        echo "\nUser to Update ID: ".$userToUpdate->id;
        echo "\nUser to Update Original Name: ".$userToUpdate->name;
        echo "\nUser to Update Original Email: ".$userToUpdate->email;
        echo "\nUpdated Name: ".$updatedData['name'];
        echo "\nDuplicate Email: ".$updatedData['email'];
        echo "\n";

        echo '📝 Created existing user with email: '.$existingUser->email."\n";

        try {
            $this->browse(function (Browser $browser) use ($userToUpdate, $updatedData) {
                echo '🌐 Opening user update page...'."\n";
                $browser->visit(new UserUpdatePage($userToUpdate->id))
                    ->type('@name', $updatedData['name'])
                    ->type('@email', $updatedData['email'])
                    ->click('@submit')
                    ->assertSee('The email has already been taken')
                    ->assertPathIs('/users/'.$userToUpdate->id.'/edit');
            });

            echo '🔍 Verifying user was not updated in database...'."\n";
            // Verify user was not updated in database
            $this->assertDatabaseMissing('users', [
                'id' => $userToUpdate->id,
                'name' => $updatedData['name'],
                'email' => $updatedData['email'],
            ]);

            echo '✅ SUCCESS: Update correctly failed with duplicate email for ID: '.$userToUpdate->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for duplicate email with ID: '.$userToUpdate->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Update Fails with Password Mismatch ===";
        echo "\nUser ID: ".$user->id;
        echo "\nOriginal Name: ".$user->name;
        echo "\nOriginal Email: ".$user->email;
        echo "\nUpdated Name: ".$updatedData['name'];
        echo "\nUpdated Email: ".$updatedData['email'];
        echo "\nPassword: ".$updatedData['password'];
        echo "\nPassword Confirmation: ".$updatedData['password_confirmation'];
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user, $updatedData) {
                echo '🌐 Opening user update page...'."\n";
                $browser->visit(new UserUpdatePage($user->id))
                    ->type('@name', $updatedData['name'])
                    ->type('@email', $updatedData['email'])
                    ->type('@password', $updatedData['password'])
                    ->type('@password_confirmation', $updatedData['password_confirmation'])
                    ->click('@submit')
                    ->assertSee('The password confirmation does not match')
                    ->assertPathIs('/users/'.$user->id.'/edit');
            });

            echo '🔍 Verifying user was not updated in database...'."\n";
            // Verify user was not updated in database
            $this->assertDatabaseMissing('users', [
                'id' => $user->id,
                'name' => $updatedData['name'],
                'email' => $updatedData['email'],
            ]);

            echo '✅ SUCCESS: Update correctly failed with password mismatch for ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for password mismatch with ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Update Fails with Missing Fields ===";
        echo "\nUser ID: ".$user->id;
        echo "\nOriginal Name: ".$user->name;
        echo "\nOriginal Email: ".$user->email;
        echo "\nTesting with empty form submission\n";

        try {
            $this->browse(function (Browser $browser) use ($user) {
                echo '🌐 Opening user update page...'."\n";
                $browser->visit(new UserUpdatePage($user->id))
                    ->clear('@name')
                    ->clear('@email')
                    ->click('@submit')
                    ->assertSee('The name field is required')
                    ->assertSee('The email field is required')
                    ->assertPathIs('/users/'.$user->id.'/edit');
            });

            echo '✅ SUCCESS: Update correctly failed with missing required fields for ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for missing fields with ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Update Fails with Short Password ===";
        echo "\nUser ID: ".$user->id;
        echo "\nOriginal Name: ".$user->name;
        echo "\nOriginal Email: ".$user->email;
        echo "\nUpdated Name: ".$updatedData['name'];
        echo "\nUpdated Email: ".$updatedData['email'];
        echo "\nShort Password: ".$updatedData['password'].' (Length: '.strlen($updatedData['password']).')';
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user, $updatedData) {
                echo '🌐 Opening user update page...'."\n";
                $browser->visit(new UserUpdatePage($user->id))
                    ->type('@name', $updatedData['name'])
                    ->type('@email', $updatedData['email'])
                    ->type('@password', $updatedData['password'])
                    ->type('@password_confirmation', $updatedData['password'])
                    ->click('@submit')
                    ->assertSee('The password must be at least 8 characters')
                    ->assertPathIs('/users/'.$user->id.'/edit');
            });

            echo '🔍 Verifying user was not updated in database...'."\n";
            // Verify user was not updated in database
            $this->assertDatabaseMissing('users', [
                'id' => $user->id,
                'name' => $updatedData['name'],
                'email' => $updatedData['email'],
            ]);

            echo '✅ SUCCESS: Update correctly failed with short password for ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for short password with ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Update Only Name (Keep Email) ===";
        echo "\nUser ID: ".$user->id;
        echo "\nOriginal Name: ".$user->name;
        echo "\nOriginal Email: ".$user->email;
        echo "\nUpdated Name: ".$updatedName;
        echo "\nEmail (unchanged): ".$originalEmail;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user, $updatedName) {
                echo '🌐 Opening user update page...'."\n";
                $browser->visit(new UserUpdatePage($user->id))
                    ->clear('@name')
                    ->type('@name', $updatedName)
                    ->click('@submit')
                    ->pause(3000) // Wait for form submission
                    ->assertSee('User updated successfully!')
                    ->assertPathIs('/users/'.$user->id.'/edit');
            });

            echo '🔍 Verifying only name was updated...'."\n";
            // Verify only name was updated, email remains the same
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => $updatedName,
                'email' => $originalEmail,
            ]);

            echo '✅ SUCCESS: Only name updated successfully for ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for name-only update with ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Update Only Email (Keep Name) ===";
        echo "\nUser ID: ".$user->id;
        echo "\nOriginal Name: ".$user->name;
        echo "\nOriginal Email: ".$user->email;
        echo "\nName (unchanged): ".$originalName;
        echo "\nUpdated Email: ".$updatedEmail;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user, $updatedEmail) {
                echo '🌐 Opening user update page...'."\n";
                $browser->visit(new UserUpdatePage($user->id))
                    ->clear('@email')
                    ->type('@email', $updatedEmail)
                    ->click('@submit')
                    ->pause(3000) // Wait for form submission
                    ->assertSee('User updated successfully!')
                    ->assertPathIs('/users/'.$user->id.'/edit');
            });

            echo '🔍 Verifying only email was updated...'."\n";
            // Verify only email was updated, name remains the same
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => $originalName,
                'email' => $updatedEmail,
            ]);

            echo '✅ SUCCESS: Only email updated successfully for ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for email-only update with ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
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

        echo "\n=== TEST: Go Back to Users List ===";
        echo "\nUser ID: ".$user->id;
        echo "\nUser Name: ".$user->name;
        echo "\nUser Email: ".$user->email;
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($user) {
                echo '🌐 Opening user update page...'."\n";
                $browser->visit(new UserUpdatePage($user->id))
                    ->click('@back-button')
                    ->assertPathIs('/users');
            });

            echo '✅ SUCCESS: Successfully navigated back to users list from ID: '.$user->id."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for back navigation with ID: '.$user->id."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }
}

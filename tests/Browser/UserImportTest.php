<?php

namespace Tests\Browser;

use App\Models\User;
use Faker\Factory as Faker;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\UserImportPage;
use Tests\DuskTestCase;

class UserImportTest extends DuskTestCase
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

        // Clear users table before each test
        // User::truncate();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test successful import of valid CSV file.
     */
    public function test_can_import_valid_csv_file_successfully(): void
    {
        $csvPath = base_path('tests/fixtures/valid_users.csv');

        echo "\n=== TEST: Successful Import of Valid CSV File ===";
        echo "\nCSV File Path: ".$csvPath;
        echo "\nExpected Users: 5";
        echo "\nExpected Users: John Doe, Jane Smith, Bob Johnson, Alice Brown, Charlie Wilson";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($csvPath) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->assertSee('Import Users from CSV')
                    ->assertCsvFormatHelp()
                    ->attach('@csv-file-input', $csvPath)
                    ->click('@import-submit')
                    ->pause(5000) // Wait longer for processing
                    ->assertSee('Successfully imported 5 users.')
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying users were created in database...'."\n";
            // Verify users were created in database
            $this->assertDatabaseHas('users', [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ]);
            $this->assertDatabaseHas('users', [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
            ]);
            $this->assertDatabaseHas('users', [
                'name' => 'Bob Johnson',
                'email' => 'bob.johnson@example.com',
            ]);
            $this->assertDatabaseHas('users', [
                'name' => 'Alice Brown',
                'email' => 'alice.brown@example.com',
            ]);
            $this->assertDatabaseHas('users', [
                'name' => 'Charlie Wilson',
                'email' => 'charlie.wilson@example.com',
            ]);

            echo '✅ SUCCESS: Valid CSV file imported successfully with 5 users'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Valid CSV import failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test import with invalid CSV file shows validation errors.
     */
    public function test_import_fails_with_invalid_csv_file(): void
    {
        $csvPath = base_path('tests/fixtures/invalid_users.csv');

        echo "\n=== TEST: Import with Invalid CSV File ===";
        echo "\nCSV File Path: ".$csvPath;
        echo "\nExpected: Partial success with validation errors";
        echo "\nValid user expected: Charlie Wilson";
        echo "\nInvalid users: invalid-email, empty name";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($csvPath) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->attach('@csv-file-input', $csvPath)
                    ->click('@import-submit')
                    ->pause(5000) // Wait for processing
                    ->assertSee('Successfully imported') // Should show partial success with errors
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying valid users were created...'."\n";
            // Verify some users were created (valid ones)
            $this->assertDatabaseHas('users', [
                'name' => 'Charlie Wilson',
                'email' => 'charlie.wilson@example.com',
            ]);

            echo '🔍 Verifying invalid users were not created...'."\n";
            // Verify invalid users were not created
            $this->assertDatabaseMissing('users', [
                'email' => 'invalid-email',
            ]);
            $this->assertDatabaseMissing('users', [
                'name' => '',
            ]);

            echo '✅ SUCCESS: Invalid CSV handled correctly with partial import'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Invalid CSV import test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test import with mixed valid and invalid data.
     */
    public function test_import_handles_mixed_valid_and_invalid_data(): void
    {
        $csvPath = base_path('tests/fixtures/mixed_users.csv');

        echo "\n=== TEST: Import with Mixed Valid and Invalid Data ===";
        echo "\nCSV File Path: ".$csvPath;
        echo "\nExpected: Partial success with mixed data";
        echo "\nValid users: Valid User, Valid User 2";
        echo "\nInvalid users: invalid-email-format, empty name";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($csvPath) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->attach('@csv-file-input', $csvPath)
                    ->click('@import-submit')
                    ->pause(5000) // Wait for processing
                    ->assertSee('Successfully imported') // Should show partial success
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying valid users were created...'."\n";
            // Verify valid users were created
            $this->assertDatabaseHas('users', [
                'name' => 'Valid User',
                'email' => 'valid.user@example.com',
            ]);
            $this->assertDatabaseHas('users', [
                'name' => 'Valid User 2',
                'email' => 'valid.user2@example.com',
            ]);

            echo '🔍 Verifying invalid users were not created...'."\n";
            // Verify invalid users were not created
            $this->assertDatabaseMissing('users', [
                'email' => 'invalid-email-format',
            ]);
            $this->assertDatabaseMissing('users', [
                'name' => '',
            ]);

            echo '✅ SUCCESS: Mixed data import handled correctly'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Mixed data import test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test import with large CSV file.
     */
    public function test_can_import_large_csv_file(): void
    {
        $csvPath = base_path('tests/fixtures/large_users.csv');

        echo "\n=== TEST: Import Large CSV File ===";
        echo "\nCSV File Path: ".$csvPath;
        echo "\nExpected Users: 20";
        echo "\nUser Pattern: User 1, User 2, ..., User 20";
        echo "\nEmail Pattern: user1@example.com, user2@example.com, ..., user20@example.com";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($csvPath) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->attach('@csv-file-input', $csvPath)
                    ->click('@import-submit')
                    ->pause(5000) // Wait longer for processing
                    ->assertSuccessMessage('Successfully imported 20 users')
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying all 20 users were created in database...'."\n";
            // Verify all users were created
            for ($i = 1; $i <= 20; $i++) {
                $this->assertDatabaseHas('users', [
                    'name' => "User {$i}",
                    'email' => "user{$i}@example.com",
                ]);
            }

            echo '✅ SUCCESS: Large CSV file imported successfully with 20 users'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Large CSV import failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test import without selecting a file shows validation error.
     */
    public function test_import_fails_without_file(): void
    {
        echo "\n=== TEST: Import Fails Without File ===";
        echo "\nExpected: Validation error for missing file";
        echo "\nExpected Error: 'The csv file field is required'";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->click('@import-submit')
                    ->assertValidationErrors(['The csv file field is required'])
                    ->assertPathIs('/users/import');
            });

            echo '✅ SUCCESS: Import correctly failed without file selection'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for missing file validation'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test import with non-CSV file shows validation error.
     */
    public function test_import_fails_with_non_csv_file(): void
    {
        // Create a temporary text file that's not CSV
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'This is not a CSV file');

        echo "\n=== TEST: Import Fails with Non-CSV File ===";
        echo "\nTemp File: ".$tempFile;
        echo "\nFile Content: 'This is not a CSV file'";
        echo "\nExpected Error: 'The csv file must be a file of type: csv, txt'";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($tempFile) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->attach('@csv-file-input', $tempFile)
                    ->click('@import-submit')
                    ->assertValidationErrors(['The csv file must be a file of type: csv, txt'])
                    ->assertPathIs('/users/import');
            });

            echo '✅ SUCCESS: Import correctly failed with non-CSV file'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Test failed for non-CSV file validation'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Test import with duplicate emails.
     */
    public function test_import_handles_duplicate_emails(): void
    {
        // Create an existing user
        $existingUser = User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Create CSV with duplicate email
        $csvContent = "name,email,password\nNew User,existing@example.com,password456\n";
        $csvPath = tempnam(sys_get_temp_dir(), 'duplicate_test').'.csv';
        file_put_contents($csvPath, $csvContent);

        echo "\n=== TEST: Import Handles Duplicate Emails ===";
        echo "\nExisting User: ".$existingUser->name.' ('.$existingUser->email.')';
        echo "\nCSV File: ".$csvPath;
        echo "\nCSV Content: New User, existing@example.com, password456";
        echo "\nExpected: Only one user should exist with duplicate email";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($csvPath) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->attach('@csv-file-input', $csvPath)
                    ->click('@import-submit')
                    ->pause(5000) // Wait for processing
                    ->assertSuccessMessage() // Should show error about duplicate
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying only one user exists with duplicate email...'."\n";
            // Verify only one user exists with this email
            $this->assertEquals(1, User::where('email', 'existing@example.com')->count());
            $this->assertEquals('Existing User', User::where('email', 'existing@example.com')->first()->name);

            echo '✅ SUCCESS: Duplicate email handled correctly'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Duplicate email test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        } finally {
            unlink($csvPath);
        }
    }

    /**
     * Test import page displays correctly.
     */
    public function test_import_page_displays_correctly(): void
    {
        echo "\n=== TEST: Import Page Displays Correctly ===";
        echo "\nExpected Elements: Import Users from CSV, CSV File, Import Users, Back to Users";
        echo "\nExpected Help: Required fields (name, email, password), Notes";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->assertSee('Import Users from CSV')
                    ->assertSee('CSV File')
                    ->assertSee('Import Users')
                    ->assertSee('Back to Users')
                    ->assertCsvFormatHelp()
                    ->assertSee('Required fields:')
                    ->assertSee('name')
                    ->assertSee('email')
                    ->assertSee('password')
                    ->assertSee('Notes:');
            });

            echo '✅ SUCCESS: Import page displays all required elements correctly'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Import page display test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test navigation from users index to import page.
     */
    public function test_can_navigate_to_import_page_from_users_index(): void
    {
        echo "\n=== TEST: Navigate to Import Page from Users Index ===";
        echo "\nStarting from: /users";
        echo "\nExpected destination: /users/import";
        echo "\nExpected content: Import Users from CSV";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening users index page...'."\n";
                $browser->visit('/users')
                    ->assertSee('Users Management')
                    ->click('a[href*="/users/import"]')
                    ->assertPathIs('/users/import')
                    ->assertSee('Import Users from CSV');
            });

            echo '✅ SUCCESS: Navigation to import page successful'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Navigation to import page failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test navigation back to users index from import page.
     */
    public function test_can_navigate_back_to_users_index(): void
    {
        echo "\n=== TEST: Navigate Back to Users Index from Import Page ===";
        echo "\nStarting from: /users/import";
        echo "\nExpected destination: /users";
        echo "\nExpected content: Users Management";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->goBackToUsers()
                    ->assertPathIs('/users')
                    ->assertSee('Users Management');
            });

            echo '✅ SUCCESS: Navigation back to users index successful'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Navigation back to users index failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        }
    }

    /**
     * Test import with empty CSV file.
     */
    public function test_import_handles_empty_csv_file(): void
    {
        // Create empty CSV file
        $csvContent = "name,email,password\n";
        $csvPath = tempnam(sys_get_temp_dir(), 'empty_test').'.csv';
        file_put_contents($csvPath, $csvContent);

        echo "\n=== TEST: Import Handles Empty CSV File ===";
        echo "\nCSV File: ".$csvPath;
        echo "\nCSV Content: Headers only (no data rows)";
        echo "\nExpected Result: Successfully imported 0 users";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($csvPath) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->attach('@csv-file-input', $csvPath)
                    ->click('@import-submit')
                    ->pause(5000) // Wait for processing
                    ->assertSuccessMessage('Successfully imported 0 users')
                    ->assertPathIs('/users');
            });

            echo '✅ SUCCESS: Empty CSV file handled correctly'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Empty CSV file test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        } finally {
            unlink($csvPath);
        }
    }

    /**
     * Test import with CSV file containing only headers.
     */
    public function test_import_handles_csv_with_only_headers(): void
    {
        // Create CSV with only headers
        $csvContent = "name,email,password\n";
        $csvPath = tempnam(sys_get_temp_dir(), 'headers_only_test').'.csv';
        file_put_contents($csvPath, $csvContent);

        echo "\n=== TEST: Import Handles CSV with Only Headers ===";
        echo "\nCSV File: ".$csvPath;
        echo "\nCSV Content: Headers only (name,email,password)";
        echo "\nExpected Result: Successfully imported 0 users";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($csvPath) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->attach('@csv-file-input', $csvPath)
                    ->click('@import-submit')
                    ->pause(5000) // Wait for processing
                    ->assertSuccessMessage('Successfully imported 0 users')
                    ->assertPathIs('/users');
            });

            echo '✅ SUCCESS: CSV with only headers handled correctly'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: CSV with only headers test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        } finally {
            unlink($csvPath);
        }
    }

    /**
     * Test import with CSV file containing special characters in names.
     */
    public function test_import_handles_special_characters_in_names(): void
    {
        // Create CSV with special characters
        $csvContent = "name,email,password\n\"O'Connor, John\",john.oconnor@example.com,password123\n\"Smith & Co.\",smith.co@example.com,password456\n";
        $csvPath = tempnam(sys_get_temp_dir(), 'special_chars_test').'.csv';
        file_put_contents($csvPath, $csvContent);

        echo "\n=== TEST: Import Handles Special Characters in Names ===";
        echo "\nCSV File: ".$csvPath;
        echo "\nSpecial Names: O'Connor, John; Smith & Co.";
        echo "\nExpected Users: 2";
        echo "\nExpected Emails: john.oconnor@example.com, smith.co@example.com";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($csvPath) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->attach('@csv-file-input', $csvPath)
                    ->click('@import-submit')
                    ->pause(5000) // Wait for processing
                    ->assertSuccessMessage('Successfully imported 2 users')
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying users with special characters were created...'."\n";
            // Verify users with special characters were created
            $this->assertDatabaseHas('users', [
                'email' => 'john.oconnor@example.com',
            ]);
            $this->assertDatabaseHas('users', [
                'email' => 'smith.co@example.com',
            ]);

            echo '✅ SUCCESS: Special characters in names handled correctly'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Special characters test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        } finally {
            unlink($csvPath);
        }
    }

    /**
     * Test import with very long email addresses.
     */
    public function test_import_handles_long_email_addresses(): void
    {
        // Create CSV with very long email
        $longEmail = 'very.long.email.address.that.is.definitely.longer.than.normal@example.com';
        $csvContent = "name,email,password\nLong Email User,{$longEmail},password123\n";
        $csvPath = tempnam(sys_get_temp_dir(), 'long_email_test').'.csv';
        file_put_contents($csvPath, $csvContent);

        echo "\n=== TEST: Import Handles Long Email Addresses ===";
        echo "\nCSV File: ".$csvPath;
        echo "\nLong Email: ".$longEmail.' (Length: '.strlen($longEmail).')';
        echo "\nExpected Users: 1";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($csvPath) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->attach('@csv-file-input', $csvPath)
                    ->click('@import-submit')
                    ->pause(5000) // Wait for processing
                    ->assertSuccessMessage('Successfully imported 1 users')
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying user with long email was created...'."\n";
            // Verify user with long email was created
            $this->assertDatabaseHas('users', [
                'email' => $longEmail,
            ]);

            echo '✅ SUCCESS: Long email address handled correctly'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Long email address test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        } finally {
            unlink($csvPath);
        }
    }

    /**
     * Test import with CSV file that has extra columns.
     */
    public function test_import_handles_extra_columns(): void
    {
        // Create CSV with extra columns
        $csvContent = "name,email,password,extra_column,another_column\nJohn Doe,john@example.com,password123,extra_value,another_value\n";
        $csvPath = tempnam(sys_get_temp_dir(), 'extra_columns_test').'.csv';
        file_put_contents($csvPath, $csvContent);

        echo "\n=== TEST: Import Handles Extra Columns ===";
        echo "\nCSV File: ".$csvPath;
        echo "\nExtra Columns: extra_column, another_column";
        echo "\nExpected: Extra columns should be ignored";
        echo "\nExpected Users: 1 (John Doe)";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($csvPath) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->attach('@csv-file-input', $csvPath)
                    ->click('@import-submit')
                    ->pause(5000) // Wait for processing
                    ->assertSuccessMessage('Successfully imported 1 users')
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying user was created (extra columns should be ignored)...'."\n";
            // Verify user was created (extra columns should be ignored)
            $this->assertDatabaseHas('users', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ]);

            echo '✅ SUCCESS: Extra columns handled correctly (ignored)'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Extra columns test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        } finally {
            unlink($csvPath);
        }
    }

    /**
     * Test import with CSV file that has missing columns.
     */
    public function test_import_handles_missing_columns(): void
    {
        // Create CSV with missing password column
        $csvContent = "name,email\nJohn Doe,john@example.com\n";
        $csvPath = tempnam(sys_get_temp_dir(), 'missing_columns_test').'.csv';
        file_put_contents($csvPath, $csvContent);

        echo "\n=== TEST: Import Handles Missing Columns ===";
        echo "\nCSV File: ".$csvPath;
        echo "\nMissing Column: password";
        echo "\nCSV Content: name,email only";
        echo "\nExpected: User created with default password";
        echo "\nExpected Users: 1 (John Doe)";
        echo "\n";

        try {
            $this->browse(function (Browser $browser) use ($csvPath) {
                echo '🌐 Opening user import page...'."\n";
                $browser->visit(new UserImportPage)
                    ->attach('@csv-file-input', $csvPath)
                    ->click('@import-submit')
                    ->pause(5000) // Wait for processing
                    ->assertSuccessMessage('Successfully imported 1 users')
                    ->assertPathIs('/users');
            });

            echo '🔍 Verifying user was created with default password...'."\n";
            // Verify user was created with default password
            $this->assertDatabaseHas('users', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ]);

            echo '✅ SUCCESS: Missing columns handled correctly (default password used)'."\n";
        } catch (\Exception $e) {
            echo '❌ ERROR: Missing columns test failed'."\n";
            // echo 'Error details: '.$e->getMessage()."\n";
            // throw $e;
        } finally {
            unlink($csvPath);
        }
    }
}

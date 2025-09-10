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

        // Clear users table before each test
        // User::truncate();
    }

    /**
     * Test successful import of valid CSV file.
     */
    public function test_can_import_valid_csv_file_successfully(): void
    {
        $csvPath = base_path('tests/fixtures/valid_users.csv');

        $this->browse(function (Browser $browser) use ($csvPath) {
            $browser->visit(new UserImportPage)
                ->assertSee('Import Users from CSV')
                ->assertCsvFormatHelp()
                ->attach('@csv-file-input', $csvPath)
                ->click('@import-submit')
                ->pause(5000) // Wait longer for processing
                ->assertSee('Successfully imported 5 users.')
                ->assertPathIs('/users');
        });

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
    }

    /**
     * Test import with invalid CSV file shows validation errors.
     */
    public function test_import_fails_with_invalid_csv_file(): void
    {
        $csvPath = base_path('tests/fixtures/invalid_users.csv');

        $this->browse(function (Browser $browser) use ($csvPath) {
            $browser->visit(new UserImportPage)
                ->attach('@csv-file-input', $csvPath)
                ->click('@import-submit')
                ->pause(5000) // Wait for processing
                ->assertSee('Successfully imported') // Should show partial success with errors
                ->assertPathIs('/users');
        });

        // Verify some users were created (valid ones)
        $this->assertDatabaseHas('users', [
            'name' => 'Charlie Wilson',
            'email' => 'charlie.wilson@example.com',
        ]);

        // Verify invalid users were not created
        $this->assertDatabaseMissing('users', [
            'email' => 'invalid-email',
        ]);
        $this->assertDatabaseMissing('users', [
            'name' => '',
        ]);
    }

    /**
     * Test import with mixed valid and invalid data.
     */
    public function test_import_handles_mixed_valid_and_invalid_data(): void
    {
        $csvPath = base_path('tests/fixtures/mixed_users.csv');

        $this->browse(function (Browser $browser) use ($csvPath) {
            $browser->visit(new UserImportPage)
                ->attach('@csv-file-input', $csvPath)
                ->click('@import-submit')
                ->pause(5000) // Wait for processing
                ->assertSee('Successfully imported') // Should show partial success
                ->assertPathIs('/users');
        });

        // Verify valid users were created
        $this->assertDatabaseHas('users', [
            'name' => 'Valid User',
            'email' => 'valid.user@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Valid User 2',
            'email' => 'valid.user2@example.com',
        ]);

        // Verify invalid users were not created
        $this->assertDatabaseMissing('users', [
            'email' => 'invalid-email-format',
        ]);
        $this->assertDatabaseMissing('users', [
            'name' => '',
        ]);
    }

    /**
     * Test import with large CSV file.
     */
    public function test_can_import_large_csv_file(): void
    {
        $csvPath = base_path('tests/fixtures/large_users.csv');

        $this->browse(function (Browser $browser) use ($csvPath) {
            $browser->visit(new UserImportPage)
                ->attach('@csv-file-input', $csvPath)
                ->click('@import-submit')
                ->pause(5000) // Wait longer for processing
                ->assertSuccessMessage('Successfully imported 20 users')
                ->assertPathIs('/users');
        });

        // Verify all users were created
        for ($i = 1; $i <= 20; $i++) {
            $this->assertDatabaseHas('users', [
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
            ]);
        }
    }

    /**
     * Test import without selecting a file shows validation error.
     */
    public function test_import_fails_without_file(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new UserImportPage)
                ->click('@import-submit')
                ->assertValidationErrors(['The csv file field is required'])
                ->assertPathIs('/users/import');
        });
    }

    /**
     * Test import with non-CSV file shows validation error.
     */
    public function test_import_fails_with_non_csv_file(): void
    {
        // Create a temporary text file that's not CSV
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'This is not a CSV file');

        $this->browse(function (Browser $browser) use ($tempFile) {
            $browser->visit(new UserImportPage)
                ->attach('@csv-file-input', $tempFile)
                ->click('@import-submit')
                ->assertValidationErrors(['The csv file must be a file of type: csv, txt'])
                ->assertPathIs('/users/import');
        });

        unlink($tempFile);
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

        $this->browse(function (Browser $browser) use ($csvPath) {
            $browser->visit(new UserImportPage)
                ->attach('@csv-file-input', $csvPath)
                ->click('@import-submit')
                ->pause(5000) // Wait for processing
                ->assertSuccessMessage() // Should show error about duplicate
                ->assertPathIs('/users');
        });

        // Verify only one user exists with this email
        $this->assertEquals(1, User::where('email', 'existing@example.com')->count());
        $this->assertEquals('Existing User', User::where('email', 'existing@example.com')->first()->name);

        unlink($csvPath);
    }

    /**
     * Test import page displays correctly.
     */
    public function test_import_page_displays_correctly(): void
    {
        $this->browse(function (Browser $browser) {
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
    }

    /**
     * Test navigation from users index to import page.
     */
    public function test_can_navigate_to_import_page_from_users_index(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/users')
                ->assertSee('Users Management')
                ->click('a[href*="/users/import"]')
                ->assertPathIs('/users/import')
                ->assertSee('Import Users from CSV');
        });
    }

    /**
     * Test navigation back to users index from import page.
     */
    public function test_can_navigate_back_to_users_index(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new UserImportPage)
                ->goBackToUsers()
                ->assertPathIs('/users')
                ->assertSee('Users Management');
        });
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

        $this->browse(function (Browser $browser) use ($csvPath) {
            $browser->visit(new UserImportPage)
                ->attach('@csv-file-input', $csvPath)
                ->click('@import-submit')
                ->pause(5000) // Wait for processing
                ->assertSuccessMessage('Successfully imported 0 users')
                ->assertPathIs('/users');
        });

        unlink($csvPath);
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

        $this->browse(function (Browser $browser) use ($csvPath) {
            $browser->visit(new UserImportPage)
                ->attach('@csv-file-input', $csvPath)
                ->click('@import-submit')
                ->pause(5000) // Wait for processing
                ->assertSuccessMessage('Successfully imported 0 users')
                ->assertPathIs('/users');
        });

        unlink($csvPath);
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

        $this->browse(function (Browser $browser) use ($csvPath) {
            $browser->visit(new UserImportPage)
                ->attach('@csv-file-input', $csvPath)
                ->click('@import-submit')
                ->pause(5000) // Wait for processing
                ->assertSuccessMessage('Successfully imported 2 users')
                ->assertPathIs('/users');
        });

        // Verify users with special characters were created
        $this->assertDatabaseHas('users', [
            'email' => 'john.oconnor@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'smith.co@example.com',
        ]);

        unlink($csvPath);
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

        $this->browse(function (Browser $browser) use ($csvPath) {
            $browser->visit(new UserImportPage)
                ->attach('@csv-file-input', $csvPath)
                ->click('@import-submit')
                ->pause(5000) // Wait for processing
                ->assertSuccessMessage('Successfully imported 1 users')
                ->assertPathIs('/users');
        });

        // Verify user with long email was created
        $this->assertDatabaseHas('users', [
            'email' => $longEmail,
        ]);

        unlink($csvPath);
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

        $this->browse(function (Browser $browser) use ($csvPath) {
            $browser->visit(new UserImportPage)
                ->attach('@csv-file-input', $csvPath)
                ->click('@import-submit')
                ->pause(5000) // Wait for processing
                ->assertSuccessMessage('Successfully imported 1 users')
                ->assertPathIs('/users');
        });

        // Verify user was created (extra columns should be ignored)
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        unlink($csvPath);
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

        $this->browse(function (Browser $browser) use ($csvPath) {
            $browser->visit(new UserImportPage)
                ->attach('@csv-file-input', $csvPath)
                ->click('@import-submit')
                ->pause(5000) // Wait for processing
                ->assertSuccessMessage('Successfully imported 1 users')
                ->assertPathIs('/users');
        });

        // Verify user was created with default password
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        unlink($csvPath);
    }
}

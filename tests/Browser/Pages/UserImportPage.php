<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class UserImportPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url()
    {
        return '/users/import';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url())
            ->assertSee('Import Users from CSV')
            ->assertSee('CSV File')
            ->assertSee('Import Users')
            ->assertSee('Back to Users');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@csv-file-input' => 'input[name="csv_file"]',
            '@import-submit' => 'button[type="submit"]',
            '@back-to-users' => 'a[href*="/users"]',
            '@success-message' => '.alert-success',
            '@error-message' => '.alert-danger',
            '@validation-errors' => '.alert-danger ul li',
        ];
    }

    /**
     * Submit the import form.
     */
    public function submitImport(Browser $browser)
    {
        $browser->click('@import-submit');
    }

    /**
     * Go back to users index.
     */
    public function goBackToUsers(Browser $browser)
    {
        $browser->click('@back-to-users');
    }

    /**
     * Assert that success message is displayed.
     */
    public function assertSuccessMessage(Browser $browser, $message = null)
    {
        $browser->assertVisible('@success-message');

        if ($message) {
            $browser->assertSee($message);
        }
    }

    /**
     * Assert that error message is displayed.
     */
    public function assertErrorMessage(Browser $browser, $message = null)
    {
        $browser->assertVisible('@error-message');

        if ($message) {
            $browser->assertSee($message);
        }
    }

    /**
     * Assert that validation errors are displayed.
     */
    public function assertValidationErrors(Browser $browser, $errors = [])
    {
        $browser->assertVisible('@validation-errors');

        foreach ($errors as $error) {
            $browser->assertSee($error);
        }
    }

    /**
     * Assert that the CSV format help is displayed.
     */
    public function assertCsvFormatHelp(Browser $browser)
    {
        $browser->assertSee('CSV Format')
            ->assertSee('Required fields:')
            ->assertSee('name')
            ->assertSee('email')
            ->assertSee('password')
            ->assertSee('Notes:');
    }
}

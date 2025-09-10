<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class UserExportPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url()
    {
        return '/users';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url())
            ->assertSee('Users Management')
            ->assertSee('Add New User')
            ->assertSee('Import Users')
            ->assertSee('Export Users');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@export-button' => 'a[href*="/users/export"]',
            '@import-button' => 'a[href*="/users/import"]',
            '@add-user-button' => 'a[href*="/register"]',
            '@users-table' => 'table',
            '@users-table-body' => 'table tbody',
            '@no-users-message' => 'div.no-users',
            '@success-message' => '.alert-success',
            '@error-message' => '.alert-danger',
        ];
    }

    /**
     * Click the export button.
     */
    public function clickExportButton(Browser $browser)
    {
        $browser->click('@export-button');
    }

    /**
     * Assert that the export button is visible.
     */
    public function assertExportButtonVisible(Browser $browser)
    {
        $browser->assertVisible('@export-button');
    }

    /**
     * Assert that the export button has correct text.
     */
    public function assertExportButtonText(Browser $browser)
    {
        $browser->assertSeeIn('@export-button', 'Export Users');
    }

    /**
     * Assert that users table is visible.
     */
    public function assertUsersTableVisible(Browser $browser)
    {
        $browser->assertVisible('@users-table');
    }

    /**
     * Assert that no users message is visible.
     */
    public function assertNoUsersMessageVisible(Browser $browser)
    {
        $browser->assertVisible('@no-users-message');
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
     * Assert that a specific user is visible in the table.
     */
    public function assertUserInTable(Browser $browser, $userName, $userEmail)
    {
        $browser->assertSeeIn('@users-table-body', $userName)
            ->assertSeeIn('@users-table-body', $userEmail);
    }

    /**
     * Assert that the table has a specific number of users.
     */
    public function assertUserCount(Browser $browser, $expectedCount)
    {
        if ($expectedCount === 0) {
            $browser->assertVisible('@no-users-message');
        } else {
            $browser->assertVisible('@users-table');
            // Count the number of rows in the table body using elements()
            $elements = $browser->elements('table tbody tr');
            $actualCount = count($elements);
            if ($actualCount !== $expectedCount) {
                throw new \Exception("Expected {$expectedCount} users but found {$actualCount}");
            }
        }
    }
}

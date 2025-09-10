<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class UserDeletePage extends Page
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return "/users/{$this->userId}/delete";
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
            ->assertSee('Delete User');
    }

    /**
     * Get the element shortcuts for the page.
     */
    public function elements(): array
    {
        return [
            '@confirm-delete' => 'button[type="submit"]',
            '@cancel-delete' => 'a[href*="/users"]',
            '@user-name' => '.user-name',
            '@user-email' => '.user-email',
            '@success-message' => '.alert-success',
            '@error-message' => '.alert-danger',
        ];
    }

    /**
     * Confirm user deletion.
     */
    public function confirmDelete(Browser $browser): void
    {
        $browser->click('@confirm-delete');
    }

    /**
     * Cancel user deletion.
     */
    public function cancelDelete(Browser $browser): void
    {
        $browser->click('@cancel-delete');
    }

    /**
     * Delete user from users index page.
     */
    public function deleteFromIndex(Browser $browser): void
    {
        $browser->click("form[action*='/users/{$this->userId}'] button[type='submit']");
    }
}

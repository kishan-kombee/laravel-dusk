<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class UserUpdatePage extends Page
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
        return "/users/{$this->userId}/edit";
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
            ->assertSee('Update User');
    }

    /**
     * Get the element shortcuts for the page.
     */
    public function elements(): array
    {
        return [
            '@name' => 'input[name="name"]',
            '@email' => 'input[name="email"]',
            '@password' => 'input[name="password"]',
            '@password_confirmation' => 'input[name="password_confirmation"]',
            '@submit' => 'button[type="submit"]',
            '@success-message' => '.alert-success',
            '@error-message' => '.alert-danger',
            '@back-button' => 'a[href*="/users"]',
        ];
    }

    /**
     * Fill the user update form.
     */
    public function fillUpdateForm(Browser $browser, array $data): void
    {
        $browser->clear('@name')
            ->type('@name', $data['name'])
            ->clear('@email')
            ->type('@email', $data['email']);

        if (isset($data['password']) && ! empty($data['password'])) {
            $browser->type('@password', $data['password']);
        }

        if (isset($data['password_confirmation']) && ! empty($data['password_confirmation'])) {
            $browser->type('@password_confirmation', $data['password_confirmation']);
        }
    }

    /**
     * Submit the user update form.
     */
    public function submitUpdateForm(Browser $browser): void
    {
        $browser->click('@submit');
    }

    /**
     * Go back to users list.
     */
    public function goBack(Browser $browser): void
    {
        $browser->click('@back-button');
    }
}

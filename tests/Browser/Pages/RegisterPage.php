<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class RegisterPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/register';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
            ->assertSee('Register');
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
        ];
    }

    /**
     * Fill the registration form.
     */
    public function fillRegistrationForm(Browser $browser, array $data): void
    {
        $browser->type('@name', $data['name'])
            ->type('@email', $data['email'])
            ->type('@password', $data['password'])
            ->type('@password_confirmation', $data['password_confirmation']);
    }

    /**
     * Submit the registration form.
     */
    public function submitRegistrationForm(Browser $browser): void
    {
        $browser->click('@submit');
    }
}

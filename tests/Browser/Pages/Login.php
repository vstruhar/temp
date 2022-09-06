<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class Login extends Page
{
    /**
     * @param \Laravel\Dusk\Browser $browser
     * @param string                $email
     * @param string                $password
     *
     * @return void
     */
    public function loginUser(Browser $browser, string $email, string $password): void
    {
        $browser->type('@email', $email)
                ->type('@password', $password)
                ->press('@submit');
    }
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/login';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@email'       => '#email',
            '@password'    => '#password',
            '@remember_me' => 'input[name=remember]',
            '@submit'      => 'button[type=submit]',
        ];
    }
}

<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /** @test */
    public function it_should_be_able_to_open_login_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login)
                    ->assertSee('Ponfys');
        });
    }

    /** @test */
    public function it_should_be_able_to_return_error_message_if_user_does_not_exist()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login)
                    ->loginUser('doesnotexist@mail.com', 'password')
                    ->waitUntilEnabled('@submit')
                    ->assertSee('Prihlasovacie údaje nie sú správne');
        });
    }

    /** @test */
    public function it_should_be_able_to_return_error_message_if_invalid_password_is_used()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login)
                    ->loginUser('user@mail.com', 'invalid-password')
                    ->waitUntilEnabled('@submit')
                    ->assertSee('Prihlasovacie údaje nie sú správne');
        });
    }

    /** @test */
    public function it_should_be_able_to_login_an_existing_user()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login)
                    ->loginUser('user@mail.com', 'password')
                    ->pause(2000)
                    ->assertPathIs('/documents/invoices');
        });
    }

    /** @test */
    public function it_should_be_able_to_block_deactivated_user_from_loggin_in()
    {
        $user = User::where('email', 'user@mail.com')->first();

        $user->update(['active' => 0]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new Login)
                    ->loginUser($user->email, 'password')
                    ->waitUntilEnabled('@submit')
                    ->assertSee('Prihlasovacie údaje nie sú správne');
        });

        $user->update(['active' => 1]);
    }
}

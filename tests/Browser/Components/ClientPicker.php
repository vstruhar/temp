<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class ClientPicker extends BaseComponent
{
    /**
     * @param \Laravel\Dusk\Browser $browser
     *
     * @return void
     */
    public function selectFirstClient(Browser $browser): void
    {
        $browser->click('.multiselect__tags')
                ->pause(100)
                ->clickAtPoint(1373, 372)
                ->pause(100)
                ->clickAtPoint(1594, 329);
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return 'div[aria-owns="listbox-inputClient"]';
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param Browser $browser
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector());
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }
}

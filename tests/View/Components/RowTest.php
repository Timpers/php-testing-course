<?php 
use Tests\TestCase;

class RowTest extends TestCase
{
    public function test_header_row_is_rendered()
    {
        $this->blade('<x-row header>This should be rendered in the midddle </x-row>')
        ->assertSee('sticky')
        ->assertSee('This should be rendered in the midddle')
        ->assertSee('bg-gray-200');

        $this->blade('<x-row />')
        ->assertDontSee('sticky')
        ->assertSee('bg-white');
    }
}
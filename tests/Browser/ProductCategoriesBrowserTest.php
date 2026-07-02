<?php

declare(strict_types=1);

use App\Models\ProductCategory;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create([
        'email' => 'categories@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->user->teams()->attach($this->team);
});

function loginAndVisitProductCategories(object $context): mixed
{
    $page = visit('/app/login');

    $page->type('#form\\.email', 'categories@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2);

    return visit('/app/'.$context->team->slug.'/product-categories');
}

it('renders the product categories list page', function (): void {
    $page = loginAndVisitProductCategories($this);

    $page->assertPathIs('/app/'.$this->team->slug.'/product-categories')
        ->assertSee('Product Categories')
        ->assertSee('New Product Category')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('product-categories/list'), fullPage: true);
});

it('displays seeded product categories in the table', function (): void {
    ProductCategory::factory()->for($this->team)->create([
        'name' => 'Electronics Test',
    ]);

    $page = loginAndVisitProductCategories($this);

    $page->assertSee('Electronics Test')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('product-categories/with-data'), fullPage: true);
});

it('renders the product category edit page', function (): void {
    $category = ProductCategory::factory()->for($this->team)->create([
        'name' => 'Edit Category Test',
    ]);

    loginAndVisitProductCategories($this);

    $page = visit('/app/'.$this->team->slug.'/product-categories/'.$category->id.'/edit');

    $page->assertSee('Edit Product Category')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('product-categories/edit'));
});

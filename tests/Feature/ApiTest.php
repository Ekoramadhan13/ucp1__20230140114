<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->category = Category::create(['name' => 'Electronic']);
    }

    public function test_it_can_login_and_get_token()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'message']);
    }

    public function test_it_can_list_products()
    {
        Product::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'name' => 'Laptop',
            'qty' => 10,
            'price' => 5000
        ]);

        $response = $this->getJson('/api/product');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function test_it_can_create_product_with_token()
    {
        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/product', [
            'category_id' => $this->category->id,
            'name' => 'Mouse',
            'quantity' => 50,
            'price' => 100
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'Mouse');
    }

    public function test_it_can_crud_category_with_token()
    {
        $token = $this->user->createToken('test')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        // List
        $this->getJson('/api/category', $headers)->assertStatus(200);

        // Create
        $response = $this->postJson('/api/category', ['name' => 'Books'], $headers);
        $response->assertStatus(201);
        $categoryId = $response->json('data.id');

        // Show
        $this->getJson("/api/category/$categoryId", $headers)->assertStatus(200);

        // Update
        $this->putJson("/api/category/$categoryId", ['name' => 'Stationery'], $headers)
             ->assertStatus(200)
             ->assertJsonPath('data.name', 'Stationery');

        // Delete
        $this->deleteJson("/api/category/$categoryId", [], $headers)->assertStatus(200);
    }
}

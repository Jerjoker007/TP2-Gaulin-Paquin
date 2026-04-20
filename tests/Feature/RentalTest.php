<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class RentalTest extends TestCase
{
    use RefreshDatabase;

    public function test_rental_returnsActiveRentals()
    {
        $this->seed();

        $role = Role::create([ "name" => "user"]);

        $user = User::factory()->create([
            'first_name' => 'Joe',
            'last_name' => 'Bob',
            'email' => 'joe.bob@example.com',
            'phone' => '1234567890',
            'role_id' => $role->id,
            'login' => 'joebob',
            'password' => bcrypt('oldpassword123'),
        ]);

        $this->actingAs($user)->getJson('/api/me/rentals')->assertStatus(200);
    }

    public function test_rental_userNotAuthentificated()
    {
        $this->seed();

        $this->getJson('/api/me/rentals')->assertStatus(401);
    }

    public function test_rental_throttle()
    {
        $this->seed();

        $role = Role::create([ "name" => "user"]);

        $user = User::factory()->create([
            'first_name' => 'Joe',
            'last_name' => 'Bob',
            'email' => 'joe.bob@example.com',
            'phone' => '1234567890',
            'role_id' => $role->id,
            'login' => 'joebob',
            'password' => bcrypt('oldpassword123'),
        ]);

        for ($i = 0; $i < 60; $i++) {
            $response = $this->actingAs($user)->getJson('/api/me/rentals');
            $response->assertStatus(200);
        }

        $response = $this->actingAs($user)->getJson('/api/me/rentals');
        $response->assertStatus(429);
    }
}

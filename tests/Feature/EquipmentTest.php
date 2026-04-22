<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Rental;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    use RefreshDatabase;

    private const CATEGORY = ["name" => "test"];
    
    private const EQUIPMENT = [
        "name" => "test",
        "description" => "test",
        "daily_price" => "10.99"
    ];

    public function test_route_equipment_overflow_is_throttle(): void
    {
        $role = Role::create(["name" => "user"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "category_id" => $category->id
        ];

        for ($i=0; $i < 60; $i++) { 
            $response = $this->postJson('/api/equipment', $json);

            $response->assertStatus(FORBIDDEN);
        }

        $response = $this->postJson('/api/equipment', $json);

        $response->assertStatus(429);
    }

    public function test_route_post_equipment_without_token(): void 
    {
        $response = $this->postJson('/api/equipment', self::EQUIPMENT);

        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_route_post_equipment_with_base_user(): void 
    {
        $role = Role::create(["name" => "user"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "category_id" => $category->id
        ];

        $response = $this->postJson('/api/equipment', $json);

        $response->assertStatus(FORBIDDEN);
    }

    public function test_route_post_equipment_with_missing_field(): void 
    {
        $role = Role::create(["name" => "admin"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );

        $response = $this->postJson('/api/equipment', self::EQUIPMENT);

        $response->assertStatus(INVALID_DATA);
    }

    public function test_route_post_equipment_with_bad_daily_price(): void 
    {
        $role = Role::create(["name" => "admin"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "daily_price" => 0.214,
            "category_id" => $category->id
        ];

        $response = $this->postJson('/api/equipment', $json);

        $response->assertStatus(INVALID_DATA);
    }

    public function test_route_post_equipment_add_to_data_base(): void 
    {
        $role = Role::create(["name" => "admin"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "category_id" => $category->id
        ];

        $response = $this->postJson('/api/equipment', $json);

        $response->assertStatus(CREATED);
        $this->assertDatabaseHas('equipment', self::EQUIPMENT);
    }

    public function test_route_put_equipment_without_token(): void 
    {
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "category_id" => $category->id
        ];
        $equipment = Equipment::create($json);

        $response = $this->putJson("/api/equipment/{$equipment->id}", $json);

        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_route_put_equipment_with_base_user(): void 
    {
        $role = Role::create(["name" => "user"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "category_id" => $category->id
        ];
        $equipment = Equipment::create($json);

        $response = $this->putJson("/api/equipment/{$equipment->id}", $json);

        $response->assertStatus(FORBIDDEN);
    }

    public function test_route_put_equipment_with_missing_field(): void 
    {
        $role = Role::create(["name" => "admin"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $equipment = Equipment::create([...self::EQUIPMENT, "category_id" => $category->id]);

        $response = $this->putJson("/api/equipment/{$equipment->id}", self::EQUIPMENT);

        $response->assertStatus(INVALID_DATA);
    }

    public function test_route_put_equipment_with_bad_daily_price(): void 
    {
        $role = Role::create(["name" => "admin"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "daily_price" => 0.214,
            "category_id" => $category->id
        ];
        $equipment = Equipment::create([...$json, "daily_price" => 0.52]);

        $response = $this->putJson("/api/equipment/{$equipment->id}", $json);

        $response->assertStatus(INVALID_DATA);
    }

    public function test_route_put_equipment_with_bad_id(): void 
    {
        $role = Role::create(["name" => "admin"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "category_id" => $category->id
        ];

        $response = $this->putJson("/api/equipment/1", [...$json, "name" => "New name"]);

        $response->assertStatus(NOT_FOUND);
    }

    public function test_route_put_equipment_change_equipment(): void 
    {
        $role = Role::create(["name" => "admin"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "category_id" => $category->id
        ];
        $equipment = Equipment::create($json);

        $response = $this->putJson("/api/equipment/{$equipment->id}", [...$json, "name" => "New name"]);

        $response->assertStatus(OK);
        $this->assertDatabaseHas('equipment', [...self::EQUIPMENT, "name" => "New name"]);
        $this->assertDatabaseMissing('equipment', self::EQUIPMENT);
    }

    public function test_route_delete_equipment_without_token(): void 
    {
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "category_id" => $category->id
        ];
        $equipment = Equipment::create($json);

        $response = $this->deleteJson("/api/equipment/{$equipment->id}");

        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_route_delete_equipment_with_base_user(): void 
    {
        $role = Role::create(["name" => "user"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "category_id" => $category->id
        ];
        $equipment = Equipment::create($json);

        $response = $this->deleteJson("/api/equipment/{$equipment->id}");

        $response->assertStatus(FORBIDDEN);
    }

    public function test_route_delete_equipment_with_bad_id(): void 
    {
        $role = Role::create(["name" => "admin"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );

        $response = $this->deleteJson("/api/equipment/1");

        $response->assertStatus(NOT_FOUND);
    }

    public function test_route_delete_equipment_used_in_rental(): void 
    {
        $role = Role::create(["name" => "admin"]);
        $user;
        Sanctum::actingAs(
            $user = User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "category_id" => $category->id
        ];
        $equipment = Equipment::create($json);
        Rental::create([
            'equipment_id' => $equipment->id, 
            'user_id' => $user->id, 
            'start_date' => '2026-01-03', 
            'end_date' => '2026-02-01', 
            'total_price' => 0.52
        ]);

        $response = $this->deleteJson("/api/equipment/{$equipment->id}");

        $response->assertStatus(CONFLICT);
        $this->assertDatabaseHas('equipment', self::EQUIPMENT);
    }

    public function test_route_delete_equipment(): void 
    {
        $role = Role::create(["name" => "admin"]);
        Sanctum::actingAs(
            User::factory()->create(["role_id" => $role->id]), ['*']
        );
        $category = Category::create(self::CATEGORY);
        $json = [
            ...self::EQUIPMENT,
            "category_id" => $category->id
        ];
        $equipment = Equipment::create($json);

        $response = $this->deleteJson("/api/equipment/{$equipment->id}");

        $response->assertStatus(NO_CONTENT);
        $this->assertDatabaseMissing('equipment', self::EQUIPMENT);
    }
}

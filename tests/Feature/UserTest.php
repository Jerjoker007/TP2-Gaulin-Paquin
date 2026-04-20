<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_updateUser_patchesTheNewPassword()
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

        $this->actingAs($user)->patchJson('/api/me', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertStatus(200);

        //https://stackoverflow.com/questions/21495502/laravel-hashcheck-always-return-false
        //fresh() pour refresh le modèle avec le nouveau mot de passe hashé
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_user_notAuthentificated()
    {
        $this->seed();
        $this->patchJson('/api/me', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertStatus(401);
    }

    public function test_user_passwordConfirmationDoesNotMatch()
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

        $this->actingAs($user)->patchJson('/api/me', [
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword123',
        ])->assertStatus(422);
    }

    public function test_user_throttle()
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
            $response = $this->actingAs($user)->patchJson('/api/me', [
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);
            $response->assertStatus(200);
        }

        $response = $this->actingAs($user)->patchJson('/api/me', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $response->assertStatus(429);
    }
}

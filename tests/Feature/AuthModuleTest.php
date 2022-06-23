<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthModuleTest extends TestCase
{
    /**
     * Auth module unit test.
     *
     * @return void
     */

    public function test_invalid_register_request()
    {
        $this->json('POST', 'api/auth/register', ['Accept' => 'application/json'])
            ->assertStatus(422);
    }

    public function test_successfull_registration()
    {
        $userData = [
            "first_name" => "Test First Name",
            "last_name" => "Test Last Name",
            "email" => "testss@example.com",
            "password" => "demo12345",
            "password_confirmation" => "demo12345"
        ];

        $this->json('POST', 'api/auth/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(200);
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $response = $this->post('api/auth/login', [
            'email' => "tests@example.com",
            'password' => "demo12345",
        ]);

        $response->assertStatus(200);
    }
}

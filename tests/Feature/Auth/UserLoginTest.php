<?php

use App\Models\User;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    protected $user;

    // SETUP

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    // ASSERTIONS

    public function test_user_can_login(): void
    {
        // Arrange
        $user = $this->user;

        // Act
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token_access',
            'token_type',
        ]);
    }

    // FAILING TESTS

    public function test_user_cannot_login_with_invalid_email(): void
    {
        // Arrange
        $user = $this->user;

        // Act
        $response = $this->postJson('/api/v1/login', [
            'email' => 'invalid_email',
            'password' => 'password',
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'The email must be a valid email address.'
            ]
        ]);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        // Arrange
        $user = $this->user;

        // Act
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => '',
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'The password field is required.'
            ]
        ]);
    }

    public function test_user_cannot_login_with_wrong_email(): void
    {
        // Arrange
        $user = $this->user;

        // Act
        $response = $this->postJson('/api/v1/login', [
            'email' => 'wrong.email@email.dom',
            'password' => 'password',
        ]);

        // Assert
        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [
                'Invalid credentials'
            ]
        ]);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        // Arrange
        $user = $this->user;

        // Act
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        // Assert
        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [
                'Invalid credentials'
            ]
        ]);
    }
}

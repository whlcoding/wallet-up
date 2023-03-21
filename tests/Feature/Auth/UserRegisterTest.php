<?php

use Tests\TestCase;

class UserRegisterTest extends TestCase
{

    // ASSERTIONS

    public function test_user_can_register(): void
    {
        // Arrange
        $user = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'fake.user@walletup.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        // Act
        $response = $this->postJson('/api/v1/register', $user);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'token_access',
            'token_type',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $user['email']
        ]);
    }

    // FAILING TESTS

    public function test_user_cannot_register_with_invalid_email(): void
    {
        // Arrange
        $user = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid_email',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        // Act
        $response = $this->postJson('/api/v1/register', $user);

        // Assert
        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'The email must be a valid email address.'
            ]
        ]);
    }

    public function test_user_cannot_register_with_different_password(): void
    {
        // Arrange
        $user = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'fake.user@walletup.com',
            'password' => 'password',
            'password_confirmation' => 'different_password'
        ];

        // Act
        $response = $this->postJson('/api/v1/register', $user);

        // Assert
        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'The password confirmation and password must match.'
            ]
        ]);
    }

    public function test_user_cannot_register_without_required_fields(): void
    {
        // Arrange
        $user = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => '',
            // 'password_confirmation' => '' it's not necessary
        ];

        // Act
        $response = $this->postJson('/api/v1/register', $user);

        // Assert
        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'The first name field is required.',
                'The last name field is required.',
                'The email field is required.',
                'The password field is required.'
            ]
        ]);
    }
}


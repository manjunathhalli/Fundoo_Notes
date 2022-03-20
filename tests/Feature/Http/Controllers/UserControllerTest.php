<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * Successfull registration
     *
     * @test
     */
    public function test_SuccessfulRegistration()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/auth/register', [
                "first_name" => "Manju",
                "last_name" => "Halli",
                "email" => "manju123@gmail.com",
                "password" => "123456",
                "confirm_password" => "123456"
            ]);

        $response->assertStatus(200)->assertJson(['message' => 'User successfully registered']);
    }

    /**
     * @test for
     * Already Registered User
     */
    public function test_If_User_Already_Registered()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/auth/register', [
                "firstname" => "Manju",
                "lastname" => "Halli",
                "email" => "manju123@gmail.com",
                "password" => "123456",
                "confirm_password" => "123456"
            ]);
        $response->assertStatus(200)->assertJson(['message' => 'The email has already been taken']);
    }

    /**
     * @test for
     * Successfull login
     */

    public function test_SuccessfulLogin()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json(
            'POST',
            '/api/auth/login',
            [
                "email" => "manju123@gmail.com",
                "password" => "123456"
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Login successfull']);
    }

    /**
     * @test for
     * Unsuccessfull Login
     */

    public function test_UnSuccessfulLogin()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json(
            'POST',
            '/api/auth/login',
            [
                "email" => "manju567@gmail.com",
                "password" => "manj7899"
            ]
        );
        $response->assertStatus(401)->assertJson(['message' => 'email not found register first']);
    }

    /**
     * @test for
     * Successfull Logout
     */
    public function test_SuccessfulLogout()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwNDUyMiwiZXhwIjoxNjQ3NzA4MTIyLCJuYmYiOjE2NDc3MDQ1MjIsImp0aSI6Im11OHlBQUEwendXYWw1MDEiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.duX6pRe2-Dkw26I39KGyhrJSoDgz4w6Bp6C72X9dj-o'
        ])->json('POST', '/api/auth/logout');
        $response->assertStatus(201)->assertJson(['message' => 'User successfully logget out']);
    }

    /**
     * @test for
     * Successfull forgotpassword
     */
    public function test_SuccessfulForgotPassword()
    { {
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/auth/forgotpassword', [
                "email" => "manju123@gmail.com"
            ]);

            $response->assertStatus(200)->assertJson(['message' => 'password reset link genereted in mail']);
        }
    }
    /**
     * @test for
     * UnSuccessfull forgotpassword
     */
    public function test_IfGiven_InvalidEmailId()
    { {
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/auth/forgotpassword', [
                "email" => "mannjuuo56@gmail.com"
            ]);

            $response->assertStatus(404)->assertJson(['message' => 'we can not find a user with that email address']);
        }
    }
    /**
     * @test for
     * Successfull resetpassword
     */
    public function test_SuccessfulResetPassword()
    { {
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/auth/resetpassword', [
                "new_password" => "manju543",
                "confirm_password" => "manju543",
                "token" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NzcwNDUyMiwiZXhwIjoxNjQ3NzA4MTIyLCJuYmYiOjE2NDc3MDQ1MjIsImp0aSI6Im11OHlBQUEwendXYWw1MDEiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.duX6pRe2-Dkw26I39KGyhrJSoDgz4w6Bp6C72X9dj-o"
            ]);

            $response->assertStatus(201)->assertJson(['message' => 'Password reset successfull!']);
        }
    }
}

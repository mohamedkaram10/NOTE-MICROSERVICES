<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testUserCanRegister(): void
    {
        $this->postJson('api/register', [
            'name' => 'mohamed',
            'email' => 'mohamed@gmail.com',
            'password' => 'secret123',
        ])->assertOk();

        $this->assertDatabaseHas('users', ['name' => 'mohamed']);
    }

    public function testAUserCanLoginWithEmailAndPassword()
    {
        $user = User::factory()->create([
            'password' => '123',
        ]);
        $response = $this->post('login', [
            'email' => $user->email,
            'password' => '123',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($user->id, $response->json('user.id'));
        $this->assertEquals($user->email, $response->json('user.email'));
        $this->assertNotEmpty($response->json('authorization.token'));
        $token = $response->json('authorization.token');
        auth()->forgetGuards();
        app('tymon.jwt')->unsetToken();

        $response = $this
            ->get('api/users/'.$user->id);
        $response->assertStatus(401);
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get('api/users/'.$user->id);
        $response->assertStatus(200);
    }
}

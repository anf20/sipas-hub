<?php

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;

uses(RefreshDatabase::class);

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});

test('users can authenticate using the login screen', function () {
    $this->withoutMiddleware([PreventRequestForgery::class]);
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $this->withoutMiddleware([PreventRequestForgery::class]);
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();

    $this->assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    $this->withoutMiddleware([PreventRequestForgery::class]);
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    $user = User::factory()->withTwoFactor()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('users can logout', function () {
    $this->withoutMiddleware([PreventRequestForgery::class]);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});

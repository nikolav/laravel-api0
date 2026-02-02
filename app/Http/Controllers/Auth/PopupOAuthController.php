<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
// use Laravel\Socialite\Two\AbstractProvider;

class PopupOAuthController extends Controller
{
  public function redirect(Request $request, string $provider)
  {
    abort_unless(in_array($provider, config('app.oauth_providers_supported'), true), 404);

    /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
    $driver = Socialite::driver($provider);

    // Provider-specific scopes (optional but useful)
    if ('google' === $provider) {
      $driver->scopes(['openid', 'profile', 'email']);
    }
    // if ($provider === 'github') {
    //   # email can be hidden otherwise
    //   $driver->scopes(['read:user', 'user:email']);
    // }

    return $driver->redirect();
  }

  public function callback(Request $request, string $provider)
  {
    abort_unless(in_array($provider, config('app.oauth_providers_supported'), true), 404);

    $social = Socialite::driver($provider)->user();

    $providerId = $social->getId();
    // can be null (FB / GH edge cases)
    $email      = $social->getEmail();
    $avatar     = $social->getAvatar();

    // user by provider link
    $user = User::where([
      'provider'    => $provider,
      'provider_id' => $providerId,
    ])->first();

    // user by email if present
    if (!$user && $email) {
      $user = User::where(['email' => $email])->first();
    }

    if (!$user) {
      // no user; add
      $user = User::create([
        'email'       => $email ?? (Str::uuid() . '@no-email.local'),
        'password'    => bcrypt(Str::random(40)),
        'provider'    => $provider,
        'provider_id' => $providerId,
        'avatar'      => $avatar,
      ]);
    } else {
      // update social fields for existing user
      $user->forceFill([
        'provider'    => $provider,
        'provider_id' => $providerId,
        'avatar'      => $avatar,
      ])->save();
    }

    // sanctum bearer token
    $token = $user->createToken('access_token', ['*'])->plainTextToken;

    // html page that sends token to opener and closes
    $frontendOrigin = config('app.frontend_origin');

    return response(
      Blade::render('auth.oauth-response-popup', [
        'origin' => $frontendOrigin,
        'tok'    => $token,
      ])
    )
      ->header('Content-Type', 'text/html; charset=UTF-8')
      ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
  }
}

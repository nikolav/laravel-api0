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
  private array $allowedProviders = ['google',];

  public function redirect(Request $request, string $provider)
  {
    abort_unless(in_array($provider, $this->allowedProviders, true), 404);

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
    abort_unless(in_array($provider, $this->allowedProviders, true), 404);

    $social = Socialite::driver($provider)->user();

    $providerId = $social->getId();
    // can be null (FB / GH edge cases)
    $email      = $social->getEmail();
    $avatar     = $social->getAvatar();

    // Find by provider link first
    $user = User::where([
      'provider'    => $provider,
      'provider_id' => $providerId,
    ])->first();

    // Optional: link to an existing user by email if email is present
    if (!$user && $email) {
      $user = User::where('email', $email)->first();
    }

    if (!$user) {
      $user = User::create([
        'email'       => $email ?? (Str::uuid() . '@no-email.local'),
        'password'    => bcrypt(Str::random(40)),
        'provider'    => $provider,
        'provider_id' => $providerId,
        'avatar'      => $avatar,
      ]);
    } else {
      $user->forceFill([
        'provider'    => $provider,
        'provider_id' => $providerId,
        'avatar'      => $avatar,
      ])->save();
    }

    // sanctum bearer token
    $token = $user->createToken('oauth-' . $provider, ['*'])->plainTextToken;

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

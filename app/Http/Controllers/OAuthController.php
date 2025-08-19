<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    private array $allowed = ['github', 'google'];

    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, $this->allowed, true), 404);

        $driver = Socialite::driver($provider);

        if ($provider === 'google') {
            $driver = $driver->scopes(['openid', 'profile', 'email']);
        }

        return $driver->redirect();
    }

    public function callback(string $provider)
    {
        abort_unless(in_array($provider, $this->allowed, true), 404);

        try {
            $oauth = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('login')->withErrors(ucfirst($provider) . ' auth failed.');
        }

        $providerId = (string) $oauth->getId();
        $email = $oauth->getEmail(); // can be null (e.g., hidden email)
        $name = $oauth->getName() ?: $oauth->getNickname() ?: ucfirst($provider) . ' User';

        // Resolve user: already linked → by email → new
        $user = User::where([
            'provider' => $provider,
            'provider_id' => $providerId,
        ])->first()
            ?? ($email ? User::where('email', $email)->first() : null)
            ?? new User();

        // Always refresh provider fields/tokens
        $user->forceFill([
            'provider' => $provider,
            'provider_id' => $providerId,
            'token' => $oauth->token ?? null,
            'refresh_token' => $oauth->refreshToken ?? null,
            'expires_in' => $oauth->expiresIn ?? null,
        ]);

        // Only fill identity if missing
        if (blank($user->name))
            $user->name = $name;
        if (blank($user->email))
            $user->email = $email ?? (Str::random(5) . '@users.noreply.' . $provider . '.com');

        // Ensure password exists (random placeholder for OAuth users)
        if (blank($user->password))
            $user->password = bcrypt(Str::random(32));

        $user->save();

        Auth::login($user, remember: true);

        return redirect()->intended(route('home'));
    }
}

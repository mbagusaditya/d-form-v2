<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    /**
     * Redirect to Google OAuth provider
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // First check if user exists with this Google ID
            $user = User::where('google_id', $googleUser->getId())->first();

            // If not found, check by email
            if (! $user) {
                $user = User::where('email', $googleUser->getEmail())->first();
            }

            if ($user) {
                // Update existing user with Google ID and avatar if not set
                if (! $user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                }
                if (! $user->avatar) {
                    $user->update([
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }

                Auth::login($user);

                Notification::make()
                    ->success()
                    ->title(explode(', ', __('auth.login_success'))[0])
                    ->body(explode(', ', __('auth.login_success'))[1])
                    ->send();

                return redirect()->intended('dashboard');
            }

            // Create new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => bcrypt(Str::random(32)), // Random password since OAuth users don't need it
                'email_verified_at' => now(),
            ]);

            Auth::login($user);

            Notification::make()
                ->success()
                ->title(explode(', ', __('auth.login_success'))[0])
                ->body(explode(', ', __('auth.login_success'))[1])
                ->send();

            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Authentication Failed')
                ->body('Unable to login with Google. Please try again.')
                ->send();

            return redirect()->route('auth.login');
        }
    }

    /**
     * Redirect to GitHub OAuth provider
     */
    public function redirectToGithub(): RedirectResponse
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Handle GitHub OAuth callback
     */
    public function handleGithubCallback(): RedirectResponse
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            // First check if user exists with this GitHub ID
            $user = User::where('github_id', $githubUser->getId())->first();

            // If not found, check by email (only if email is provided)
            if (! $user && $githubUser->getEmail()) {
                $user = User::where('email', $githubUser->getEmail())->first();
            }

            if ($user) {
                // Update existing user with GitHub ID and avatar if not set
                if (! $user->github_id) {
                    $user->update([
                        'github_id' => $githubUser->getId(),
                    ]);
                }
                if (! $user->avatar) {
                    $user->update([
                        'avatar' => $githubUser->getAvatar(),
                    ]);
                }

                Auth::login($user);

                Notification::make()
                    ->success()
                    ->title(explode(', ', __('auth.login_success'))[0])
                    ->body(explode(', ', __('auth.login_success'))[1])
                    ->send();

                return redirect()->intended('dashboard');
            }

            // Create new user
            // Note: GitHub may not always provide email if user hasn't made it public
            $user = User::create([
                'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                'email' => $githubUser->getEmail() ?? $githubUser->getNickname() . '@github.local',
                'github_id' => $githubUser->getId(),
                'avatar' => $githubUser->getAvatar(),
                'password' => bcrypt(Str::random(32)), // Random password since OAuth users don't need it
                'email_verified_at' => $githubUser->getEmail() ? now() : null,
            ]);

            Auth::login($user);

            Notification::make()
                ->success()
                ->title(explode(', ', __('auth.login_success'))[0])
                ->body(explode(', ', __('auth.login_success'))[1])
                ->send();

            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Authentication Failed')
                ->body('Unable to login with GitHub. Please try again.')
                ->send();

            return redirect()->route('auth.login');
        }
    }
}

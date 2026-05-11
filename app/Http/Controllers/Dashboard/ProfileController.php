<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDashboardProfileAvatarRequest;
use App\Http\Requests\UpdateDashboardProfilePasswordRequest;
use App\Http\Requests\UpdateDashboardProfileRequest;
use App\Services\User\UserAvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function update(UpdateDashboardProfileRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $validated = $request->validated();

        if (($validated['email'] ?? null) !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        $user->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Profile updated successfully.',
        ]);

        return redirect()->back();
    }

    public function updateAvatar(UpdateDashboardProfileAvatarRequest $request, UserAvatarService $avatars): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $file = $request->file('avatar');
        if (! $file instanceof UploadedFile) {
            return redirect()->back();
        }

        $avatars->update($user, $file);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Foto profil berhasil diperbarui.',
        ]);

        return redirect()->back();
    }

    public function destroyAvatar(Request $request, UserAvatarService $avatars): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $avatars->clear($user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Foto profil dihapus.',
        ]);

        return redirect()->back();
    }

    public function updatePassword(UpdateDashboardProfilePasswordRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->password = $request->validated('password');
        $user->save();

        $request->session()->regenerate();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Password updated successfully.',
        ]);

        return redirect()->back();
    }
}

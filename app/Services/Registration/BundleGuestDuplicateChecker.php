<?php

namespace App\Services\Registration;

use App\Models\Form;
use App\Models\FormAnswer;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final class BundleGuestDuplicateChecker
{
    public function assertEmailAvailableForForm(Form $form, string $email, string $errorKey = 'team_member_emails'): void
    {
        $emailLower = mb_strtolower(trim($email));

        if ($emailLower === '') {
            return;
        }

        if (FormAnswer::query()
            ->where('form_id', $form->id)
            ->whereRaw('LOWER(invited_email) = ?', [$emailLower])
            ->excludeRejectedSubmissions()
            ->exists()) {
            throw ValidationException::withMessages([
                $errorKey => __('A participant with this email is already registered for this form.'),
            ]);
        }

        $linkedUser = User::query()->whereRaw('LOWER(email) = ?', [$emailLower])->first();

        if ($linkedUser !== null
            && FormAnswer::query()
                ->where('form_id', $form->id)
                ->where('user_id', $linkedUser->id)
                ->excludeRejectedSubmissions()
                ->exists()) {
            throw ValidationException::withMessages([
                $errorKey => __('A participant with this email is already registered for this form.'),
            ]);
        }
    }
}

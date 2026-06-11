<?php

namespace App\Services\Registration;

use App\Models\FormAnswer;

final class BundleGuestDisplayNameResolver
{
    public function resolve(FormAnswer $submission): string
    {
        if ($submission->user !== null && $submission->user->name !== '') {
            return $submission->user->name;
        }

        $answers = is_array($submission->answers) ? $submission->answers : [];

        foreach ($answers as $value) {
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        $email = $submission->invited_email;

        if (is_string($email) && $email !== '') {
            return $email;
        }

        return __('Participant');
    }
}

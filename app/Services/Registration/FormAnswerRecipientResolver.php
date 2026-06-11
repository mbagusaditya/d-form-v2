<?php

namespace App\Services\Registration;

use App\Models\FormAnswer;

final class FormAnswerRecipientResolver
{
    public function email(FormAnswer $submission): ?string
    {
        $fromUser = $submission->user?->email;

        if (is_string($fromUser) && $fromUser !== '') {
            return $fromUser;
        }

        $invited = $submission->invited_email;

        if (is_string($invited) && $invited !== '') {
            return $invited;
        }

        return null;
    }

    public function userIdForLog(FormAnswer $submission): ?string
    {
        $id = $submission->user_id;

        return is_string($id) && $id !== '' ? $id : null;
    }
}

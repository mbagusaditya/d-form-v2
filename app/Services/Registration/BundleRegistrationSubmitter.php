<?php

namespace App\Services\Registration;

use App\Enums\MemberConfirmationStatus;
use App\Enums\RegistrationRole;
use App\Exceptions\QuotaExceededException;
use App\Models\Event;
use App\Models\Form;
use App\Models\FormAnswer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class BundleRegistrationSubmitter
{
    public static function isBundleForm(Form $form): bool
    {
        $metadata = $form->metadata;
        if (! is_array($metadata)) {
            return false;
        }

        $mode = $metadata['registration_mode'] ?? null;

        return is_string($mode) && strtolower($mode) === 'bundle';
    }

    /**
     * @param  list<array<string, mixed>>  $memberAnswerRows  one merged answers payload per member (same keys as leader)
     * @param  list<User>  $memberUsers
     * @return array{leader: FormAnswer, members: list<FormAnswer>}
     */
    public function submit(
        Form $form,
        Event $event,
        User $leaderUser,
        array $leaderAnswers,
        array $memberAnswerRows,
        array $memberUsers,
        bool $adminExemptFromQuota,
    ): array {
        $teamSize = TeamRegistrationSubmitter::resolveTeamSize($form);

        if ($teamSize < 2) {
            throw ValidationException::withMessages([
                'team_member_emails' => __('This bundle form is misconfigured (team size). Contact the organizer.'),
            ]);
        }

        if (count($memberUsers) !== $teamSize - 1 || count($memberAnswerRows) !== count($memberUsers)) {
            throw ValidationException::withMessages([
                'team_member_emails' => __('Bundle participants were misconfigured. Try again or contact the organizer.'),
            ]);
        }

        $participants = array_merge([$leaderUser], $memberUsers);
        foreach ($participants as $participant) {
            if (FormAnswer::query()
                ->where('form_id', $form->id)
                ->where('user_id', $participant->id)
                ->exists()) {
                throw ValidationException::withMessages([
                    'team_member_emails' => __('A participant is already registered for this form.'),
                ]);
            }
        }

        $memberAnswerRows = array_map(
            fn (array $row) => json_decode(json_encode($row, JSON_THROW_ON_ERROR), true),
            $memberAnswerRows
        );

        return DB::transaction(function () use (
            $form,
            $event,
            $leaderUser,
            $memberUsers,
            $leaderAnswers,
            $memberAnswerRows,
            $adminExemptFromQuota,
            $teamSize
        ): array {
            $lockedEvent = Event::query()->lockForUpdate()->find($event->id);
            if ($lockedEvent === null) {
                throw new \RuntimeException('Event not found.');
            }

            if (! $adminExemptFromQuota
                && $lockedEvent->quota !== null
                && $lockedEvent->quota > 0
                && $lockedEvent->registered_count + $teamSize > $lockedEvent->quota) {
                throw new QuotaExceededException();
            }

            $groupToken = $this->generateUniqueGroupToken((string) $form->id);
            $inviteExpiresAt = now()->addDays((int) config('registration.invitation_ttl_days', 7));

            $leader = FormAnswer::query()->create([
                'answers' => $leaderAnswers,
                'form_id' => $form->id,
                'user_id' => (string) $leaderUser->id,
                'leader_form_answer_id' => null,
                'registration_role' => RegistrationRole::Leader,
                'member_confirmation_status' => MemberConfirmationStatus::Accepted,
                'member_confirmed_at' => now(),
                'invitation_token' => null,
                'group_token' => $groupToken,
                'invited_email' => null,
                'invitation_expired_at' => null,
            ]);

            $memberRows = [];

            foreach ($memberUsers as $i => $memberUser) {
                $memberRows[] = FormAnswer::query()->create([
                    'answers' => $memberAnswerRows[$i],
                    'form_id' => $form->id,
                    'user_id' => (string) $memberUser->id,
                    'leader_form_answer_id' => $leader->id,
                    'registration_role' => RegistrationRole::Member,
                    'member_confirmation_status' => MemberConfirmationStatus::Pending,
                    'invitation_token' => $this->generateUniqueInvitationToken(),
                    'group_token' => $groupToken,
                    'invited_email' => $memberUser->email,
                    'invitation_expired_at' => $inviteExpiresAt,
                ]);
            }

            $lockedEvent->increment('registered_count', $teamSize);

            return [
                'leader' => $leader->fresh(),
                'members' => array_map(fn (FormAnswer $r) => $r->fresh(), $memberRows),
            ];
        });
    }

    private function generateUniqueGroupToken(string $formId): string
    {
        for ($i = 0; $i < 48; $i++) {
            $token = strtoupper(Str::random(8));
            if (! FormAnswer::query()->where('form_id', $formId)->where('group_token', $token)->exists()) {
                return $token;
            }
        }

        throw new \RuntimeException('Could not generate a unique group token.');
    }

    private function generateUniqueInvitationToken(): string
    {
        for ($i = 0; $i < 32; $i++) {
            $token = Str::random(48);
            if (! FormAnswer::query()->where('invitation_token', $token)->exists()) {
                return $token;
            }
        }

        throw new \RuntimeException('Could not generate a unique invitation token.');
    }
}

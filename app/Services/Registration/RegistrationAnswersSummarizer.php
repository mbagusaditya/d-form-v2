<?php

namespace App\Services\Registration;

use App\Models\FormAnswer;
use App\Models\FormField;
use Illuminate\Support\Facades\Storage;

final class RegistrationAnswersSummarizer
{
    /**
     * @return array<string, string>
     */
    public function summarizeForPortal(FormAnswer $submission): array
    {
        return $this->summarize($submission);
    }

    /**
     * @return array<string, string>
     */
    public function summarize(FormAnswer $submission): array
    {
        $answers = is_array($submission->answers) ? $submission->answers : [];

        $fields = FormField::query()
            ->where('form_id', $submission->form_id)
            ->orderBy('order')
            ->get(['name', 'label', 'input_type', 'metadata']);

        $lines = [];
        foreach ($fields as $field) {
            if (! array_key_exists($field->name, $answers)) {
                continue;
            }
            $value = $answers[$field->name];

            if ($field->input_type === 'fileUpload') {
                $lines[$field->label] = $this->fileFieldSummary($value);

                continue;
            }

            if (is_array($value)) {
                $lines[$field->label] = implode(', ', array_map(fn ($v) => (string) $v, $value));

                continue;
            }

            if ($value === null || $value === '') {
                $lines[$field->label] = '—';
            } else {
                $lines[$field->label] = (string) $value;
            }
        }

        return $lines;
    }

    private function fileFieldSummary(mixed $value): string
    {
        if (! is_string($value) || $value === '') {
            return '—';
        }

        try {
            return Storage::disk('public')->url($value);
        } catch (\Throwable) {
            return __('File uploaded');
        }
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDashboardProfileAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'avatar.required' => 'Pilih berkas gambar terlebih dahulu.',
            'avatar.image' => 'Unggahan harus berupa gambar.',
            'avatar.mimes' => 'Gunakan JPEG, PNG, GIF, atau WebP.',
            'avatar.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}

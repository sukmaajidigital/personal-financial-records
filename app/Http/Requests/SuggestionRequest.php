<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuggestionRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'focus' => ['required', 'string', Rule::in([
                'fitur_baru',
                'pengurangan_fitur',
                'tampilan',
                'performa',
                'keamanan',
                'lainnya',
            ])],
            'image' => ['nullable', 'image', 'max:2048'], // max 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul saran wajib diisi.',
            'title.max' => 'Judul maksimal 255 karakter.',
            'description.required' => 'Deskripsi saran wajib diisi.',
            'description.max' => 'Deskripsi maksimal 5000 karakter.',
            'focus.required' => 'Fokus saran wajib dipilih.',
            'focus.in' => 'Fokus saran tidak valid.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}

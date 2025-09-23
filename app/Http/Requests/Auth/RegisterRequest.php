<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 
                'confirmed',
                function ($attribute, $value, $fail) {
                    $validation = User::validatePasswordStrength($value);
                    if (!$validation['valid']) {
                        $fail('Password tidak memenuhi kriteria keselamatan: ' . implode(', ', $validation['errors']));
                    }
                    if ($validation['strength'] < 60) {
                        $fail('Password terlalu lemah. Skor kekuatan: ' . $validation['strength'] . '/100');
                    }
                }
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama diperlukan.',
            'name.max' => 'Nama tidak boleh melebihi 255 aksara.',
            'email.required' => 'Email diperlukan.',
            'email.email' => 'Format email tidak sah.',
            'email.unique' => 'Email ini telah digunakan.',
            'password.required' => 'Password diperlukan.',
            'password.confirmed' => 'Pengesahan password tidak sepadan.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $risdaStaf = $user->risdaStaf;

        // Base rules for all users
        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ];

        // Additional rules if user has RISDA Staf record
        if ($risdaStaf) {
            $rules = array_merge($rules, [
                'no_pekerja' => ['required', 'string', 'max:50'],
                'nama_penuh' => ['required', 'string', 'max:255'],
                'no_kad_pengenalan' => ['required', 'string', 'max:14'],
                'jantina' => ['required', 'in:lelaki,perempuan'],
                'jawatan' => ['required', 'string', 'max:100'],
                'no_telefon' => ['required', 'string', 'max:20'],
                'no_fax' => ['nullable', 'string', 'max:20'],
                'alamat_1' => ['required', 'string', 'max:255'],
                'alamat_2' => ['nullable', 'string', 'max:255'],
                'poskod' => ['required', 'string', 'size:5'],
                'bandar' => ['required', 'string', 'max:100'],
                'negeri' => ['required', 'string', 'max:100'],
                'negara' => ['required', 'string', 'max:100'],
            ]);
        }

        return $rules;
    }
}

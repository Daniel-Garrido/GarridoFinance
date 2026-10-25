<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],

            // dígitos, espacios, guiones, paréntesis y "+" (ej. +52 81 2345 6789)
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-()]{7,}$/'],
        ], [
            'name.required' => 'Escribe tu nombre.',
            'email.required' => 'Escribe tu correo electrónico.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'Ese correo ya está registrado en otra cuenta.',
            'phone.regex' => 'El teléfono solo puede tener números, espacios, guiones, paréntesis y "+".',
        ])->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'last_name' => $input['last_name'] ?? null,
                'email' => $input['email'],
                'phone' => $input['phone'] ?? null,
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'last_name' => $input['last_name'] ?? null,
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}

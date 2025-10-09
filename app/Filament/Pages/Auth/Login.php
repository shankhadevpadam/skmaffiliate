<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as AuthLogin;

class Login extends AuthLogin
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => true,
        ];
    }
}

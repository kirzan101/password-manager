<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use InvalidArgumentException;

/**
 * Cast for encrypting and decrypting credential secrets.
 */
class EncryptedCredentialSecret implements CastsAttributes
{
    /**
     * Get the decrypted value of the credential secret.
     */
    public function get(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    ): ?string {
        if ($value === null) {
            return null;
        }

        return app('credentials.encrypter')
            ->decryptString($value);
    }

    /**
     * Set the encrypted value of the credential secret.
     */
    public function set(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    ): ?string {
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException(
                'Credential secret must be a string.'
            );
        }

        return app('credentials.encrypter')
            ->encryptString($value);
    }
}

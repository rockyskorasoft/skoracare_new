<?php

namespace App\Support;

use Throwable;

class SecureRouteParameter
{
    private const CIPHER = 'AES-256-CBC';

    private const IV_BYTES = 16;

    private const MAC_BYTES = 32;

    /**
     * Convert an internal numeric id into a URL-safe encrypted parameter.
     */
    public static function encode(int|string $value): string
    {
        $iv = random_bytes(self::IV_BYTES);
        $encryptedValue = openssl_encrypt((string) $value, self::CIPHER, self::key(), OPENSSL_RAW_DATA, $iv);

        if ($encryptedValue === false) {
            throw new \RuntimeException('Unable to create secure route parameter.');
        }

        $mac = hash_hmac('sha256', $iv.$encryptedValue, self::key(), true);

        return bin2hex($iv.$mac.$encryptedValue);
    }

    /**
     * Convert a URL-safe encrypted parameter back to a numeric id.
     */
    public static function decode(string $value): ?string
    {
        if (ctype_digit((string) $value)) {
            return (string) $value;
        }

        try {
            $encryptedValue = self::encryptedValueFromRoute($value);

            if (! $encryptedValue) {
                return null;
            }

            $decryptedValue = self::decryptRouteValue($encryptedValue);

            return ctype_digit((string) $decryptedValue) ? (string) $decryptedValue : null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Decode a route token or return a 404 for invalid/tampered values.
     */
    public static function decodeOrFail(?string $value): string
    {
        if (! $value) {
            abort(404);
        }

        return self::decode($value) ?? abort(404);
    }

    /**
     * Resolve current lowercase hex tokens and older base64-url tokens.
     */
    private static function encryptedValueFromRoute(string $value): ?string
    {
        if (ctype_xdigit($value) && strlen($value) % 2 === 0) {
            $encryptedValue = hex2bin($value);

            return $encryptedValue === false ? null : $encryptedValue;
        }

        $encryptedValue = base64_decode(strtr(self::pad($value), '-_', '+/'), true);

        return $encryptedValue === false ? null : $encryptedValue;
    }

    /**
     * Decrypt current compact tokens, with fallback for older Laravel tokens.
     */
    private static function decryptRouteValue(string $value): ?string
    {
        $minimumLength = self::IV_BYTES + self::MAC_BYTES + 1;

        if (strlen($value) >= $minimumLength) {
            $iv = substr($value, 0, self::IV_BYTES);
            $mac = substr($value, self::IV_BYTES, self::MAC_BYTES);
            $encryptedValue = substr($value, self::IV_BYTES + self::MAC_BYTES);
            $expectedMac = hash_hmac('sha256', $iv.$encryptedValue, self::key(), true);

            if (hash_equals($expectedMac, $mac)) {
                $decryptedValue = openssl_decrypt($encryptedValue, self::CIPHER, self::key(), OPENSSL_RAW_DATA, $iv);

                return $decryptedValue === false ? null : $decryptedValue;
            }
        }

        return decrypt($value);
    }

    /**
     * Derive a binary encryption key from the Laravel app key.
     */
    private static function key(): string
    {
        $key = config('app.key');

        if (str_starts_with($key, 'base64:')) {
            $decodedKey = base64_decode(substr($key, 7), true);

            if ($decodedKey !== false) {
                return hash('sha256', $decodedKey, true);
            }
        }

        return hash('sha256', $key, true);
    }

    /**
     * Restore removed base64 padding before decoding old route tokens.
     */
    private static function pad(string $value): string
    {
        return $value.str_repeat('=', (4 - strlen($value) % 4) % 4);
    }
}

<?php
namespace Pi\Oauth\Provider\Utility;

/**
 * Implements JWT encoding and decoding as per http://tools.ietf.org/html/draft-ietf-oauth-json-web-token-06
 * Encoding alogrithm based on http://code.google.com/p/google-api-php-client
 * Decoding alogithm based on https://github.com/luciferous/jwt
 * @author F21
 * @see https://github.com/F21/jwt
 */
class Jwt
{
    public static function encode($payload, $key, $algo = 'HS256')
    {
        $header = array(
            'typ'   => 'JWT',
            'alg'   => $algo
        );

        $segments = array(
            static::urlsafeB64Encode(json_encode($header)),
            static::urlsafeB64Encode(json_encode($payload))
        );

        $signing_input = implode('.', $segments);

        $signature = static::sign($signing_input, $key, $algo);
        $segments[] = static::urlsafeB64Encode($signature);

        return implode('.', $segments);
    }

    public static function decode($jwt, $key = null, $verify = true)
    {
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            throw new \Exception('Wrong number of segments');
        }

        list($headb64, $payloadb64, $cryptob64) = $tks;
        if (null === ($header = json_decode(static::urlsafeB64Decode($headb64)))) {
            throw new \Exception('Invalid segment encoding');
        }

        if (null === $payload = json_decode(static::urlsafeB64Decode($payloadb64))) {
            throw new \Exception('Invalid segment encoding');
        }

        $sig = static::urlsafeB64Decode($cryptob64);

        if ($verify) {
            if (empty($header->alg)) {
                throw new \DomainException('Empty algorithm');
            }

            if (!static::verifySignature($sig, "$headb64.$payloadb64", $key, $header->alg)) {
                throw new \UnexpectedValueException('Signature verification failed');
            }
        }

        return $payload;
    }

    protected static function verifySignature($signature, $input, $key, $algo = 'HS256')
    {
        switch ($algo) {
            case'HS256':
            case'HS384':
            case'HS512':
                return static::sign($input, $key, $algo) === $signature;

            case 'RS256':
                return (boolean) openssl_verify($input, $signature, $key, OPENSSL_ALGO_SHA256);

            case 'RS384':
                return (boolean) openssl_verify($input, $signature, $key, OPENSSL_ALGO_SHA384);

            case 'RS512':
                return (boolean) openssl_verify($input, $signature, $key, OPENSSL_ALGO_SHA512);

            default:
                throw new \Exception("Unsupported or invalid signing algorithm.");
        }
    }

    protected static function sign($input, $key, $algo = 'HS256')
    {
        switch($algo) {
            case 'HS256':
                return hash_hmac('sha256', $input, $key, true);

            case 'HS384':
                return hash_hmac('sha384', $input, $key, true);

            case 'HS512':
                return hash_hmac('sha512', $input, $key, true);

            case 'RS256':
                return static::generateRSASignature($input, $key, OPENSSL_ALGO_SHA256);

            case 'RS384':
                return static::generateRSASignature($input, $key, OPENSSL_ALGO_SHA384);

            case 'RS512':
                return static::generateRSASignature($input, $key, OPENSSL_ALGO_SHA512);

            default:
                throw new \Exception("Unsupported or invalid signing algorithm.");
        }
    }

    protected static function generateRSASignature($input, $key, $algo)
    {
        if (!openssl_sign($input, $signature, $key, $algo)) {
            throw new \Exception("Unable to sign data.");
        }

        return $signature;
    }

    protected static function urlSafeB64Encode($data)
    {
        $b64 = base64_encode($data);
        $b64 = str_replace(array('+', '/', '\r', '\n', '='), array('-', '_'), $b64);
        return $b64;
    }

    protected static function urlSafeB64Decode($b64)
    {
        $b64 = str_replace(array('-', '_'), array('+', '/'), $b64);
        return base64_decode($b64);
    }
}
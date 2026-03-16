<?php
/**
 * Classe de criptografia para proteção de dados sensíveis
 *
 * @package AICG\Security
 * @since   1.0.0
 */

namespace AICG\Security;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Encryption
 *
 * Implementa criptografia bidirecional para chaves API e dados sensíveis
 *
 * @package AICG\Security
 * @since   1.0.0
 */
class Encryption
{
    /**
     * Método de criptografia utilizado
     *
     * @var string
     */
    private string $method;

    /**
     * Chave de criptografia
     *
     * @var string
     */
    private string $key;

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->method = $this->determine_method();
        $this->key = $this->derive_key();
    }

    /**
     * Determina o método de criptografia disponível
     *
     * @return string
     */
    private function determine_method(): string
    {
        if (extension_loaded('sodium') && function_exists('sodium_crypto_secretbox')) {
            return 'libsodium';
        }

        return 'openssl';
    }

    /**
     * Deriva a chave de criptografia das constantes do WordPress
     *
     * @return string
     */
    private function derive_key(): string
    {
        $key_material = defined('AUTH_KEY') ? AUTH_KEY : 'default-auth-key-' . get_site_url();
        $salt_material = defined('SECURE_AUTH_SALT') ? SECURE_AUTH_SALT : 'default-secure-salt-' . get_site_url();
        
        return hash_hmac('sha256', $key_material, $salt_material, true);
    }

    /**
     * Criptografa um valor
     *
     * @param string $plaintext Texto plano a ser criptografado
     * @return string Texto cifrado em base64
     */
    public function encrypt(string $plaintext): string
    {
        if (empty($plaintext)) {
            return '';
        }

        try {
            if ($this->method === 'libsodium') {
                return $this->encrypt_libsodium($plaintext);
            }

            return $this->encrypt_openssl($plaintext);
        } catch (\Exception $e) {
            // Log erro de criptografia (sem expor o valor)
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('AICG Encryption error: ' . $e->getMessage());
            }
            return '';
        }
    }

    /**
     * Descriptografa um valor
     *
     * @param string $ciphertext Texto cifrado em base64
     * @return string Texto plano
     */
    public function decrypt(string $ciphertext): string
    {
        if (empty($ciphertext)) {
            return '';
        }

        try {
            if ($this->method === 'libsodium') {
                return $this->decrypt_libsodium($ciphertext);
            }

            return $this->decrypt_openssl($ciphertext);
        } catch (\Exception $e) {
            // Log erro de descriptografia (sem expor o valor)
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('AICG Decryption error: ' . $e->getMessage());
            }
            return '';
        }
    }

    /**
     * Criptografa usando libsodium
     *
     * @param string $plaintext Texto plano
     * @return string
     * @throws \Exception
     */
    private function encrypt_libsodium(string $plaintext): string
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $key = substr($this->key, 0, SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        
        $ciphertext = sodium_crypto_secretbox($plaintext, $nonce, $key);
        
        // Limpa memória sensível
        sodium_memzero($key);
        
        return base64_encode($nonce . $ciphertext);
    }

    /**
     * Descriptografa usando libsodium
     *
     * @param string $ciphertext Texto cifrado
     * @return string
     * @throws \Exception
     */
    private function decrypt_libsodium(string $ciphertext): string
    {
        $decoded = base64_decode($ciphertext, true);
        
        if ($decoded === false) {
            throw new \Exception('Invalid base64 encoding');
        }

        if (strlen($decoded) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES) {
            throw new \Exception('Ciphertext too short');
        }

        $nonce = substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $encrypted = substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $key = substr($this->key, 0, SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        
        $plaintext = sodium_crypto_secretbox_open($encrypted, $nonce, $key);
        
        // Limpa memória sensível
        sodium_memzero($key);
        
        if ($plaintext === false) {
            throw new \Exception('Decryption failed');
        }

        return $plaintext;
    }

    /**
     * Criptografa usando OpenSSL (fallback)
     *
     * @param string $plaintext Texto plano
     * @return string
     * @throws \Exception
     */
    private function encrypt_openssl(string $plaintext): string
    {
        $cipher = 'aes-256-gcm';
        
        if (!in_array($cipher, openssl_get_cipher_methods(), true)) {
            throw new \Exception('Cipher not available');
        }

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $tag = '';
        
        $ciphertext = openssl_encrypt(
            $plaintext,
            $cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        if ($ciphertext === false) {
            throw new \Exception('Encryption failed');
        }

        return base64_encode($iv . $tag . $ciphertext);
    }

    /**
     * Descriptografa usando OpenSSL (fallback)
     *
     * @param string $ciphertext Texto cifrado
     * @return string
     * @throws \Exception
     */
    private function decrypt_openssl(string $ciphertext): string
    {
        $cipher = 'aes-256-gcm';
        $decoded = base64_decode($ciphertext, true);
        
        if ($decoded === false) {
            throw new \Exception('Invalid base64 encoding');
        }

        $iv_length = openssl_cipher_iv_length($cipher);
        $tag_length = 16;

        if (strlen($decoded) < $iv_length + $tag_length) {
            throw new \Exception('Ciphertext too short');
        }

        $iv = substr($decoded, 0, $iv_length);
        $tag = substr($decoded, $iv_length, $tag_length);
        $encrypted = substr($decoded, $iv_length + $tag_length);

        $plaintext = openssl_decrypt(
            $encrypted,
            $cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plaintext === false) {
            throw new \Exception('Decryption failed');
        }

        return $plaintext;
    }

    /**
     * Obtém o método de criptografia atual
     *
     * @return string
     */
    public function get_method(): string
    {
        return $this->method;
    }

    /**
     * Verifica se a criptografia está funcionando
     *
     * @return bool
     */
    public function is_working(): bool
    {
        $test_string = 'test_encryption_' . wp_rand();
        $encrypted = $this->encrypt($test_string);
        $decrypted = $this->decrypt($encrypted);
        
        return $decrypted === $test_string;
    }
}

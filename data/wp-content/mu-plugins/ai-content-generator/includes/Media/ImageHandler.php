<?php
/**
 * Handler de imagens
 *
 * @package AICG\Media
 * @since   1.0.0
 */

namespace AICG\Media;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ImageHandler
 *
 * Gerencia download e processamento de imagens
 *
 * @package AICG\Media
 * @since   1.0.0
 */
class ImageHandler
{
    /**
     * Tipos MIME permitidos
     *
     * @var array
     */
    private array $allowed_types = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    /**
     * Tamanho máximo de download (5MB)
     *
     * @var int
     */
    private int $max_size = 5 * 1024 * 1024;

    /**
     * Processa imagem destacada para um post
     *
     * @param string $image_url URL da imagem
     * @param int $post_id ID do post
     * @param string $alt_text Texto alternativo
     * @return int|\WP_Error ID do attachment ou erro
     */
    public function process_featured_image(string $image_url, int $post_id, string $alt_text = '')
    {
        // Valida URL
        $validated_url = $this->validate_image_url($image_url);
        
        if (is_wp_error($validated_url)) {
            return $validated_url;
        }

        // Verifica tipo via HEAD request
        $type_check = $this->check_content_type($image_url);
        
        if (is_wp_error($type_check)) {
            return $type_check;
        }

        // Download da imagem
        $download = $this->download_image($image_url);
        
        if (is_wp_error($download)) {
            return $download;
        }

        // Upload para biblioteca de mídia
        $attachment_id = $this->upload_to_media_library(
            $download,
            $post_id,
            $alt_text
        );

        if (is_wp_error($attachment_id)) {
            return $attachment_id;
        }

        // Define como imagem destacada
        $result = set_post_thumbnail($post_id, $attachment_id);

        if (!$result) {
            return new \WP_Error(
                'featured_image_failed',
                esc_html__('Falha ao definir imagem destacada.', 'ai-content-generator')
            );
        }

        return $attachment_id;
    }

    /**
     * Valida URL da imagem
     *
     * @param string $url URL a validar
     * @return string|\WP_Error
     */
    private function validate_image_url(string $url)
    {
        $parsed = wp_parse_url($url);

        if (!$parsed) {
            return new \WP_Error(
                'invalid_url',
                esc_html__('URL de imagem inválida.', 'ai-content-generator')
            );
        }

        // Verifica scheme
        if (!in_array($parsed['scheme'], ['https', 'http'], true)) {
            return new \WP_Error(
                'invalid_scheme',
                esc_html__('URL deve usar HTTP ou HTTPS.', 'ai-content-generator')
            );
        }

        // Verifica host
        if (empty($parsed['host'])) {
            return new \WP_Error(
                'missing_host',
                esc_html__('URL sem host válido.', 'ai-content-generator')
            );
        }

        return $url;
    }

    /**
     * Verifica Content-Type via HEAD request
     *
     * @param string $url URL da imagem
     * @return true|\WP_Error
     */
    private function check_content_type(string $url)
    {
        $response = wp_remote_head($url, [
            'timeout' => 10,
            'sslverify' => true,
            'user_agent' => 'WordPress/' . get_bloginfo('version'),
        ]);

        if (is_wp_error($response)) {
            return new \WP_Error(
                'head_request_failed',
                $response->get_error_message()
            );
        }

        $http_code = wp_remote_retrieve_response_code($response);
        
        if ($http_code !== 200) {
            return new \WP_Error(
                'head_request_error',
                sprintf(
                    /* translators: %d: HTTP status code */
                    esc_html__('Erro na verificação da imagem: HTTP %d', 'ai-content-generator'),
                    $http_code
                )
            );
        }

        $content_type = wp_remote_retrieve_header($response, 'content-type');

        // Remove charset da string
        $content_type = explode(';', $content_type)[0];
        $content_type = trim($content_type);

        if (!in_array($content_type, $this->allowed_types, true)) {
            return new \WP_Error(
                'invalid_content_type',
                sprintf(
                    /* translators: %s: Content type */
                    esc_html__('Tipo de imagem não permitido: %s', 'ai-content-generator'),
                    $content_type
                )
            );
        }

        // Verifica tamanho se disponível
        $content_length = wp_remote_retrieve_header($response, 'content-length');
        if ($content_length && (int) $content_length > $this->max_size) {
            return new \WP_Error(
                'file_too_large',
                sprintf(
                    /* translators: %s: Maximum file size */
                    esc_html__('Arquivo muito grande. Tamanho máximo: %s', 'ai-content-generator'),
                    size_format($this->max_size)
                )
            );
        }

        return true;
    }

    /**
     * Faz download da imagem
     *
     * @param string $url URL da imagem
     * @return array|\WP_Error
     */
    private function download_image(string $url)
    {
        $response = wp_remote_get($url, [
            'timeout' => 30,
            'limit_response_size' => $this->max_size,
            'sslverify' => true,
        ]);

        if (is_wp_error($response)) {
            return new \WP_Error(
                'download_failed',
                $response->get_error_message()
            );
        }

        $http_code = wp_remote_retrieve_response_code($response);
        
        if ($http_code !== 200) {
            return new \WP_Error(
                'download_error',
                sprintf(
                    /* translators: %d: HTTP status code */
                    esc_html__('Falha no download: HTTP %d', 'ai-content-generator'),
                    $http_code
                )
            );
        }

        $content = wp_remote_retrieve_body($response);
        $content_type = wp_remote_retrieve_header($response, 'content-type');

        if (empty($content)) {
            return new \WP_Error(
                'empty_content',
                esc_html__('Conteúdo da imagem vazio.', 'ai-content-generator')
            );
        }

        // Obtém nome do arquivo da URL
        $parsed = wp_parse_url($url);
        $filename = basename($parsed['path'] ?? '');

        if (empty($filename)) {
            $filename = 'ai-generated-' . time();
            
            // Adiciona extensão baseada no content-type
            $extensions = [
                'image/jpeg' => '.jpg',
                'image/png' => '.png',
                'image/webp' => '.webp',
                'image/gif' => '.gif',
            ];
            
            $content_type_clean = explode(';', $content_type)[0];
            $filename .= $extensions[$content_type_clean] ?? '.jpg';
        }

        return [
            'content' => $content,
            'type' => $content_type,
            'name' => sanitize_file_name($filename),
        ];
    }

    /**
     * Faz upload para biblioteca de mídia
     *
     * @param array $image_data Dados da imagem
     * @param int $post_id ID do post pai
     * @param string $alt_text Texto alternativo
     * @return int|\WP_Error
     */
    private function upload_to_media_library(array $image_data, int $post_id, string $alt_text = '')
    {
        // Upload do arquivo
        $upload = wp_upload_bits(
            $image_data['name'],
            null,
            $image_data['content']
        );

        if (!empty($upload['error'])) {
            return new \WP_Error(
                'upload_failed',
                $upload['error']
            );
        }

        $file_path = $upload['file'];
        $file_url = $upload['url'];

        // Extrai tipo MIME
        $file_type = $image_data['type'];
        if (empty($file_type)) {
            $file_type = wp_check_filetype($file_path)['type'];
        }

        // Prepara dados do attachment
        $attachment = [
            'post_mime_type' => $file_type,
            'post_title' => sanitize_file_name(pathinfo($image_data['name'], PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit',
            'guid' => $file_url,
        ];

        // Insere attachment
        $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);

        if (is_wp_error($attach_id)) {
            return $attach_id;
        }

        // Gera metadados e thumbnails
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
        wp_update_attachment_metadata($attach_id, $attach_data);

        // Define alt text
        if (!empty($alt_text)) {
            update_post_meta($attach_id, '_wp_attachment_image_alt', sanitize_text_field($alt_text));
        }

        return $attach_id;
    }

    /**
     * Obtém dimensões de uma imagem remota
     *
     * @param string $url URL da imagem
     * @return array|false
     */
    public function get_remote_image_size(string $url)
    {
        // Tenta via HEAD request primeiro
        $response = wp_remote_head($url, [
            'timeout' => 10,
            'sslverify' => true,
        ]);

        if (!is_wp_error($response)) {
            // Verifica se temos informações de tamanho
            $content_length = wp_remote_retrieve_header($response, 'content-length');
        }

        // Faz download parcial para obter dimensões
        $response = wp_remote_get($url, [
            'timeout' => 15,
            'headers' => [
                'Range' => 'bytes=0-65535', // Primeiros 64KB geralmente contêm metadados
            ],
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $content = wp_remote_retrieve_body($response);
        
        if (empty($content)) {
            return false;
        }

        // Tenta obter dimensões do conteúdo parcial
        $temp_file = tempnam(sys_get_temp_dir(), 'aicg_img_');
        file_put_contents($temp_file, $content);
        $size = getimagesize($temp_file);
        unlink($temp_file);

        if ($size) {
            return [
                'width' => $size[0],
                'height' => $size[1],
            ];
        }

        return false;
    }
}

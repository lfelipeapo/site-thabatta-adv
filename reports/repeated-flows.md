# Relatório de fluxos repetidos

Gerado em: 2026-01-26 00:45:55

## WP_Query similares

- **Args normalizados:** `$args`
  - data/wp-content/themes/thabatta-adv-theme/front-page.php:55: `$areas_query = new WP_Query($args);`
  - data/wp-content/themes/thabatta-adv-theme/front-page.php:182: `$testimonials_query = new WP_Query($args);`
  - data/wp-content/themes/thabatta-adv-theme/front-page.php:325: `$team_query = new WP_Query($args);`
  - data/wp-content/themes/thabatta-adv-theme/front-page.php:390: `$blog_query = new WP_Query($args);`
  - data/wp-content/themes/thabatta-adv-theme/page-blog.php:34: `$blog_query = new WP_Query($args);`
  - data/wp-content/themes/thabatta-adv-theme/inc/acf-fields.php:1575: `$query = new WP_Query($args);`
  - data/wp-content/themes/thabatta-adv-theme/inc/acf-fields.php:1607: `$query = new WP_Query($args);`
  - data/wp-content/themes/thabatta-adv-theme/inc/acf-fields.php:1628: `$query = new WP_Query($args);`
  - **Recomendação:** extrair a query para `src/Repository` e reutilizar.

- **Args normalizados:** `$related_args`
  - data/wp-content/themes/thabatta-adv-theme/inc/template-functions.php:1127: `$related_query = new WP_Query($related_args);`
  - data/wp-content/themes/thabatta-adv-theme/inc/template-functions.php:1179: `$related_query = new WP_Query($related_args);`
  - **Recomendação:** extrair a query para `src/Repository` e reutilizar.

## Loops repetidos (have_posts / the_post)
- Nenhum loop padrão detectado.

## Sanitização/escape repetidos
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/front-page.php:1 (ocorrências: 46)
  - Linha 24: `<section class="hero-section" style="background-image: url('<?php echo esc_url($banner_image); ?>');">`
  - Linha 27: `<h1><?php echo esc_html(get_theme_mod('hero_title', 'Thabatta Apolinário Advocacia')); ?></h1>`
  - Linha 28: `<p><?php echo esc_html(get_theme_mod('hero_description', 'Advocacia especializada em Direito Civil, Empresarial e Trabalhista. Atendimento personalizado e soluções jurídicas eficientes.')); ?></p>`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/form-handler.php:1 (ocorrências: 14)
  - Linha 78: `echo esc_html(get_post_meta($post_id, '_phone', true));`
  - Linha 82: `echo esc_html(get_post_meta($post_id, '_cpf_cnpj', true));`
  - Linha 86: `echo esc_html(get_post_meta($post_id, '_area', true));`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/security.php:1 (ocorrências: 5)
  - Linha 113: `$query_vars['s'] = sanitize_text_field($query_vars['s']);`
  - Linha 124: `$commentdata['comment_content'] = sanitize_textarea_field($commentdata['comment_content']);`
  - Linha 128: `$commentdata['comment_author'] = sanitize_text_field($commentdata['comment_author']);`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/template-functions.php:1 (ocorrências: 84)
  - Linha 150: `echo '<meta name="description" content="' . esc_attr($description) . '" />' . "\n";`
  - Linha 153: `echo '<meta name="description" content="' . esc_attr(get_bloginfo('description')) . '" />' . "\n";`
  - Linha 157: `echo '<meta name="description" content="' . esc_attr(wp_trim_words(strip_tags($term_description), 20, '...')) . '" />' . "\n";`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/admin/admin-features.php:1 (ocorrências: 23)
  - Linha 283: `<input type="text" name="thabatta_meta_title" id="thabatta_meta_title" value="<?php echo esc_attr($meta_title); ?>" class="large-text">`
  - Linha 297: `<input type="text" name="thabatta_meta_keywords" id="thabatta_meta_keywords" value="<?php echo esc_attr($meta_keywords); ?>" class="large-text">`
  - Linha 319: `<?php echo esc_html($meta_title ? $meta_title : get_the_title($post->ID)); ?>`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/admin/jetpack-integration.php:1 (ocorrências: 9)
  - Linha 104: `<p><a href="<?php echo esc_url(admin_url('admin.php?page=jetpack_modules')); ?>" class="button"><?php esc_html_e('Ativar Módulos do Jetpack', 'thabatta-adv'); ?></a></p>`
  - Linha 242: `echo esc_html(human_time_diff(0, $cache_expiry));`
  - Linha 301: `<span class="thabatta-cache-stat-number"><?php echo esc_html(strval($cache_hits)); ?></span>`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/customizer.php:1 (ocorrências: 10)
  - Linha 998: `--primary-color: <?php echo esc_attr(get_theme_mod('primary_color', '#8b0000')); ?>;`
  - Linha 999: `--primary-color-hover: <?php echo esc_attr(thabatta_adjust_brightness(get_theme_mod('primary_color', '#8b0000'), -20)); ?>;`
  - Linha 1000: `--secondary-color: <?php echo esc_attr(get_theme_mod('secondary_color', '#d4af37')); ?>;`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/ajax-handlers.php:1 (ocorrências: 20)
  - Linha 25: `$name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';`
  - Linha 26: `$email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';`
  - Linha 27: `$phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/github-actions.php:1 (ocorrências: 5)
  - Linha 73: `<input type="text" name="thabatta_github_repo" value="<?php echo esc_attr(get_option('thabatta_github_repo')); ?>" class="regular-text" placeholder="username/repository" />`
  - Linha 81: `<input type="text" name="thabatta_github_branch" value="<?php echo esc_attr(get_option('thabatta_github_branch', 'main')); ?>" class="regular-text" placeholder="main" />`
  - Linha 89: `<input type="text" name="thabatta_infinity_free_username" value="<?php echo esc_attr(get_option('thabatta_infinity_free_username')); ?>" class="regular-text" />`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/security-features.php:1 (ocorrências: 10)
  - Linha 168: `$query = sanitize_text_field($query);`
  - Linha 552: `echo '<td>' . esc_html($event['type']) . '</td>';`
  - Linha 553: `echo '<td>' . esc_html($event['ip']) . '</td>';`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/jetpack-integration.php:1 (ocorrências: 5)
  - Linha 226: `<p><a href="<?php echo esc_url(admin_url('plugins.php')); ?>" class="button"><?php esc_html_e('Instalar e Ativar Jetpack', 'thabatta-adv'); ?></a></p>`
  - Linha 246: `<h1><?php echo esc_html(get_admin_page_title()); ?></h1>`
  - Linha 532: `update_post_meta($post_id, '_thabatta_custom_cache_expiry', sanitize_text_field($_POST['thabatta_custom_cache_expiry']));`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/template-tags.php:1 (ocorrências: 57)
  - Linha 24: `esc_attr(get_the_date(DATE_W3C)),`
  - Linha 25: `esc_html(get_the_date()),`
  - Linha 26: `esc_attr(get_the_modified_date(DATE_W3C)),`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/inc/contact-handler.php:1 (ocorrências: 6)
  - Linha 17: `$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';`
  - Linha 26: `$name = isset($form_data['contact_name']) ? sanitize_text_field($form_data['contact_name']) : '';`
  - Linha 27: `$email = isset($form_data['contact_email']) ? sanitize_email($form_data['contact_email']) : '';`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/template-parts/content-page.php:1 (ocorrências: 5)
  - Linha 48: `wp_kses_post(get_the_title())`
  - Linha 68: `<a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>">`
  - Linha 74: `<h4><a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>"><?php echo esc_html(get_the_title($related_post->ID)); ?></a></h4>`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/template-parts/contact-form-multistep.php:1 (ocorrências: 5)
  - Linha 58: `echo '<option value="' . esc_attr($area->post_title) . '">' . esc_html($area->post_title) . '</option>';`
  - Linha 58: `echo '<option value="' . esc_attr($area->post_title) . '">' . esc_html($area->post_title) . '</option>';`
  - Linha 70: `echo '<option value="' . esc_html($label) . '">' . esc_html($label) . '</option>';`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.
- **Arquivo:** data/wp-content/themes/thabatta-adv-theme/template-parts/form-consultation.php:1 (ocorrências: 5)
  - Linha 85: `echo '<option value="' . esc_attr($area->post_title) . '">' . esc_html($area->post_title) . '</option>';`
  - Linha 85: `echo '<option value="' . esc_attr($area->post_title) . '">' . esc_html($area->post_title) . '</option>';`
  - Linha 99: `echo '<option value="' . esc_html($label) . '">' . esc_html($label) . '</option>';`
  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.

## Próximos passos sugeridos
- Consolidar queries repetidas em `src/Repository`. 
- Centralizar renderizações similares em `template-parts/`. 
- Criar serviços reutilizáveis em `src/Service` para regras de negócio e sanitização.

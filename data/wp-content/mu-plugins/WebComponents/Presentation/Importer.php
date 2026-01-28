<?php

namespace Thabatta\WebComponents\Presentation;

use Thabatta\WebComponents\Infrastructure\MetadataService;

class Importer {
    public function __construct(private MetadataService $metadataService) {
    }

    public function register(): void {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('wp_ajax_thabatta_import_web_component', [$this, 'importWebComponent']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function addAdminMenu(): void {
        add_submenu_page(
            'edit.php?post_type=web_component',
            'Importar Componentes',
            'Importar Componentes',
            'manage_options',
            'thabatta-import-components',
            [$this, 'renderImportPage']
        );
    }

    public function enqueueAssets(string $hook): void {
        if ($hook !== 'web_component_page_thabatta-import-components') {
            return;
        }

        $assetPath = WPMU_PLUGIN_DIR . '/WebComponents/assets/admin-importer.js';
        $assetUrl = WPMU_PLUGIN_URL . '/WebComponents/assets/admin-importer.js';

        wp_enqueue_script(
            'thabatta-web-components-importer',
            $assetUrl,
            ['jquery'],
            file_exists($assetPath) ? filemtime($assetPath) : false,
            true
        );
    }

    public function renderImportPage(): void {
        $nonce = wp_create_nonce('thabatta_import_web_component');
        ?>
        <div class="wrap">
            <h1>Importar Web Components</h1>
            <p>Selecione os componentes da classe Thabatta_Web_Components que deseja importar:</p>

            <form id="thabatta-import-form">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">Componentes Disponíveis</th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">Componentes Disponíveis</legend>
                                    <label>
                                        <input type="checkbox" name="components[]" value="card"> Card
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="accordion"> Accordion
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="tabs"> Tabs
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="slider"> Slider
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="testimonial"> Testimonial
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="cta"> CTA
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="icon_box"> Icon Box
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="team_member"> Team Member
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="counter"> Counter
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="timeline"> Timeline
                                    </label><br>
                                </fieldset>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="button" id="thabatta-import-button" class="button button-primary">Importar Componentes Selecionados</button>
                </p>
            </form>

            <div id="thabatta-import-results" style="display: none;">
                <h2>Resultados da Importação</h2>
                <div id="thabatta-import-messages"></div>
            </div>
        </div>
        <div id="thabatta-import-data"
             data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
             data-nonce="<?php echo esc_attr($nonce); ?>"></div>
        <?php
    }

    public function importWebComponent(): void {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'thabatta_import_web_component')) {
            wp_send_json_error('Erro de segurança. Por favor, recarregue a página e tente novamente.');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Você não tem permissão para realizar esta ação.');
        }

        if (!isset($_POST['components']) || !is_array($_POST['components'])) {
            wp_send_json_error('Nenhum componente selecionado.');
        }

        $components = array_map('sanitize_text_field', $_POST['components']);
        $results = [];

        $availableComponents = $this->availableComponents();

        foreach ($components as $component) {
            if (!isset($availableComponents[$component])) {
                $results[] = "<div class='notice notice-error'><p>Componente <strong>{$component}</strong> não encontrado.</p></div>";
                continue;
            }

            $componentData = $availableComponents[$component];

            $postId = wp_insert_post([
                'post_title' => ucfirst(str_replace('_', ' ', $component)),
                'post_type' => 'web_component',
                'post_status' => 'publish',
            ]);

            if (is_wp_error($postId)) {
                $results[] = "<div class='notice notice-error'><p>Erro ao criar o componente <strong>{$component}</strong>: {$postId->get_error_message()}</p></div>";
                continue;
            }

            foreach ($componentData as $key => $value) {
                $this->metadataService->set($postId, $key, $value);
            }

            $results[] = "<div class='notice notice-success'><p>Componente <strong>{$component}</strong> importado com sucesso!</p></div>";
        }

        echo implode('', $results);
        wp_die();
    }

    private function availableComponents(): array {
        return [
            /* ════════════════════════════════════════  CARD  ════════════════════════════════════════ */
            'card' => [
                'tag_name' => 'thabatta-card',
                'html_code' => '<div class="thabatta-card-image">\n    <img src="{{image}}" alt="{{title}}">\n  </div>\n  <div class="thabatta-card-content">\n    <h3 class="thabatta-card-title">{{title}}</h3>\n    <div class="thabatta-card-text">{{content}}</div>\n    <a href="{{link}}" class="thabatta-card-button">{{button_text}}</a>\n  </div>',
                'css_code'  => '.thabatta-card{display:flex;flex-direction:column;border-radius:8px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,.1);transition:transform .3s,box-shadow .3s}.thabatta-card:hover{transform:translateY(-5px);box-shadow:0 10px 20px rgba(0,0,0,.15)}.thabatta-card-image img{width:100%;display:block}.thabatta-card-content{padding:20px}.thabatta-card-title{margin:0 0 10px;font-size:1.5rem}.thabatta-card-text{margin-bottom:20px}.thabatta-card-button{display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:4px;transition:background .3s}.thabatta-card-button:hover{background:#0056b3}',
                'js_code'   => '/* preencher placeholders */(root=>{root.querySelectorAll("*").forEach(e=>{[...e.attributes].forEach(a=>a.value=a.value.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||""));e.childNodes.forEach(n=>{if(n.nodeType===3)n.textContent=n.textContent.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||"")});})})(root);\nconsole.log("Card component ready");',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ],

            /* ═════════════════════════════════════ ACCORDION ═════════════════════════════════════ */
            'accordion' => [
                'tag_name' => 'thabatta-accordion',
                'html_code' => '<div class="thabatta-accordion">\n    <div class="thabatta-accordion-item">\n      <h3 class="thabatta-accordion-header">\n        <button class="thabatta-accordion-button" type="button">\n          {{title}}\n          <span class="thabatta-accordion-icon"></span>\n        </button>\n      </h3>\n      <div class="thabatta-accordion-collapse">\n        <div class="thabatta-accordion-body">{{content}}</div>\n      </div>\n    </div>\n  </div>',
                'css_code' => '.thabatta-accordion{border:1px solid #ddd;border-radius:4px;overflow:hidden}.thabatta-accordion-item{border-bottom:1px solid #ddd}.thabatta-accordion-item:last-child{border-bottom:none}.thabatta-accordion-button{display:flex;justify-content:space-between;align-items:center;width:100%;padding:15px;background:#f8f9fa;border:none;text-align:left;cursor:pointer;transition:background .3s}.thabatta-accordion-button:hover{background:#e9ecef}.thabatta-accordion-icon{width:10px;height:10px;border-right:2px solid #333;border-bottom:2px solid #333;transform:rotate(45deg);transition:transform .3s}.thabatta-accordion-button[aria-expanded=true] .thabatta-accordion-icon{transform:rotate(-135deg)}.thabatta-accordion-collapse{display:none}.thabatta-accordion-collapse[aria-hidden=false]{display:block}.thabatta-accordion-body{padding:15px}',
                'js_code' => '(root=>{root.querySelectorAll("*").forEach(e=>{e.childNodes.forEach(n=>{if(n.nodeType===3)n.textContent=n.textContent.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||"")})})})(root);\nconst buttons=root.querySelectorAll(".thabatta-accordion-button");\nbuttons.forEach(btn=>btn.addEventListener("click",()=>{const ex=btn.getAttribute("aria-expanded")==="true";btn.setAttribute("aria-expanded",!ex);btn.parentElement.nextElementSibling.hidden=ex;}));',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ],

            /* ════════════════════════════════════════  TABS  ══════════════════════════════════════ */
            'tabs' => [
                'tag_name' => 'thabatta-tabs',
                'html_code' => '<div class="thabatta-tabs">\n    <div class="thabatta-tabs-nav" role="tablist">\n      <button class="thabatta-tab-button active" role="tab" aria-selected="true">Tab 1</button>\n      <button class="thabatta-tab-button" role="tab" aria-selected="false">Tab 2</button>\n    </div>\n    <div class="thabatta-tabs-content">\n      <div class="thabatta-tab-panel active" role="tabpanel">Conteúdo da Tab 1</div>\n      <div class="thabatta-tab-panel" role="tabpanel" hidden>Conteúdo da Tab 2</div>\n    </div>\n  </div>',
                'css_code' => '.thabatta-tabs{border:1px solid #ddd;border-radius:4px;overflow:hidden}.thabatta-tabs-nav{display:flex;background:#f8f9fa;border-bottom:1px solid #ddd}.thabatta-tab-button{padding:12px 20px;background:0 0;border:none;border-right:1px solid #ddd;cursor:pointer;transition:background .3s}.thabatta-tab-button:last-child{border-right:none}.thabatta-tab-button:hover{background:#e9ecef}.thabatta-tab-button.active{background:#fff;border-bottom:2px solid #007bff}.thabatta-tab-panel{padding:20px;display:none}.thabatta-tab-panel.active{display:block}',
                'js_code' => '(root=>{root.querySelectorAll("*").forEach(e=>{e.childNodes.forEach(n=>{if(n.nodeType===3)n.textContent=n.textContent.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||"")})})})(root);\nconst btns=root.querySelectorAll(".thabatta-tab-button"),panels=root.querySelectorAll(".thabatta-tab-panel");\nbtns.forEach((b,i)=>b.addEventListener("click",()=>{btns.forEach(x=>{x.classList.remove("active");x.setAttribute("aria-selected","false")});panels.forEach(p=>{p.classList.remove("active");p.hidden=true});b.classList.add("active");b.setAttribute("aria-selected","true");panels[i].classList.add("active");panels[i].hidden=false;}));',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ],

            /* ═══════════════════════════════════════  SLIDER  ═════════════════════════════════════ */
            'slider' => [
                'tag_name' => 'thabatta-slider',
                'html_code' => '<div class="thabatta-slider">\n    <div class="thabatta-slider-container">\n      <div class="thabatta-slide active">\n        <div class="thabatta-slide-image"><img src="{{image1}}" alt=""></div>\n        <div class="thabatta-slide-content">{{content1}}</div>\n      </div>\n      <div class="thabatta-slide">\n        <div class="thabatta-slide-image"><img src="{{image2}}" alt=""></div>\n        <div class="thabatta-slide-content">{{content2}}</div>\n      </div>\n    </div>\n    <div class="thabatta-slider-arrows">\n      <button class="thabatta-slider-arrow thabatta-slider-prev">←</button>\n      <button class="thabatta-slider-arrow thabatta-slider-next">→</button>\n    </div>\n    <div class="thabatta-slider-dots">\n      <button class="thabatta-slider-dot active" data-slide="0"></button>\n      <button class="thabatta-slider-dot" data-slide="1"></button>\n    </div>\n  </div>',
                'css_code' => '.thabatta-slider{position:relative;overflow:hidden;border-radius:8px}.thabatta-slider-container{display:flex;transition:transform .5s}.thabatta-slide{flex:0 0 100%;display:none}.thabatta-slide.active{display:block}.thabatta-slide-image img{width:100%;display:block}.thabatta-slide-content{padding:20px;background:rgba(0,0,0,.7);color:#fff}.thabatta-slider-arrows{position:absolute;top:50%;left:0;right:0;display:flex;justify-content:space-between;transform:translateY(-50%);z-index:1}.thabatta-slider-arrow{background:rgba(0,0,0,.5);color:#fff;border:none;width:40px;height:40px;border-radius:50%;cursor:pointer;transition:background .3s}.thabatta-slider-arrow:hover{background:rgba(0,0,0,.8)}.thabatta-slider-dots{position:absolute;bottom:20px;left:0;right:0;display:flex;justify-content:center;gap:10px}.thabatta-slider-dot{width:12px;height:12px;border-radius:50%;background:rgba(255,255,255,.5);border:none;cursor:pointer;transition:background .3s}.thabatta-slider-dot.active{background:#fff}',
                'js_code' => '(root=>{root.querySelectorAll("*").forEach(e=>{[...e.attributes].forEach(a=>a.value=a.value.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||""));})})(root);\nconst slides=root.querySelectorAll(".thabatta-slide"),dots=root.querySelectorAll(".thabatta-slider-dot"),prev=root.querySelector(".thabatta-slider-prev"),next=root.querySelector(".thabatta-slider-next");let idx=0;\nconst show=i=>{slides.forEach(s=>s.classList.remove("active"));dots.forEach(d=>d.classList.remove("active"));slides[i].classList.add("active");dots[i].classList.add("active");idx=i;};\nprev.addEventListener("click",()=>show((idx-1+slides.length)%slides.length));\nnext.addEventListener("click",()=>show((idx+1)%slides.length));\ndots.forEach((d,i)=>d.addEventListener("click",()=>show(i)));\nif(host.getAttribute("data-autoplay")==="true"){const int=parseInt(host.getAttribute("data-interval")||"5000");setInterval(()=>show((idx+1)%slides.length),int);}',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ],

            /* ═══════════════════════════════════  TESTIMONIAL  ═══════════════════════════════════ */
            'testimonial' => [
                'tag_name' => 'thabatta-testimonial',
                'html_code' => '<div class="thabatta-testimonial">\n    <div class="thabatta-testimonial-content">\n      <div class="thabatta-testimonial-rating">\n        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>\n      </div>\n      <div class="thabatta-testimonial-text">{{content}}</div>\n      <div class="thabatta-testimonial-author">\n        <div class="thabatta-testimonial-image"><img src="{{image}}" alt="{{author}}"></div>\n        <div class="thabatta-testimonial-info">\n          <div class="thabatta-testimonial-name">{{author}}</div>\n          <div class="thabatta-testimonial-position">{{position}}, {{company}}</div>\n        </div>\n      </div>\n    </div>\n  </div>',
                'css_code' => '.thabatta-testimonial{padding:30px;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,.1);background:#fff}.thabatta-testimonial-rating{margin-bottom:15px;color:#ffc107}.thabatta-testimonial-text{font-style:italic;margin-bottom:20px;position:relative}.thabatta-testimonial-author{display:flex;align-items:center}.thabatta-testimonial-image{width:60px;height:60px;border-radius:50%;overflow:hidden;margin-right:15px}.thabatta-testimonial-image img{width:100%;height:100%;object-fit:cover}.thabatta-testimonial-name{font-weight:bold;margin-bottom:5px}.thabatta-testimonial-position{font-size:.9em;color:#6c757d}',
                'js_code' => '(root=>{root.querySelectorAll("*").forEach(e=>{[...e.attributes].forEach(a=>a.value=a.value.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||""));e.childNodes.forEach(n=>{if(n.nodeType===3)n.textContent=n.textContent.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||"")});})})(root);',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ],

            /* ════════════════════════════════════════  CTA  ═══════════════════════════════════════ */
            'cta' => [
                'tag_name' => 'thabatta-cta',
                'html_code' => '<div class="thabatta-cta">\n    <div class="thabatta-cta-content">\n      <h2 class="thabatta-cta-title">{{title}}</h2>\n      <div class="thabatta-cta-text">{{content}}</div>\n      <a href="{{button_url}}" class="thabatta-cta-button">{{button_text}}</a>\n    </div>\n  </div>',
                'css_code' => '.thabatta-cta{padding:60px 30px;text-align:center;background:#007bff;color:#fff;border-radius:8px;background-size:cover;background-position:center;position:relative}.thabatta-cta::before{content:"";position:absolute;inset:0;background:rgba(0,0,0,.5);border-radius:8px}.thabatta-cta-content{position:relative;z-index:1;max-width:800px;margin:0 auto}.thabatta-cta-title{font-size:2.5rem;margin-bottom:20px}.thabatta-cta-text{margin-bottom:30px;font-size:1.2rem}.thabatta-cta-button{display:inline-block;padding:12px 30px;background:#fff;color:#007bff;text-decoration:none;border-radius:4px;font-weight:bold;transition:background .3s,transform .3s}.thabatta-cta-button:hover{background:#f8f9fa;transform:translateY(-3px)}',
                'js_code' => '(root=>{root.querySelectorAll("*").forEach(e=>{[...e.attributes].forEach(a=>a.value=a.value.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||""));e.childNodes.forEach(n=>{if(n.nodeType===3)n.textContent=n.textContent.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||"")});})})(root);',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ],

            /* ════════════════════════════════════  ICON BOX  ════════════════════════════════════ */
            'icon_box' => [
                'tag_name' => 'thabatta-icon-box',
                'html_code' => '<div class="thabatta-icon-box">\n    <div class="thabatta-icon-box-icon"><i class="{{icon}}"></i></div>\n    <div class="thabatta-icon-box-content">\n      <h3 class="thabatta-icon-box-title">{{title}}</h3>\n      <div class="thabatta-icon-box-text">{{content}}</div>\n      <a href="{{link}}" class="thabatta-icon-box-link">Saiba mais <i class="fas fa-arrow-right"></i></a>\n    </div>\n  </div>',
                'css_code' => '.thabatta-icon-box{padding:30px;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,.1);background:#fff;transition:transform .3s,box-shadow .3s}.thabatta-icon-box:hover{transform:translateY(-5px);box-shadow:0 10px 20px rgba(0,0,0,.15)}.thabatta-icon-box-icon{font-size:3rem;color:#007bff;margin-bottom:20px;text-align:center}.thabatta-icon-box-title{margin:0 0 15px;font-size:1.5rem}.thabatta-icon-box-text{margin-bottom:20px}.thabatta-icon-box-link{display:inline-flex;align-items:center;color:#007bff;text-decoration:none;font-weight:bold;transition:color .3s}.thabatta-icon-box-link i{margin-left:5px;transition:transform .3s}.thabatta-icon-box-link:hover{color:#0056b3}.thabatta-icon-box-link:hover i{transform:translateX(5px)}',
                'js_code' => '(root=>{root.querySelectorAll("*").forEach(e=>{[...e.attributes].forEach(a=>a.value=a.value.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||""));e.childNodes.forEach(n=>{if(n.nodeType===3)n.textContent=n.textContent.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||"")});})})(root);',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ],

            /* ═══════════════════════════════════  TEAM MEMBER  ═══════════════════════════════════ */
            'team_member' => [
                'tag_name' => 'thabatta-team-member',
                'html_code' => '<div class="thabatta-team-member">\n    <div class="thabatta-team-member-image"><img src="{{image}}" alt="{{name}}"></div>\n    <div class="thabatta-team-member-content">\n      <h3 class="thabatta-team-member-name">{{name}}</h3>\n      <div class="thabatta-team-member-position">{{position}}</div>\n      <div class="thabatta-team-member-bio">{{content}}</div>\n      <div class="thabatta-team-member-social">\n        <a href="{{facebook}}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>\n        <a href="{{twitter}}" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>\n        <a href="{{linkedin}}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>\n        <a href="{{instagram}}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>\n      </div>\n    </div>\n  </div>',
                'css_code' => '.thabatta-team-member{border-radius:8px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,.1);background:#fff;transition:transform .3s,box-shadow .3s}.thabatta-team-member:hover{transform:translateY(-5px);box-shadow:0 10px 20px rgba(0,0,0,.15)}.thabatta-team-member-image{position:relative;overflow:hidden}.thabatta-team-member-image img{width:100%;transition:transform .5s}.thabatta-team-member:hover .thabatta-team-member-image img{transform:scale(1.1)}.thabatta-team-member-content{padding:20px}.thabatta-team-member-name{margin:0 0 5px;font-size:1.5rem}.thabatta-team-member-position{color:#6c757d;margin-bottom:15px;font-style:italic}.thabatta-team-member-bio{margin-bottom:20px}.thabatta-team-member-social{display:flex;gap:10px}.thabatta-team-member-social a{display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#f8f9fa;color:#6c757d;transition:background .3s,color .3s}.thabatta-team-member-social a:hover{background:#007bff;color:#fff}',
                'js_code' => '(root=>{root.querySelectorAll("*").forEach(e=>{[...e.attributes].forEach(a=>a.value=a.value.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||""));e.childNodes.forEach(n=>{if(n.nodeType===3)n.textContent=n.textContent.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||"")});})})(root);',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ],

            /* ═════════════════════════════════════  COUNTER  ═════════════════════════════════════ */
            'counter' => [
                'tag_name' => 'thabatta-counter',
                'html_code' => '<div class="thabatta-counter">\n    <div class="thabatta-counter-icon"><i class="{{icon}}"></i></div>\n    <div class="thabatta-counter-content">\n      <div class="thabatta-counter-number">\n        <span class="thabatta-counter-prefix">{{prefix}}</span>\n        <span class="thabatta-counter-value">0</span>\n        <span class="thabatta-counter-suffix">{{suffix}}</span>\n      </div>\n      <h3 class="thabatta-counter-title">{{title}}</h3>\n      <div class="thabatta-counter-text">{{content}}</div>\n    </div>\n  </div>',
                'css_code' => '.thabatta-counter{padding:30px;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,.1);background:#fff;text-align:center}.thabatta-counter-icon{font-size:3rem;color:#007bff;margin-bottom:20px}.thabatta-counter-number{font-size:3rem;font-weight:bold;color:#343a40;margin-bottom:10px}.thabatta-counter-title{margin:0 0 15px;font-size:1.5rem}.thabatta-counter-text{color:#6c757d}',
                'js_code' => '(root=>{root.querySelectorAll("*").forEach(e=>{[...e.attributes].forEach(a=>a.value=a.value.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||""));e.childNodes.forEach(n=>{if(n.nodeType===3)n.textContent=n.textContent.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||"")});})})(root);\nconst val=root.querySelector(".thabatta-counter-value"),start=parseInt(host.getAttribute("data-start")||"0"),end=parseInt(host.getAttribute("data-end")||"100"),dur=parseInt(host.getAttribute("data-duration")||"2000");\nconst anim=(s,e,d)=>{let t0;const step=t=>{if(!t0)t0=t;const p=Math.min((t-t0)/d,1);val.textContent=Math.floor(p*(e-s)+s);if(p<1)requestAnimationFrame(step);};requestAnimationFrame(step);};\n(new IntersectionObserver(ents=>{ents.forEach(e=>{if(e.isIntersecting){anim(start,end,dur);obs.unobserve(e.target);}}},{threshold:.1})).observe(host);',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ],

            /* ════════════════════════════════════  TIMELINE  ═════════════════════════════════════ */
            'timeline' => [
                'tag_name' => 'thabatta-timeline',
                'html_code' => '<div class="thabatta-timeline">\n    <div class="thabatta-timeline-item">\n      <div class="thabatta-timeline-marker"></div>\n      <div class="thabatta-timeline-content">\n        <div class="thabatta-timeline-date">{{date}}</div>\n        <h3 class="thabatta-timeline-title">{{title}}</h3>\n        <div class="thabatta-timeline-text">{{content}}</div>\n      </div>\n    </div>\n  </div>',
                'css_code' => '.thabatta-timeline{position:relative;padding:20px 0}.thabatta-timeline::before{content:"";position:absolute;top:0;bottom:0;left:20px;width:4px;background:#e9ecef}.thabatta-timeline-item{position:relative;padding-left:50px;margin-bottom:30px}.thabatta-timeline-item:last-child{margin-bottom:0}.thabatta-timeline-marker{position:absolute;top:0;left:0;width:40px;height:40px;border-radius:50%;background:#007bff;z-index:1}.thabatta-timeline-content{padding:20px;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,.1);background:#fff}.thabatta-timeline-date{display:inline-block;padding:5px 10px;background:#f8f9fa;border-radius:4px;margin-bottom:10px;font-weight:bold}.thabatta-timeline-title{margin:0 0 10px;font-size:1.5rem}.thabatta-timeline-text{color:#6c757d}',
                'js_code' => '(root=>{root.querySelectorAll("*").forEach(e=>{e.childNodes.forEach(n=>{if(n.nodeType===3)n.textContent=n.textContent.replace(/{{(\\w+)}}/g,(_,k)=>host.getAttribute("data-"+k)||"")})})})(root);\nconst items=root.querySelectorAll(".thabatta-timeline-item");const obs=new IntersectionObserver(ents=>{ents.forEach(e=>{if(e.isIntersecting){e.target.style.opacity="1";e.target.style.transform="translateX(0)";obs.unobserve(e.target);}}},{threshold:.1});items.forEach(it=>{it.style.opacity="0";it.style.transform="translateX(-20px)";it.style.transition="opacity .5s,transform .5s";obs.observe(it);});',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ],
        ];
    }
}

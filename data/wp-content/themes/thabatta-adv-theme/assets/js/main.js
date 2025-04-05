/**
 * Main JavaScript file for Thabatta Advocacia theme
 */
(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        // Smooth scrolling for anchor links
        $('a[href*="#"]:not([href="#"])').click(function() {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                let target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                    return false;
                }
            }
        });

        // Adicionar classe ao header quando rolar a página
        const $header = $('.site-header');
        const headerHeight = $header.outerHeight();
        const $body = $('body');

        $(window).scroll(function() {
            if ($(window).scrollTop() > headerHeight) {
                $header.addClass('sticky');
                $body.css('padding-top', headerHeight + 'px');
            } else {
                $header.removeClass('sticky');
                $body.css('padding-top', '0');
            }
        });

        // Mobile menu toggle
        $('.menu-toggle').on('click', function() {
            $('.main-navigation').toggleClass('toggled');
            $(this).attr('aria-expanded', function(index, attr) {
                return attr === 'true' ? 'false' : 'true';
            });
        });

        // Dropdown menus for mobile
        $('.menu-item-has-children > a, .page_item_has_children > a').append('<span class="dropdown-toggle"><i class="fas fa-chevron-down"></i></span>');

        $('.dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            $(this).toggleClass('toggle-on');
            $(this).parent().next('.sub-menu, .children').toggleClass('toggled-on');
        });

        // Adicionar funcionalidade de Accordion
        $('.accordion-header').on('click', function() {
            $(this).toggleClass('active');
            $(this).next('.accordion-content').slideToggle(300);
        });

        // Inicializar sliders se o plugin slick estiver carregado
        if (typeof $.fn.slick !== 'undefined') {
            // Slider de Depoimentos
            $('.testimonial-slider').slick({
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: true,
                autoplay: true,
                autoplaySpeed: 5000
            });

            // Slider de Áreas de Atuação
            $('.areas-slider').slick({
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 3,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 4000,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            });
        }

        // Inicializar contador de números
        function startCounter() {
            $('.counter').each(function() {
                const $this = $(this);
                const countTo = $this.attr('data-count');
                
                $({ countNum: $this.text() }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum);
                    }
                });
            });
        }

        // Iniciar contador quando visível
        const $counters = $('.counter');
        if ($counters.length) {
            $(window).on('scroll', function() {
                const windowHeight = $(window).height();
                const scrollTop = $(window).scrollTop();
                
                $counters.each(function() {
                    const $this = $(this);
                    const offsetTop = $this.offset().top;
                    
                    if (scrollTop + windowHeight > offsetTop && !$this.hasClass('counted')) {
                        $this.addClass('counted');
                        startCounter();
                    }
                });
            });
        }

        // Formulário de contato - animação de labels
        $('.contact-form .form-control').on('focus blur', function(e) {
            $(this).parents('.form-group').toggleClass('focused', (e.type === 'focus' || this.value !== ''));
        }).trigger('blur');

        // Botão de voltar ao topo
        const $backToTop = $('.back-to-top');
        $(window).scroll(function() {
            if ($(window).scrollTop() > 300) {
                $backToTop.addClass('show');
            } else {
                $backToTop.removeClass('show');
            }
        });

        $backToTop.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 800);
        });

        // Animação AOS (se existir)
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
        }
    });

})(jQuery); 
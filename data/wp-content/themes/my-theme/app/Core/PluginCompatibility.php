<?php
namespace WPFramework\Core;

class PluginCompatibility {
    public static function init() {
        // Corrige erros do ProSites
        add_action('plugins_loaded', [self::class, 'fix_prosites_errors'], 1);
        
        // Corrige erros do SQLite Database Integration
        add_action('plugins_loaded', [self::class, 'fix_sqlite_errors'], 1);
    }

    public static function fix_prosites_errors() {
        if (!class_exists('ProSites')) {
            return;
        }

        // Corrige propriedades dinâmicas
        add_action('init', function() {
            global $psts;
            if (isset($psts)) {
                $psts->countries = [];
                $psts->usa_states = [];
                $psts->uk_counties = [];
                $psts->australian_states = [];
                $psts->canadian_provinces = [];
                $psts->eu_countries = [];
                $psts->currencies = [];
            }
        }, 1);

        // Corrige chamadas get_class() sem argumentos
        add_filter('get_class', function($class) {
            if (empty($class)) {
                return __CLASS__;
            }
            return $class;
        });
    }

    public static function fix_sqlite_errors() {
        if (!class_exists('WP_SQLite_DB')) {
            return;
        }

        // Corrige addslashes com null
        add_filter('addslashes', function($string) {
            if ($string === null) {
                return '';
            }
            return addslashes($string);
        });
    }
} 
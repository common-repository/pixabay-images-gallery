<?php

/*
Plugin Name: Pixabay Images Gallery
Plugin URI: https://goodies.pixabay.com/javascript/pixabay-widget/demo.html
Description: Use shortcodes to insert responsive Pixabay images galleries similar to Flickr, Google Images and 500px.
Version: 1.10
Author: Simon Steinberger
Author URI: https://pixabay.com/users/Simon/
License: GPLv2
*/


// i18n
function pxigw_load_textdomain() { load_plugin_textdomain('pxigw', false, dirname(plugin_basename(__FILE__ )).'/langs/'); }
add_action('plugins_loaded', 'pxigw_load_textdomain');


// add settings
include(plugin_dir_path(__FILE__).'settings.php');


// include Pixabay Image Gallery Widget JavaScript
function pxigw_enqueue_js_widget() { wp_enqueue_script('pixabayImagesGallery', plugins_url('js/pixabay-widget.min.js', __FILE__ ), array(), false, true); }
add_action('wp_enqueue_scripts', 'pxigw_enqueue_js_widget');


// add async+defer attributes to script tag
function script_loader_tag_handle($tag, $handle) { if ($handle!=='pixabayImagesGallery') return $tag; return str_replace(' src', ' defer async src', $tag); }
add_filter('script_loader_tag', script_loader_tag_handle, 10, 2);


// [pixabay_gallery search="..." user="..." ...]
function render_pxigw_shortcode($atts, $content=null) {
    $defaults = array(
        'key' => '', 'row_height' => 170, 'per_page' => 20, 'max_rows' => 0, 'truncate' => true,
        'lang' => 'en', 'image_type' => 'all', 'safesearch' => false, 'editors_choice' => false, 'order' => 'popular',
        'target' => '', 'navpos' => 'bottom', 'branding' => false, 'prev' => '◄ '.__('PREV', 'pxigw'), 'next' => __('NEXT', 'pxigw').' ►'
    );
    $options = get_option('pxigw_options');

    $o = array();
    foreach (array('key', 'search', 'user', 'lang', 'image_type', 'safesearch', 'editors_choice', 'order', 'page', 'per_page', 'row_height', 'max_rows', 'truncate', 'target', 'navpos', 'branding', 'prev', 'next') as $key) {
        if ($atts && array_key_exists($key, $atts)) $val = $atts[$key];
        elseif ($options && array_key_exists($key, $options)) $val = $options[$key];
        else $val = $defaults[$key];
        $o[$key] = $val;
    }

    $args = array(
        'q' => (trim($o['user']) ? ('user:'.$o['user'].' '.$o['search']) : $o['search']),
        'lang' => $o['lang'],
        'image_type' => $o['image_type'],
        'safesearch' => ($o['safesearch'] && $o['safesearch'] != 'false') ? 'true' : '',
        'editors_choice' => ($o['editors_choice'] && $o['editors_choice'] != 'false') ? 'true' : '',
        'order' => $o['order'],
        'page' => $o['page'],
        'per_page' => $o['per_page']
    );

    if (!$o['key'])
        return '<p><i>[ '.__("To enable the Pixabay Images Gallery, you must obtain an API key from <a href='https://pixabay.com/' target='_blank'>Pixabay</a> and enter it in the plugin's settings.", 'pxigw').' ]</i></p>';

    $query_string = http_build_query($args);
    $cache_hash = md5($query_string);
    $api_called = false;
    if (false == ($data = get_transient($cache_hash))) {
        $data = wp_remote_retrieve_body(wp_remote_get('https://pixabay.com/api/?'.$query_string, array('timeout' => 8)));
        $api_called = true;
        set_transient($cache_hash, $data, 60*60*6);
    }
    $data = json_decode($data, true);

    // prefill grid
    $innerHTML = '';
    if (is_array($data) && array_key_exists('hits', $data)) {
        $class_name = 'pixabay_widget';
        // pagination and branding
        $is_paginated = $data['totalHits'] > $o['per_page'] && $o['prev'] && $o['next'];
        $br = $o['branding'] && $o['branding'] != 'false' ? true : false;
        $nav = '';
        if ($is_paginated || $br) {
            $nav .= ('<div class="noselect '.$class_name.'_nav">');
            if ($br) $nav .= ('<div class="branding">Powered by <a href="https://pixabay.com/" target="'.$o['target'].'">Pixabay</a></div>');
            if ($is_paginated) {
                if ($o['page'] > 1) $nav .= ('<b class="'.$class_name.'_prev">'.$o['prev'].'&nbsp;</b>');
                else $nav .= ('<span>'.$o['prev'].'&nbsp;</span>');
                if ($o['page']*$o['per_page'] < $data['totalHits']) $nav .= ('<b class="'.$class_name.'_next">&nbsp; '.$o['next'].'</b>');
                else $nav .= ('<span>&nbsp; '.$o['next'].'</span>');
            }
            $nav .= '</div>';
        }
        if ($o['navpos'] == 'top') $innerHTML = $nav;
        $blank_px = plugins_url('img/blank.gif', __FILE__ );
        foreach ($data['hits'] as $hit) {
            // echo $hit['pageURL'];
            $w = $hit['previewWidth']; $h = $hit['previewHeight']; $src = $hit['previewURL'];
            if ($o['row_height'] > $h-10) { $w = $w*(180/($h+1)); $h = 180; $src = str_replace('_150', '__180', $src); }
            $innerHTML .= ('<div class="item" data-w="'.$w.'" data-h="'.$h.'"><a title="'.htmlspecialchars(ucwords($hit['tags'])).'" href="'.$hit['pageURL'].'" target="'.$o['target'].'"><img src="'.$blank_px.'" data-src="'.$src.'"></a></div>');
        }
        if ($o['navpos'] == 'bottom') $innerHTML .= $nav;
    }

    $html = '<script>var pixabayWidget = { key: "'.$o['key'].'" };</script><div class="pixabay_widget"'.((count($data['hits']) || (!count($data['hits']) && $api_called)) ? ' data-prefilled="1"' : '');
    foreach (array('search', 'user', 'lang', 'image_type', 'safesearch', 'editors_choice', 'order', 'page', 'per_page', 'row_height', 'max_rows', 'truncate', 'target', 'navpos', 'branding', 'prev', 'next') as $key)
        $html .= ' data-'.str_replace('_', '-', $key).'="'.htmlspecialchars($o[$key]).'"';
    return $html.'>'.$innerHTML.'</div>';
}
add_shortcode('pixabay_gallery', 'render_pxigw_shortcode');

?>

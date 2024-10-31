<?php

$pxigw_languages = array('cs' => 'Čeština', 'da' => 'Dansk', 'de' => 'Deutsch', 'en' => 'English', 'es' => 'Español', 'fr' => 'Français', 'id' => 'Indonesia', 'it' => 'Italiano', 'hu' => 'Magyar', 'nl' => 'Nederlands', 'no' => 'Norsk', 'pl' => 'Polski', 'pt' => 'Português', 'ro' => 'Română', 'sk' => 'Slovenčina', 'fi' => 'Suomi', 'sv' => 'Svenska', 'tr' => 'Türkçe', 'vi' => 'Việt', 'th' => 'ไทย', 'bg' => 'Български', 'ru' => 'Русский', 'el' => 'Ελληνική', 'ja' => '日本語', 'ko' => '한국어', 'zh' => '简体中文');

add_action('admin_menu', 'pxigw_add_settings_menu');
function pxigw_add_settings_menu() {
    add_options_page(__('Pixabay Images Gallery Settings', 'pxigw'), __('Pixabay Images Gallery', 'pxigw'), 'manage_options', 'pxigw_settings', 'pxigw_settings_page');
    add_action('admin_init', 'register_pxigw_options');
}


function register_pxigw_options(){
    register_setting('pxigw_options', 'pxigw_options', 'pxigw_options_validate');
    add_settings_section('pxigw_options_section', '', '', 'pxigw_settings');
    add_settings_field('key-id', 'Pixabay API Key', 'pxigw_render_api_key', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('user-id', 'user', 'pxigw_render_user', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('language-id', 'lang', 'pxigw_render_language', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('image_type-id', 'image_type', 'pxigw_render_image_type', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('safesearch-id', 'safesearch', 'pxigw_render_safesearch', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('editors_choice-id', 'editors_choice', 'pxigw_render_editors_choice', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('order-id', 'order', 'pxigw_render_order', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('per_page-id', 'per_page', 'pxigw_render_per_page', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('row_height-id', 'row_height', 'pxigw_render_row_height', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('max_rows-id', 'max_rows', 'pxigw_render_max_rows', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('truncate-id', 'truncate', 'pxigw_render_truncate', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('target-id', 'target "_blank"', 'pxigw_render_target', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('navpos-id', 'navpos', 'pxigw_render_navpos', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('branding-id', 'branding', 'pxigw_render_branding', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('prev-id', 'prev', 'pxigw_render_prev', 'pxigw_settings', 'pxigw_options_section');
    add_settings_field('next-id', 'next', 'pxigw_render_next', 'pxigw_settings', 'pxigw_options_section');
}


function pxigw_render_api_key(){
    $options = get_option('pxigw_options');
    echo '<input name="pxigw_options[key]" type="text" maxlength="80" value="'.htmlspecialchars($options['key']).'" required>';
}

function pxigw_render_user(){
    $options = get_option('pxigw_options');
    echo '<input name="pxigw_options[user]" type="text" maxlength="30" value="'.htmlspecialchars($options['user']).'">';
}

function pxigw_render_language(){
    global $pxigw_languages;
    $options = get_option('pxigw_options');
    $set_lang = substr(get_locale(), 0, 2);
    if (!$options['language']) $options['language'] = $pxigw_languages[$set_lang]?$set_lang:'en';
    echo '<select name="pxigw_options[language]">';
    foreach ($pxigw_languages as $k => $v) { echo '<option value="'.$k.'"'.($options['language']==$k?' selected="selected"':'').'>'.$v.'</option>'; }
    echo '</select>';
}

function pxigw_render_image_type(){
    $options = get_option('pxigw_options');
    ?>
    <label><input name="pxigw_options[image_type]" value="all" type="radio"<?= !$options['image_type'] | $options['image_type']=='all'?' checked="checked"':''; ?>> all</label>
    <br><label><input name="pxigw_options[image_type]" value="photo" type="radio"<?= $options['image_type']=='photo'?' checked="checked"':''; ?>> photo</label>
    <br><label><input name="pxigw_options[image_type]" value="illustration" type="radio"<?= $options['image_type']=='illustration'?' checked="checked"':''; ?>> illustration</label>
    <?php
}

function pxigw_render_safesearch(){
    $options = get_option('pxigw_options');
    echo '<label><input name="pxigw_options[safesearch]" value="true" type="checkbox"'.($options['safesearch']=='true'?' checked="checked"':'').'></label>';
}

function pxigw_render_editors_choice(){
    $options = get_option('pxigw_options');
    echo '<label><input name="pxigw_options[editors_choice]" value="true" type="checkbox"'.($options['editors_choice']=='true'?' checked="checked"':'').'></label>';
}

function pxigw_render_order(){
    $options = get_option('pxigw_options');
    ?>
    <label><input name="pxigw_options[order]" value="popular" type="radio"<?= !$options['order'] | $options['order']=='popular'?' checked="checked"':''; ?>> popular</label>
    <br><label><input name="pxigw_options[order]" value="latest" type="radio"<?= $options['order']=='latest'?' checked="checked"':''; ?>> latest</label>
    <?php
}

function pxigw_render_per_page(){
    $options = get_option('pxigw_options');
    echo '<input name="pxigw_options[per_page]" type="number" min="3" max="100" value="'.($options['per_page']?$options['per_page']:20).'">';
}

function pxigw_render_row_height(){
    $options = get_option('pxigw_options');
    echo '<input name="pxigw_options[row_height]" type="number" min="30" max="180" value="'.($options['row_height']?$options['row_height']:170).'">';
}

function pxigw_render_max_rows(){
    $options = get_option('pxigw_options');
    echo '<input name="pxigw_options[max_rows]" type="number" min="0" max="99" value="'.($options['max_rows']?$options['max_rows']:0).'"> '.__('0 for unlimited rows', 'pxigw');
}

function pxigw_render_truncate(){
    $options = get_option('pxigw_options');
    echo '<label><input name="pxigw_options[truncate]" value="true" type="checkbox"'.(!$options['truncate'] || $options['truncate']=='true'?' checked="checked"':'').'></label>';
}

function pxigw_render_target(){
    $options = get_option('pxigw_options');
    echo '<label><input name="pxigw_options[target]" value="true" type="checkbox"'.($options['target']=='_blank'?' checked="checked"':'').'> target "_blank"</label>';
}

function pxigw_render_navpos(){
    $options = get_option('pxigw_options');
    ?>
    <label><input name="pxigw_options[navpos]" value="bottom" type="radio"<?= !$options['navpos'] | $options['navpos']=='bottom'?' checked="checked"':''; ?>> bottom</label>
    <br><label><input name="pxigw_options[navpos]" value="top" type="radio"<?= $options['navpos']=='top'?' checked="checked"':''; ?>> top</label>
    <br><label><input name="pxigw_options[navpos]" value="" type="radio"<?= $options['navpos']===''?' checked="checked"':''; ?>> <?php _e('hide', 'pxigw' ); ?></label>
    <?php
}

function pxigw_render_branding(){
    $options = get_option('pxigw_options');
    echo '<label><input name="pxigw_options[branding]" value="true" type="checkbox"'.($options['branding']=='true'?' checked="checked"':'').'></label>';
}

function pxigw_render_prev(){
    $options = get_option('pxigw_options');
    echo '<input name="pxigw_options[prev]" type="text" maxlength="255" value="'.(($options['prev'] === '' or $options['prev'])?htmlspecialchars($options['prev']):('◄ '.__('PREV', 'pxigw'))).'">';
}

function pxigw_render_next(){
    $options = get_option('pxigw_options');
    echo '<input name="pxigw_options[next]" type="text" maxlength="255" value="'.(($options['next'] === '' or $options['next'])?htmlspecialchars($options['next']):(__('NEXT', 'pxigw')).' ►').'">';
}


function pxigw_settings_page() { ?>
    <div class="wrap">
    <h2><?= _e('Pixabay Images Gallery', 'pxigw'); ?></h2>
    <?php
        $o = get_option('pxigw_options');
        if (!$o['key']) {
            echo '<div class="notice notice-info"><p>';
            echo _e("To enable this plugin, you must obtain an API key from Pixabay and enter it in the settings below. After <a href='https://pixabay.com/accounts/register/' target='_blank'>signing up for free on Pixbay</a>, you can <a href='https://pixabay.com/api/docs/' target='_blank'>find your API key here</a>.", 'pxigw');
            echo '</p></div>';
        }
    ?>

    <h3>Shortcode</h3>
    <p>
        <?php _e('Use this shortcode to show Pixabay images galleries on your page:', 'pxigw' ); ?>
        <pre style="margin-left:30px">[pixabay_gallery]</pre>
    </p>
    <p>
        <?php _e('Select images by adding parameters:', 'pxigw' ); ?>
        <pre style="margin-left:30px">[pixabay_gallery user="Unsplash" max_rows=3 branding="false"]</pre>
    </p>
    <br>
    <h3><?php _e('Available Parameters', 'pxigw' ); ?></h3>
    <table>
        <tr><th style="text-align:left"><?php _e('Name', 'pxigw' ); ?></th><th style="text-align:left"><?php _e('Description', 'pxigw' ); ?></th></tr>
        <tr><td>search</td><td><?php _e('A string to search for. Maximum length: 100 characters. Omit to select all images.', 'pxigw' ); ?></td></tr>
        <tr><td>user</td><td><?php _e('A Pixabay username to limit search results to.', 'pxigw' ); ?></td></tr>
        <tr><td>lang</td><td><?php echo __("The language to search in. Accepted values:", 'pxigw')." 'bg', 'cs', 'da', 'de', 'el', 'en', 'es', 'fi', 'fr', 'hu', 'id', 'it', 'ja', 'ko', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sv', 'th', 'tr', 'vi', 'zh'"; ?></td></tr>
        <tr><td>image_type</td><td><?php echo __('A media type to search within. Accepted values:', 'pxigw' ).' "all", "photo", "illustration"'; ?></td></tr>
        <tr><td>safesearch</td><td><?php echo __('A flag indicating that only images suitable for all ages should be returned. Accepted values:', 'pxigw' ).' "true", "false"'; ?></td></tr>
        <tr><td>editors_choice &nbsp;</td><td><?php echo __('Select images that have received an <a href="https://pixabay.com/editors_choice/">Editor\'s Choice</a> award. Accepted values:', 'pxigw' ).' "true", "false"'; ?></td></tr>
        <tr><td>order</td><td><?php echo __('How the results should be ordered. Accepted values:', 'pxigw' ).' "popular", "latest"'; ?></td></tr>
        <tr><td>page</td><td><?php _e('Returned image sets are paginated. Use this parameter to select the page number.', 'pxigw' ); ?></td></tr>
        <tr><td>per_page</td><td><?php echo __('Maximum number of images per next/prev page. Values:', 'pxigw' ).' 3-100'; ?></td></tr>
        <tr><td>row_height</td><td><?php echo __('Maximum height of a row of images in pixels. Values:', 'pxigw' ).' 30 - 180'; ?></td></tr>
        <tr><td>max_rows</td><td><?php _e('Maximum number of rows to display. Images exceeding this row are hidden.', 'pxigw' ); ?></td></tr>
        <tr><td>truncate</td><td><?php _e('Hide <i>incomplete</i> last row of images.', 'pxigw' ); ?></td></tr>
        <tr><td>target</td><td><?php _e("Link target for images, e.g. '_blank.'", 'pxigw' ); ?></td></tr>
        <tr><td>navpos</td><td><?php echo __('Position of pagination links and branding. Leave empty to hide both. Accepted values:', 'pxigw' ).' "bottom", "top"'; ?></td></tr>
        <tr><td>branding</td><td><?php echo __('Whether to show "Powered by Pixabay". Accepted values:', 'pxigw' ).' "true", "false"'; ?></td></tr>
        <tr><td>prev</td><td><?php _e('Text for the "previous" link. Leave empty to hide pagination controls. Default: "◄ PREV"', 'pxigw' ); ?></td></tr>
        <tr><td>next</td><td><?php _e('Text for the "next" link. Leave empty to hide pagination controls. Default: "NEXT ►"', 'pxigw' ); ?></td></tr>
    </table>
    <br>
    <h3><?php _e('Pixabay API Key and Default Settings', 'pxigw' ); ?></h3>
    <form method="post" action="options.php">
        <?php
            settings_fields('pxigw_options');
            do_settings_sections('pxigw_settings');
            submit_button();
        ?>
    </form>
    <hr style="margin-bottom:20px">
    <p>
        Official <a href="https://pixabay.com/"><img src="https://pixabay.com/static/img/logo.png" style="width:120px;margin:0 5px;position:relative;top:5px"></a> plugin by <a href="https://pixabay.com/service/imprint/">Simon Steinberger</a>.
    </p>
    <p>Find us on <a href="https://www.facebook.com/pixabay">Facebook</a>, <a href="https://plus.google.com/+Pixabay">Google+</a> and <a href="https://twitter.com/pixabay">Twitter</a>.</p>
    </div>
<?php }


function pxigw_options_validate($input){
    global $pxigw_languages;
    $options = get_option('pxigw_options');
    $options['key'] = trim($input['key']);
    $options['user'] = substr(trim($input['user']), 0, 30);
    if ($pxigw_languages[$input['language']]) $options['language'] = $input['language'];
    if (in_array($input['image_type'], array('all', 'photo', 'illustration'))) $options['image_type'] = $input['image_type'];
    if ($input['safesearch']) $options['safesearch'] = 'true'; else $options['safesearch'] = '';
    if ($input['editors_choice']) $options['editors_choice'] = 'true'; else $options['editors_choice'] = '';
    if (in_array($input['order'], array('latest', 'popular'))) $options['order'] = $input['order'];
    $per_page = intval($input['per_page']);
    if ($per_page >= 3 and $per_page <= 100) $options['per_page'] = $per_page;
    $row_height = intval($input['row_height']);
    if ($row_height >= 30 and $row_height <= 180) $options['row_height'] = $row_height;
    $max_rows = intval($input['max_rows']);
    if ($max_rows >= 0 and $max_rows <= 99) $options['max_rows'] = $max_rows;
    if ($input['truncate'] === 'true') $options['truncate'] = 'true'; else $options['truncate'] = 'false';
    if ($input['target']) $options['target'] = '_blank'; else $options['target'] = '';
    if (in_array($input['navpos'], array('bottom', 'top', ''))) $options['navpos'] = $input['navpos'];
    if ($input['branding'] === 'true') $options['branding'] = 'true'; else $options['branding'] = 'false';
    $options['prev'] = $input['prev'] ? substr(trim($input['prev']), 0, 255) : '';
    $options['next'] = $input['next'] ? substr(trim($input['next']), 0, 255) : '';
    return $options;
}
?>

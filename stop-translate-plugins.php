<?php
/*
Plugin Name: Stop translate plugins
Description: You can control plugin use translation or not.
Version: 0.1
Text Domain: stop-translate-plugins
Domain Path: /languages/
Author: Simon Chuang
Author URI: https://webdesign.sig.tw
*/

define( 'SIG_ST_OPTIONS', 'sig-stop-translate');

load_plugin_textdomain( 'stop-translate-plugins', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/*----------------------------------------
* add admin menu
*----------------------------------------*/
add_action('admin_menu', 'add_admin_language_menu');

function add_admin_language_menu(){
  add_menu_page('Stop translate','Stop translate','administrator','stop-translate', 'page_unload_textdomain');
}

/*----------------------------------------
* admin page
*----------------------------------------*/
function page_unload_textdomain() {

    if ( ! function_exists( 'get_plugins' ) ) {
	    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $all_plugins = get_plugins();

    $option = get_option(SIG_ST_OPTIONS);

    ?>
    <div class="wrap">
        <h2><?php _e( 'Plugin List', 'stop-translate-plugins' )?></h2>
        <p class="description"><?php _e( 'Select plugin which you want unload translate', 'stop-translate-plugins' )?></p>
        <br>
        <form method="post" action="options.php">
            <?php settings_fields('sig-option-group'); ?>
            <?php settings_errors(); ?>
            <table class="wp-list-table widefat plugins">
                <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'stop-translate-plugins' )?></label><input id="cb-select-all-1" type="checkbox"></td>
                        <th scope="col" id="name" class="manage-column column-name column-primary"><?php _e( 'Plugin', 'stop-translate-plugins' )?></th>
                        <th scope="col" id="description" class="manage-column column-description"><?php _e( 'About Plugin', 'stop-translate-plugins' )?></th>
                    </tr>
                </thead>
                <tbody>
            <?php
                if(count($all_plugins)>0){

                    foreach($all_plugins as $k=>$plug){

                        $Author = $plug['Author'];
                        if(!empty($plug['AuthorURI']))  $Author = "<a href=\"{$plug['AuthorURI']}\">$Author</a>";

                        $checked = (is_array($option) && in_array($plug['TextDomain'],$option)) ? 'checked="checked"':'';
                        $active = (!empty($checked)) ? 'active':'inactive';
            ?>
                <tr class="<?php echo $active;?>" data-plugin="<?php echo $k?>">
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text"><?php echo __( 'Select ', 'stop-translate-plugins' ). $plug['Name']?></label>
                        <input name="<?php echo SIG_ST_OPTIONS?>[]" value="<?php echo $plug['TextDomain']?>" type="checkbox" <?php echo $checked;?>>
                    </th>
                    <td class="plugin-title column-primary">
                        <strong><?php echo $plug['Name']?></strong>
                    </td>
                    <td class="column-description desc">
						<div class="plugin-description"><?php echo $plug['Description']?></div>
						<div class="inactive second plugin-version-author-uri"><?php
                            echo __( 'Version: ', 'stop-translate-plugins' ).$plug['Version'];
                            echo ' | ';
                            echo __( 'Author: ', 'stop-translate-plugins' ).$Author;
                        ?></div>
    				</td>
                </tr>
            <?php
                    }
                }
            ?>
                </tbody>
            </table>

            <p class="description">
                <?php echo __( 'Memory usage: ', 'stop-translate-plugins' ) . round(memory_get_usage() / 1024 / 1024, 2) . ' MB';?>
            </p>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php

}

/*----------------------------------------------------
* register options
*----------------------------------------------------*/
add_action( 'admin_init', 'sig_register_opt_var' );
function sig_register_opt_var() {
    register_setting( 'sig-option-group',SIG_ST_OPTIONS );
}

/*----------------------------------------------------
* do unload textdomain
*----------------------------------------------------*/
add_action( 'init', 'unload_lang' );
function unload_lang(){

    $option = get_option(SIG_ST_OPTIONS);
    if(is_array($option)){
        foreach($option as $domain){
            unload_textdomain( $domain );
        }
    }
}
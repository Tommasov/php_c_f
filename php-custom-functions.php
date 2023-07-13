<?php
/**
 * Plugin Name:       PHP - Custom Functions
 * Plugin URI:        https://github.com/Tommasov/php_c_f
 * Description:       Inserisci codice PHP nel tuo sito web in modo indipendente dal tema scelto
 * Version:           1.1
 * Author:            Tommaso Vietina
 * Author URI:        https://github.com/Tommasov/
 * Text Domain:       php_c_f
 */

include "functions.php";

add_action( 'admin_enqueue_scripts', 'codemirror_enqueue_scripts' );
add_action( 'admin_menu', 'php_c_f_options_page' );

function codemirror_enqueue_scripts( $hook ) {
	$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'php' ) );
	
	wp_localize_script( 'jquery', 'cm_settings', $cm_settings );
	wp_enqueue_script( 'wp-theme-plugin-editor' );
	wp_enqueue_style( 'wp-codemirror' );
}

function php_c_f_options_page() {
	add_menu_page(
		'PHP Custom Functions',
		'PHP',
		'manage_options',
		'php_c_f',
		'php_c_f_options_page_html'
	);
}

function php_c_f_get_current_content() {
	return file_get_contents( plugin_dir_path( __FILE__ ) . "functions.php" );
}

/**
 * @param string $data
 *
 * @return void
 */
function php_c_f_save_content( string $data ) {
	file_put_contents( plugin_dir_path( __FILE__ ) . "functions.php", "" ); //Pulisco il file
	file_put_contents( plugin_dir_path( __FILE__ ) . "functions.php", $data );
}

function php_c_f_options_page_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	if ( isset( $_POST["codice_php"] ) ) {
		php_c_f_save_content( stripcslashes( $_POST["codice_php"] ) );
	}
	
	?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p>Il codice PHP verrà incluso in modo indipendente dal tema. Perfavore utilizza i tag PHP <strong>&lt;?php</strong> /*code*/ <strong>?&gt;</strong></p>
        <h2>functions.php</h2>
        <form method="post">
            <table class="form-table" role="presentation">
                <tbody>
                <tr>
                    <td>
                        <textarea name="codice_php"
                                  id="codice_php"
                                  class="large-text code"
                                  rows="10"
                                  cols="50">
                            <?php echo esc_textarea( php_c_f_get_current_content() ); ?>
                        </textarea>
                    </td>
                </tr>
                </tbody>
            </table>
            <p>
                <button name="codice_php_salva" type="submit" class="button button-primary"><?php echo __( 'Salva', 'php_c_f' ); ?></button>
            </p>
        </form>
        <!--suppress JSUnresolvedReference -->
        <script>
            jQuery(document).ready(function ($) {
                wp.codeEditor.initialize($('#codice_php'), cm_settings);
            })
        </script>
    </div>
	<?php
}
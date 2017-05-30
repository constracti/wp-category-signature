<?php

/*
 * Plugin Name: KGR Category Signature
 * Plugin URI: https://github.com/constracti/wp-category-signature
 * Description: Appends a signature to the content of each post belonging to a category.
 * Author: constracti
 * Version: 1.0
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kgr-category-signature
 * Domain Path: /languages
 */

if ( !defined( 'ABSPATH' ) )
	exit;

add_action( 'plugins_loaded', function() {
	load_plugin_textdomain( 'kgr-category-signature', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );

add_action( 'category_edit_form', function( WP_Term $cat ) {
	$signature = get_term_meta( $cat->term_id, 'kgr-category-signature', TRUE );
?>
<table class="form-table">
	<tbody>
		<tr class="form-field">
			<th scope="row">
				<label for="kgr-category-signature"><?php esc_html_e( 'Signature', 'kgr-category-signature' ); ?></label>
			</th>
			<td>
				<textarea name="kgr-category-signature" id="kgr-category-signature" rows="5" cols="50" class="large-text"><?php echo esc_html( $signature ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Each post belonging to this category will have its content appended with the signature above.', 'kgr-category-signature' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>
<?php
} );

add_action( 'edit_category', function( int $cat_id ) {
	echo 'edit category' . "\n";
	$signature = $_POST['kgr-category-signature'];
	if ( $signature === '' )
		delete_term_meta( $cat_id, 'kgr-category-signature' );
	else
		update_term_meta( $cat_id, 'kgr-category-signature', $signature );
} );

add_filter( 'the_content', function( string $content ): string {
	$cats = get_the_category();
	$visited = [];
	foreach ( $cats as $cat ) {
		while ( TRUE ) {
			if ( in_array( $cat->term_id, $visited ) )
				break;
			$visited[] = $cat->term_id;
			$signature = get_term_meta( $cat->term_id, 'kgr-category-signature', TRUE );
			$content .= $signature;
			if ( $cat->parent === 0 )
				break;
			$cat = get_term( $cat->parent );
		}
	}
	return $content;
} );

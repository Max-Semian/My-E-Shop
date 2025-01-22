<?php
/**
 * Description tab
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

global $post;

$heading = apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'My-E-Shop' ) );

?>

<?php if ( $heading ) : ?>
	<h2><?php echo esc_html( $heading ); ?></h2>
	<?php $acf = get_field('title'); ?><h2><?php echo $acf;?></h2>
	<?php $acf = get_field('description'); ?><p><?php echo $acf;?></p>
	<?php 

		$image = get_field('image');
		$size = 'large'; // (thumbnail, medium, large, full или ваш размер)

		if( $image ) {

			echo wp_get_attachment_image( $image, $size );

		}
	?>
<?php endif; ?>

<?php the_content(); ?>

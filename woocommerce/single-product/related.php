<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$args = array(
	'post_type' => 'product',
	'posts_per_page' => 3,
	'orderby' => 'date',
	'order' => 'DESC',
	'post_status' => 'publish',
	'post__not_in' => [$product->get_id()]
);

$products_query = new WP_Query($args);

if ($products_query->have_posts()) {
	?>
	<div class="related products">
		<h2 class="products__title"><?php esc_html_e('Related Products', 'woocommerce-custom-theme'); ?></h2>

		<div class="products__cards">
			<?php
			while ($products_query->have_posts()) {
				$products_query->the_post();
				$product_id = get_the_ID();
				$product = wc_get_product($product_id);
				$product_url = get_permalink();
				$image = wp_get_attachment_image_src(get_post_thumbnail_id( $product_id ), 'single-post-thumbnail');
				$alt_text = get_post_meta(get_post_thumbnail_id( $product_id ), '_wp_attachment_image_alt', true);
				?>

				<div class="products__product-card">
					<a href="<?php echo esc_url($product_url); ?>">
						<img src="<?php echo esc_url($image[0]); ?>" alt="<?php echo esc_attr($alt_text); ?>" class="products__product-image">
					</a>

					<a href="<?php echo esc_url($product_url); ?>" class="products__product-title-link">
						<h2 class="products__product-title"><?php the_title(); ?></h2>
					</a>

					<?php
					$variation_prices = array();

					if ($product->is_type('variable')) {
						$variations = $product->get_available_variations();

						foreach ($variations as $variation) {
							$variation_id = $variation['variation_id'];
							$variation_obj = wc_get_product($variation_id);
							$variation_prices[] = $variation_obj->get_price();
						}

						$lowest_price = wc_price(min($variation_prices));
						$highest_price = wc_price(max($variation_prices));

						?>
						<span class="products__price"><?php echo $lowest_price .  ' - ' . $highest_price; ?></span>
						<a href="<?php echo esc_url($product_url); ?>" class="products__card-button"><?php esc_html_e('Check options', 'woocommerce-custom-theme'); ?></a>
						<?php
					} else {
						$price = $product->get_price();
						?>
						<span class="products__price"><?php echo wc_price($price); ?></span>
						<?php
						echo apply_filters('woocommerce_loop_add_to_cart_link',
							sprintf('<a href="%s" data-quantity="1" class="products__card-button" %s>Add to cart</a>',
								esc_url($product->add_to_cart_url()),
								esc_attr(isset($class) ? $class : 'button'),
								isset($attributes) ? wc_implode_html_attributes($attributes) : ''
							),
							$product
						);
					}
					?>

				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
	wp_reset_postdata();
}
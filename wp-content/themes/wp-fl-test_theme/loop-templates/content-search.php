<?php
/**
 * Search results partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<article style="margin-bottom: 30px; position: relative; min-height: 190px; display:flex;" <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<aside class="entry-aside mr-3 mt-2 h-100 flex-shrink-0 flex-basis-30">
		<div>
			<a href="<?php the_permalink(); ?>">
				<?php
				if (has_post_thumbnail()) {
					the_post_thumbnail('thumbnail');
				} else
					echo wp_get_attachment_image(80, 'thumbnail');
				?>
			</a>
		</div>
	</aside>

	<main>
		<header class="entry-header">


			<?php
			the_title(
				sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())),
				'</a></h2>'
			);
			?>


			<div class="entry-meta">

				<?php understrap_posted_on(); ?>

			</div><!-- .entry-meta -->


		</header><!-- .entry-header -->

		<div class="entry-summary">

			<?php wp_trim_excerpt(the_excerpt()); ?>

		</div><!-- .entry-summary -->

		<footer class="entry-footer">

			<?php understrap_entry_footer(); ?>

		</footer><!-- .entry-footer -->
	</main>
</article><!-- #post-<?php the_ID(); ?> -->
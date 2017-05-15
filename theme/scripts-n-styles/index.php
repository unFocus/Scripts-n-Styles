<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div>
	<header role="banner"></header>
	<main id="main" role="main">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<header>
					<?php
					the_title();
					echo get_the_tag_list();
					echo get_the_category_list();
					?>
				</header>
				<?php
					the_post_thumbnail();
					the_content();
					wp_link_pages();
				?>
				<?php if ( is_singular() ) comments_template(); ?>
			</article>
		<?php endwhile; endif; ?>
		<nav><?php if ( get_next_posts_link() ) next_posts_link(); if ( get_previous_posts_link() ) previous_posts_link(); ?></nav>
	</main>
	<aside><?php if ( is_active_sidebar( 'sidebar-1' ) ) dynamic_sidebar( 'sidebar-1' ); ?></aside>
	<footer role="contentinfo"></footer>
	</div>
	<?php wp_footer(); ?>
</body>
</html>
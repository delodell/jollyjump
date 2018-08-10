<?php
/**
 * The template for displaying 404 (Not Found) pages.
 */
?>
<?php get_header(); ?>

<div id="page" class="single">
	<div class="content">
		<article class="article">
			<div id="content_box" >
				<div class="single_post">
					<header>
						<div class="title">
							<h1><?php _e('Error 404 Not Found', 'point'); ?></h1>
						</div>
					</header>
					<div class="post-content">
						<p><?php _e('Oops! We couldn\'t find this Page.', 'point'); ?><br/>
						<?php _e('Please check your URL or use the search form below.', 'point'); ?></p>
						<?php get_search_form();?>
						<p class="clear"></p>
					</div><!--.post-content--><!--#error404 .post-->
				</div><!--#content-->
			</div><!--#content_box-->
		</article>
		<?php get_sidebar(); ?>
		<?php get_footer(); ?>
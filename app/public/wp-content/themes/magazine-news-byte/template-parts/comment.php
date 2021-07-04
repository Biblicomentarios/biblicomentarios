<li <?php hoot_attr( 'comment' ); ?>>

	<article>
		<header class="comment-avatar">
			<?php echo get_avatar( $comment ); ?>
			<?php global $post;
			if ( $comment->user_id === $post->post_author ) { ?>
				<div class="comment-by-author"><?php esc_html_e( 'Author', 'magazine-news-byte' ); ?></div>
			<?php } ?>
		</header><!-- .comment-avatar -->

		<div class="comment-content-wrap">

			<div <?php hoot_attr( 'comment-content' ); ?>>
				<?php comment_text(); ?>
			</div><!-- .comment-content -->

			<footer class="comment-meta">
				<div class="comment-meta-block">
					<cite <?php hoot_attr( 'comment-author' ); ?>><?php comment_author_link(); ?></cite>
				</div>
				<div class="comment-meta-block">
					<time <?php hoot_attr( 'comment-published' ); ?>><?php $d = apply_filters( 'comment_date_format', '' ); echo esc_html( get_comment_date( $d ) ); ?></time>
				</div>
				<div class="comment-meta-block">
					<a <?php hoot_attr( 'comment-permalink' ); ?>><?php esc_html_e( 'Permalink', 'magazine-news-byte' ); ?></a>
				</div>
				<?php if ( comments_open() ) : ?>
					<div class="comment-meta-block">
						<?php hoot_comment_reply_link(); ?>
					</div>
				<?php endif; ?>
				<?php edit_comment_link(); ?>
			</footer><!-- .comment-meta -->

		</div><!-- .comment-content-wrap -->

	</article>

<?php /* No closing </li> is needed.  WordPress will know where to add it. */ ?>
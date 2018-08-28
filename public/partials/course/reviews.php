<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$course = new TBS_Course( get_the_ID());

if ( ! comments_open() ) {
	return;
}

?>
<div id="course_reviews" class="tbs-course-reviews">
	<div id="comments">
		<h2 class="tbs-reviews-title"><?php
			if (  $count = $course->get_review_count() ) {
				/* translators: 1: reviews count 2: product name */
				printf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, TBS_i18n::get_domain_name() ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
			} else {
				_e( 'Reviews', 'woocommerce' );
			}
		?></h2>

		<?php if ( have_comments() ) : ?>

			<ol class="commentlist">
				<?php wp_list_comments(); ?>
			</ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links( apply_filters( 'woocommerce_comment_pagination_args', array(
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'type'      => 'list',
				) ) );
				echo '</nav>';
			endif; ?>

		<?php else : ?>

			<p class="tbs-noreviews"><?php _e( 'There are no reviews yet.', TBS_i18n::get_domain_name() ); ?></p>

		<?php endif; ?>
	</div>

	<div id="course_review_form_wrapper">
		<div id="course_review_form">
			<?php
				$commenter = wp_get_current_commenter();

				$comment_form = array(
					'title_reply'          => have_comments() ? __( 'Add a review', TBS_i18n::get_domain_name() ) : sprintf( __( 'Be the first to review &ldquo;%s&rdquo;', TBS_i18n::get_domain_name() ), get_the_title() ),
					'title_reply_to'       => __( 'Leave a Reply to %s', TBS_i18n::get_domain_name() ),
					'title_reply_before'   => '<span id="reply-title" class="comment-reply-title">',
					'title_reply_after'    => '</span>',
					'comment_notes_after'  => '',
					'fields'               => array(
						'author' => '<p class="comment-form-author">' . '<label for="author">' . esc_html__( 'Name', TBS_i18n::get_domain_name() ) . ' <span class="required">*</span></label> ' .
									'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" required /></p>',
						'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', TBS_i18n::get_domain_name() ) . ' <span class="required">*</span></label> ' .
									'<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" required /></p>',
					),
					'label_submit'  => __( 'Submit', TBS_i18n::get_domain_name() ),
					'logged_in_as'  => '',
					'comment_field' => '',
				);

				if ( $account_page_url = wc_get_page_permalink( 'myaccount' ) ) {
					$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', TBS_i18n::get_domain_name() ), esc_url( $account_page_url ) ) . '</p>';
				}

				$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="course_rating">' . esc_html__( 'Your rating', TBS_i18n::get_domain_name() ) . '</label><select name="course_rating" id="course_rating" aria-required="true" required>
					<option value="">' . esc_html__( 'Rate&hellip;', TBS_i18n::get_domain_name() ) . '</option>
					<option value="5">' . esc_html__( 'Perfect', TBS_i18n::get_domain_name() ) . '</option>
					<option value="4">' . esc_html__( 'Good', TBS_i18n::get_domain_name() ) . '</option>
					<option value="3">' . esc_html__( 'Average', TBS_i18n::get_domain_name() ) . '</option>
					<option value="2">' . esc_html__( 'Not that bad', TBS_i18n::get_domain_name() ) . '</option>
					<option value="1">' . esc_html__( 'Very poor', TBS_i18n::get_domain_name() ) . '</option>
				</select></div>';

				$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', TBS_i18n::get_domain_name() ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required></textarea></p>';

				comment_form( $comment_form );
			?>
		</div>
	</div>

	<div class="clear"></div>
</div>

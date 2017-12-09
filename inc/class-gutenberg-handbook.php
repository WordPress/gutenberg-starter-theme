<?php

class Gutenberg_Handbook {

	/**
	 * Append a "Edit on GitHub" link to Handbook document titles
	 */
	public static function filter_the_title_edit_link( $title, $id = null ) {
		// Only apply to the main title for the document
		if ( ! is_singular( 'handbook' )
			|| ! is_main_query()
			|| ! in_the_loop()
			|| $id !== get_queried_object_id() ) {
			return $title;
		}

		$markdown_source = self::get_markdown_edit_link( get_the_ID() );
		if ( ! $markdown_source ) {
			return $title;
		}

		return $title . ' <a class="github-edit" href="' . esc_url( $markdown_source ) . '"><img src="' . esc_url( get_template_directory_uri() . '/assets/images/github-mark.svg' ) . '"> <span>Edit</span></a>';
	}

	/**
	 * WP-CLI Handbook pages are maintained in the GitHub repo, so the edit
	 * link should ridirect to there.
	 */
	public static function redirect_edit_link_to_github( $link, $post_id, $context ) {
		if ( is_admin() ) {
			return $link;
		}
		$post = get_post( $post_id );
		if ( ! $post ) {
			return $link;
		}

		if ( 'handbook' !== $post->post_type ) {
			return $link;
		}

		$markdown_source = self::get_markdown_edit_link( $post_id );
		if ( ! $markdown_source ) {
			return $link;
		}

		if ( 'display' === $context ) {
			$markdown_source = esc_url( $markdown_source );
		}

		return $markdown_source;
	}

	/**
	 * o2 does inline editing, so we also need to remove the class name that it looks for.
	 *
	 * o2 obeys the edit_post capability for displaying the edit link, so we also need to manually
	 * add the edit link if it isn't there - it always redirects to GitHub, so it doesn't need to
	 * obey the edit_post capability in this instance.
	 */
	public static function redirect_o2_edit_link_to_github( $actions, $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return $actions;
		}

		if ( 'handbook' !== $post->post_type ) {
			return $actions;
		}

		$markdown_source = self::get_markdown_edit_link( $post_id );
		if ( ! $markdown_source ) {
			return $actions;
		}

		/*
		 * Define our own edit post action for o2.
		 *
		 * Notable differences from the original are:
		 * - the 'href' parameter always goes to the GitHub source.
		 * - the 'o2-edit' class is missing, so inline editing is disabled.
		 */
		$edit_action = array(
			'action' => 'edit',
			'href' => $markdown_source,
			'classes' => array( 'edit-post-link' ),
			'rel' => $post_id,
			'initialState' => 'default'
		);

		// Find and replace the existing edit action.
		$replaced = false;
		foreach( $actions as &$action ) {
			if ( 'edit' === $action['action'] ) {
				$action = $edit_action;
				$replaced = true;
				break;
			}
		}
		unset( $action );

		// If there was no edit action replaced, add it in manually.
		if ( ! $replaced ) {
			$actions[30] = $edit_action;
		}

		return $actions;
	}

	private static function get_markdown_edit_link( $post_id ) {
		$markdown_source = Import_Gutendocs::get_markdown_source( $post_id );
		if ( is_wp_error( $markdown_source ) ) {
			return '';
		}
		if ( 'raw.githubusercontent.com' !== parse_url( $markdown_source, PHP_URL_HOST )
			|| false !== stripos( $markdown_source, '/edit/master/' ) ) {
			return $markdown_source;
		}
		$markdown_source = preg_replace( '#https?://raw\.githubusercontent\.com/([^/]+/[^/]+)/(.+)#', 'https://github.com/$1/edit/$2', $markdown_source );
		return $markdown_source;
	}
}

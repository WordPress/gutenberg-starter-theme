<?php

class Import_Gutendocs {

	private static $handbook_manifest = 'https://raw.githubusercontent.com/WordPress/gutenberg/task/move-docs-to-w.org/docs/manifest.json';
	private static $input_name = 'wporg-gutenberg-markdown-source';
	private static $meta_key = 'wporg_gutenberg_markdown_source';
	private static $nonce_name = 'wporg-gutenberg-markdown-source-nonce';
	private static $submit_name = 'wporg-gutenberg-markdown-import';
	private static $supported_post_types = array( 'handbook' );
	private static $posts_per_page = 100;

	/**
	 * Register our cron task if it doesn't already exist
	 */
	public static function action_init() {
		if ( ! wp_next_scheduled( 'wporg_gutenberg_manifest_import' ) ) {
			wp_schedule_event( time(), '15_minutes', 'wporg_gutenberg_manifest_import' );
		}
		if ( ! wp_next_scheduled( 'wporg_gutenberg_markdown_import' ) ) {
			wp_schedule_event( time(), '15_minutes', 'wporg_gutenberg_markdown_import' );
		}
	}

	public static function action_wporg_gutenberg_manifest_import() {
		$response = wp_remote_get( self::$handbook_manifest . '?' . uniqid() );
		if ( is_wp_error( $response ) ) {
			return $response;
		} elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'invalid-http-code', 'Markdown source returned non-200 http code.' );
		}
		$manifest = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! $manifest ) {
			return new WP_Error( 'invalid-manifest', 'Manifest did not unfurl properly.' );;
		}
		// Fetch all handbook posts for comparison
		$q = new WP_Query( array(
			'post_type'      => self::$supported_post_types,
			'post_status'    => 'publish',
			'posts_per_page' => self::$posts_per_page,
		) );
		$existing = $q->posts;
		$created = $updated = 0;
		foreach( $manifest as $id => $doc ) {
			$doc['order'] = $id;
			$post_parent = null;
			if ( ! empty( $doc['parent'] ) ) {
				// Find the parent in the existing set
				$parents = wp_filter_object_list( $existing, array( 'post_name' => $doc['parent'] ) );
				if ( ! empty( $parents ) ) {
					$parent = array_shift( $parents );
				} else {
					// Create the parent and add it to the stack
					if ( isset( $manifest[ $doc['parent'] ] ) ) {
						$parent_doc = $manifest[ $doc['parent'] ];
						$parent = self::create_post_from_manifest_doc( $parent_doc );
						if ( $parent ) {
							$created++;
							$existing[] = $parent;
						} else {
							continue;
						}
					} else {
						continue;
					}
				}
				$post_parent = $parent->ID;
			}
			$existing_post = wp_filter_object_list( $existing, array( 'post_name' => $doc['slug'] ) );
			if ( $existing_post ) {
				$existing_post = array_shift( $existing_post );
				$doc['ID'] = $existing_post->ID;
				$post = self::update_post_from_manifest_doc( $doc, $post_parent );
				if ( $post ) {
					$updated++;
					$existing[] = $post;
				}
			} else {
				$post = self::create_post_from_manifest_doc( $doc, $post_parent );
				if ( $post ) {
					$created++;
					$existing[] = $post;
				}
			}
		}
		if ( class_exists( 'WP_CLI' ) ) {
			\WP_CLI::success( "Successfully created {$created} handbook pages, updated {$updated} handbook pages." );
		}
	}

	/**
	 * Create a new handbook page from the manifest document
	 */
	private static function create_post_from_manifest_doc( $doc, $post_parent = null ) {
		$post_data = array(
			'post_type'   => 'handbook',
			'post_status' => 'publish',
			'post_parent' => $post_parent,
			'post_title'  => sanitize_text_field( wp_slash( $doc['title'] ) ),
			'post_name'   => sanitize_title_with_dashes( $doc['slug'] ),
			'menu_order'  => $doc['order'],
		);
		$post_id = wp_insert_post( $post_data );
		if ( ! $post_id ) {
			return false;
		}
		if ( class_exists( 'WP_CLI' ) ) {
			\WP_CLI::log( "Created post {$post_id} for {$doc['title']}." );
		}
		update_post_meta( $post_id, self::$meta_key, esc_url_raw( $doc['markdown_source'] ) );
		return get_post( $post_id );
	}

	/**
	 * Update an existing handbook page from the manifest document
	 */
	private static function update_post_from_manifest_doc( $doc, $post_parent = null ) {
		$post_data = array(
			'ID'          => $doc['ID'],
			'post_parent' => $post_parent,
			'post_title'  => sanitize_text_field( wp_slash( $doc['title'] ) ),
			'menu_order'  => $doc['order'],
		);
		$post_id = wp_update_post( $post_data );
		if ( ! $post_id ) {
			return false;
		}
		if ( class_exists( 'WP_CLI' ) ) {
			\WP_CLI::log( "Updated post {$post_id}: {$doc['title']}." );
		}
		update_post_meta( $post_id, self::$meta_key, esc_url_raw( $doc['markdown_source'] ) );
		return get_post( $post_id );
	}

	public static function action_wporg_gutenberg_markdown_import() {
		$q = new WP_Query( array(
			'post_type'      => self::$supported_post_types,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => self::$posts_per_page,
		) );
		$ids = $q->posts;
		$success = 0;
		foreach( $ids as $id ) {
			$ret = self::update_post_from_markdown_source( $id );
			if ( class_exists( 'WP_CLI' ) ) {
				if ( is_wp_error( $ret ) ) {
					\WP_CLI::warning( $ret->get_error_message() );
				} else {
					\WP_CLI::log( "Updated {$id} from markdown source" );
					$success++;
				}
			}
		}
		if ( class_exists( 'WP_CLI' ) ) {
			$total = count( $ids );
			\WP_CLI::success( "Successfully updated {$success} of {$total} handbook pages." );
		}
	}

	/**
	 * Handle a request to import from the markdown source
	 */
	public static function action_load_post_php() {
		if ( ! isset( $_GET[ self::$submit_name ] )
			|| ! isset( $_GET[ self::$nonce_name ] )
			|| ! isset( $_GET['post'] ) ) {
			return;
		}
		$post_id = (int) $_GET['post'];
		if ( ! current_user_can( 'edit_post', $post_id )
			|| ! wp_verify_nonce( $_GET[ self::$nonce_name ], self::$input_name )
			|| ! in_array( get_post_type( $post_id ), self::$supported_post_types, true ) ) {
			return;
		}

		$response = self::update_post_from_markdown_source( $post_id );
		if ( is_wp_error( $response ) ) {
			wp_die( $response->get_error_message() );
		}

		wp_safe_redirect( get_edit_post_link( $post_id, 'raw' ) );
		exit;
	}

	/**
	 * Add an input field for specifying Markdown source
 	 */
	public static function action_edit_form_after_title( $post ) {
		if ( ! in_array( $post->post_type, self::$supported_post_types, true ) ) {
			return;
		}
		$markdown_source = get_post_meta( $post->ID, self::$meta_key, true );
		?>
		<label>Markdown source: <input
			type="text"
			name="<?php echo esc_attr( self::$input_name ); ?>"
			value="<?php echo esc_attr( $markdown_source ); ?>"
			placeholder="Enter a URL representing a markdown file to import"
			size="50" />
		</label> <?php
			if ( $markdown_source ) :
				$update_link = add_query_arg( array(
					self::$submit_name => 'import',
					self::$nonce_name  => wp_create_nonce( self::$input_name ),
				), get_edit_post_link( $post->ID, 'raw' ) );
				?>
				<a class="button button-small button-primary" href="<?php echo esc_url( $update_link ); ?>">Import</a>
			<?php endif; ?>
		<?php wp_nonce_field( self::$input_name, self::$nonce_name ); ?>
		<?php
	}

	/**
	 * Save the Markdown source input field
	 */
	public static function action_save_post( $post_id ) {

		if ( ! isset( $_POST[ self::$input_name ] )
			|| ! isset( $_POST[ self::$nonce_name ] )
			|| ! in_array( get_post_type( $post_id ), self::$supported_post_types, true ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST[ self::$nonce_name ], self::$input_name ) ) {
			return;
		}

		$markdown_source = '';
		if ( ! empty( $_POST[ self::$input_name ] ) ) {
			$markdown_source = esc_url_raw( $_POST[ self::$input_name ] );
		}
		update_post_meta( $post_id, self::$meta_key, $markdown_source );
	}

	/**
	 * Filter cron schedules to add a 15 minute schedule
	 */
	public static function filter_cron_schedules( $schedules ) {
		$schedules['15_minutes'] = array(
			'interval' => 15 * MINUTE_IN_SECONDS,
			'display'  => '15 minutes'
		);
		return $schedules;
	}

	/**
	 * Update a post from its Markdown source
	 */
	private static function update_post_from_markdown_source( $post_id ) {
		$markdown_source = self::get_markdown_source( $post_id );
		if ( is_wp_error( $markdown_source ) ) {
			return $markdown_source;
		}
		if ( ! function_exists( 'jetpack_require_lib' ) ) {
			return new WP_Error( 'missing-jetpack-require-lib', 'jetpack_require_lib() is missing on system.' );
		}

		// Transform GitHub repo HTML pages into their raw equivalents
		$markdown_source = preg_replace( '#https?://github\.com/([^/]+/[^/]+)/blob/(.+)#', 'https://raw.githubusercontent.com/$1/$2', $markdown_source );
		$markdown_source = add_query_arg( 'v', time(), $markdown_source );
		$response = wp_remote_get( $markdown_source );
		if ( is_wp_error( $response ) ) {
			return $response;
		} elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'invalid-http-code', 'Markdown source returned non-200 http code.' );
		}

		$markdown = wp_remote_retrieve_body( $response );
		// Strip YAML doc from the header
		$markdown = preg_replace( '#^---(.+)---#Us', '', $markdown );

		$title = null;
		if ( preg_match( '/^#\s(.+)/', $markdown, $matches ) ) {
			$title = $matches[1];
			$markdown = preg_replace( '/^#\s(.+)/', '', $markdown );
		}

		// Transform to HTML
		jetpack_require_lib( 'markdown' );
		$parser = new \WPCom_GHF_Markdown_Parser;
		$html = $parser->transform( $markdown );

		// Turn the code blocks into tabs
		$html = preg_replace_callback( '/{%\s+codetabs\s+%}(.*?){%\s+end\s+%}/ms', array( 'Import_Gutendocs', 'parse_code_blocks' ), $html );
		$html = str_replace( 'class="php"', 'class="language-php"', $html );
		$html = str_replace( 'class="js"', 'class="language-javascript"', $html );
		$html = str_replace( 'class="css"', 'class="language-css"', $html );

		// Save the post
		$post_data = array(
			'ID'           => $post_id,
			'post_content' => wp_filter_post_kses( wp_slash( $html ) ),
		);
		if ( ! is_null( $title ) ) {
			$post_data['post_title'] = sanitize_text_field( wp_slash( $title ) );
		}
		wp_update_post( $post_data );
		return true;
	}

	/**
	 * Callback for the preg_replace_callback() in ::update_post_from_markdown_source(),
	 * to transform a block of code tabs into HTML.
	 */
	public static function parse_code_blocks( $matches ) {
		$splitted_tabs = preg_split( '/{%\s+([\w]+)\s+%}/', trim( $matches[1] ), -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );

		$html = '<div class="code-tabs">';
		$code_blocks = '';

		for ( $ii = 0; $ii < count( $splitted_tabs ); $ii += 2 ) {
			$classes = 'code-tab ' . $splitted_tabs[ $ii ];
			$code_classes = 'code-tab-block ' . $splitted_tabs[ $ii ];

			if ( 0 === $ii ) {
				$classes .= ' is-active';
				$code_classes .= ' is-active';
			}

			$html .= "<button data-language='{$splitted_tabs[ $ii ]}' class='$classes'>{$splitted_tabs[ $ii ]}</button>";
			$code_blocks .= "<div class='$code_classes'>{$splitted_tabs[ $ii + 1 ]}</div>";
		}

		$html .= "$code_blocks</div>";

		return $html;
	}

	/**
	 * Retrieve the markdown source URL for a given post.
	 */
	public static function get_markdown_source( $post_id ) {
		$markdown_source = get_post_meta( $post_id, self::$meta_key, true );
		if ( ! $markdown_source ) {
			return new WP_Error( 'missing-markdown-source', "Markdown source is missing for post $post_id." );
		}

		return $markdown_source;
	}
}

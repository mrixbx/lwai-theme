<?php
/**
 * LWAI functions and definitions
 *
 * @package LWAI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'lwai_setup' ) ) :
	function lwai_setup() {
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array(
			'search-form', 'comment-form', 'comment-list',
			'gallery', 'caption', 'style', 'script',
		) );
		add_theme_support( 'custom-logo', array(
			'height'      => 100,
			'width'       => 400,
			'flex-width'  => true,
			'flex-height' => true,
			'header-text' => array( 'site-title', 'site-description' ),
		) );

		// Navigation menus
		register_nav_menus( array(
			'primary' => esc_html__( 'Main Navigation', 'lwai' ),
			'footer'  => esc_html__( 'Footer Navigation', 'lwai' ),
		) );

		// Custom image sizes
		add_image_size( 'card-thumb',    400, 267, true );
		add_image_size( 'compact-thumb', 120, 80,  true );
		add_image_size( 'hero',          1200, 675, true );
	}
endif;
add_action( 'after_setup_theme', 'lwai_setup' );

// Native lazy loading for all images
add_filter( 'wp_lazy_loading_enabled', '__return_true' );

/**
 * Enqueue scripts and styles.
 */
function lwai_scripts() {
	// Google Fonts - FT Stack
	wp_enqueue_style(
		'lwai-fonts',
		'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500&family=Libre+Franklin:wght@400;600;700&display=swap',
		array(),
		null
	);

	// Bootstrap CSS
	wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3' );

	// Main stylesheet
	wp_enqueue_style( 'lwai-style', get_stylesheet_uri(), array('bootstrap-css'), wp_get_theme()->get( 'Version' ) );

	// Bootstrap JS
	wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), '5.3.3', true );

	// Consolidated JS
	wp_enqueue_script(
		'lwai-scripts',
		get_template_directory_uri() . '/assets/js/lwai.js',
		array('bootstrap-js'),
		wp_get_theme()->get( 'Version' ),
		true   // load in footer
	);
}
add_action( 'wp_enqueue_scripts', 'lwai_scripts' );

/**
 * Custom thumbnail output helper.
 */
if ( ! function_exists( 'lwai_post_thumbnail' ) ) :
	function lwai_post_thumbnail( $size = 'card-thumb' ) {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}
		if ( is_singular() ) {
			?>
			<div class="post-thumbnail">
				<?php the_post_thumbnail( $size, array( 'loading' => 'lazy' ) ); ?>
			</div>
			<?php
		} else {
			?>
			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php the_post_thumbnail( $size, array( 'loading' => 'lazy', 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
			</a>
			<?php
		}
	}
endif;

/**
 * Customizer: Logo, Site Title, Favicon, Footer Text.
 */
function lwai_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'lwai_theme_options', array(
		'title'       => __( 'Theme Options', 'lwai' ),
		'priority'    => 20,
		'description' => __( 'Configure logo, site identity, and footer text.', 'lwai' ),
	) );

	// Move core identity controls into our unified section
	foreach ( array( 'custom_logo', 'blogname', 'blogdescription', 'site_icon' ) as $ctrl ) {
		$c = $wp_customize->get_control( $ctrl );
		if ( $c ) {
			$c->section = 'lwai_theme_options';
		}
	}

	// Custom footer text
	$wp_customize->add_setting( 'lwai_footer_text', array(
		'default'           => '&copy; ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ) . '. All rights reserved.',
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( 'lwai_footer_text', array(
		'label'   => __( 'Footer Copyright Text', 'lwai' ),
		'section' => 'lwai_theme_options',
		'type'    => 'textarea',
	) );
}
add_action( 'customize_register', 'lwai_customize_register', 20 );

/**
 * Automatically inject a Table of Contents (TOC) to Single Posts.
 */
function lwai_add_toc_to_content( $content ) {
    if ( ! is_single() || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    $toc_items = array();
    $counter = 1;

    // Find H2, H3 tags and add an ID attribute (H1 is the article title — skip it)
    $content = preg_replace_callback( '/<h([2-3])([^>]*)>(.*?)<\/h\1>/s', function( $matches ) use ( &$toc_items, &$counter ) {
        $level = intval( $matches[1] );
        $title_html = $matches[3];
        $title_text = strip_tags( $title_html );
        $slug = sanitize_title( $title_text ) . '-' . $counter;
        
        $toc_items[] = array(
            'level' => $level,
            'slug'  => $slug,
            'title' => $title_text
        );
        $counter++;
        
        // Return heading with injected id
        return sprintf( '<h%d%s id="%s" style="scroll-margin-top: 100px;">%s</h%d>', $level, $matches[2], $slug, $title_html, $level );
    }, $content );

    if ( empty( $toc_items ) ) {
        return $content; // No H1-H3 tags found
    }

    // Determine the minimum headline depth to scale indents properly
    $min_level = min( array_column( $toc_items, 'level' ) );

    // Build the TOC HTML
    $toc = '<nav class="article-toc mb-5" style="border: 1px solid var(--ft-border); background-color: var(--ft-bg-card); font-family: var(--ff-ui);">';
    $toc .= '<div class="d-flex justify-content-between align-items-center p-3" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#tocCollapse" aria-expanded="true" aria-controls="tocCollapse">';
    $toc .= '<p class="mb-0 fw-bold" style="font-family: var(--ff-heading); letter-spacing: 0.5px; color: var(--ft-text-headline); font-size: 1.1rem;">Contents</p>';
    $toc .= '<span style="color: var(--ft-text-headline);"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg></span>';
    $toc .= '</div>';
    
    $toc .= '<div id="tocCollapse" class="collapse show" style="border-top: 1px solid var(--ft-border);">';
    $toc .= '<div class="p-3">';
    $toc .= '<ol class="mb-0 ps-3" style="line-height: 1.6;">';
    
    $current_depth = $min_level;

    foreach ( $toc_items as $index => $item ) {
        $level = $item['level'];

        // Open submenus as needed
        if ( $level > $current_depth ) {
            while ( $current_depth < $level ) {
                $toc .= '<ol class="ps-4 mb-0 mt-2">';
                $current_depth++;
            }
        } elseif ( $level < $current_depth ) {
            // Close submenus as needed
            while ( $current_depth > $level ) {
                $toc .= '</ol>';
                $current_depth--;
            }
        }
        
        $fw = ( $level == $min_level ) ? 'fw-bold' : '';
        $border = 'border-bottom';
        
        $toc .= sprintf(
            '<li class="%s %s py-2" style="border-color: var(--ft-border) !important;"><a href="#%s" class="text-decoration-none hover-primary d-block" style="color: var(--ft-text-headline);">%s</a></li>',
            $fw,
            $border,
            $item['slug'],
            $item['title']
        );
    }
    
    // Close any remaining open submenus
    while ( $current_depth > $min_level ) {
        $toc .= '</ol>';
        $current_depth--;
    }
    
    $toc .= '</ol></div></div></nav>';

    return $toc . $content;
}
add_filter( 'the_content', 'lwai_add_toc_to_content' );

/**
 * Add Custom Avatar Field to User Profile
 */
function lwai_custom_user_profile_fields( $user ) {
    // Add media uploader script natively for this page
    wp_enqueue_media();
    $custom_avatar = get_the_author_meta( 'lwai_custom_avatar', $user->ID );
    ?>
    <h3>Custom Avatar</h3>
    <table class="form-table">
        <tr>
            <th><label for="lwai_custom_avatar">Profile Image URL</label></th>
            <td>
                <input type="text" name="lwai_custom_avatar" id="lwai_custom_avatar" value="<?php echo esc_attr( $custom_avatar ); ?>" class="regular-text" />
                <button type="button" class="button" id="lwai_upload_avatar_btn">Upload Image</button>
                <br>
                <span class="description">Upload an image to override the default Gravatar.</span>
                <div style="margin-top: 10px;">
                    <img id="lwai_avatar_preview" src="<?php echo esc_url( $custom_avatar ); ?>" style="max-width: 150px; <?php echo empty( $custom_avatar ) ? 'display:none;' : ''; ?>" />
                </div>
            </td>
        </tr>
    </table>
    <script>
    jQuery(document).ready(function($){
        var mediaUploader;
        $('#lwai_upload_avatar_btn').click(function(e) {
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Avatar',
                button: { text: 'Choose Avatar' },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#lwai_custom_avatar').val(attachment.url);
                $('#lwai_avatar_preview').attr('src', attachment.url).show();
            });
            mediaUploader.open();
        });
    });
    </script>
    <?php
}
add_action( 'show_user_profile', 'lwai_custom_user_profile_fields' );
add_action( 'edit_user_profile', 'lwai_custom_user_profile_fields' );

/**
 * Save Custom Avatar Field
 */
function lwai_save_custom_user_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    if ( isset( $_POST['lwai_custom_avatar'] ) ) {
        update_user_meta( $user_id, 'lwai_custom_avatar', esc_url_raw( $_POST['lwai_custom_avatar'] ) );
    }
}
add_action( 'personal_options_update', 'lwai_save_custom_user_profile_fields' );
add_action( 'edit_user_profile_update', 'lwai_save_custom_user_profile_fields' );

/**
 * Override Default Avatar with Custom Avatar
 */
function lwai_custom_avatar_url( $args, $id_or_email ) {
    $user_id = 0;
    
    // Resolve user ID from various types passed to get_avatar
    if ( is_numeric( $id_or_email ) ) {
        $user_id = (int) $id_or_email;
    } elseif ( is_string( $id_or_email ) && ( $user = get_user_by( 'email', $id_or_email ) ) ) {
        $user_id = $user->ID;
    } elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
        $user_id = (int) $id_or_email->user_id;
    } elseif ( $id_or_email instanceof WP_Post ) {
        $user_id = (int) $id_or_email->post_author;
    } elseif ( $id_or_email instanceof WP_Comment ) {
        if ( ! empty( $id_or_email->user_id ) ) {
            $user_id = (int) $id_or_email->user_id;
        }
    }
    
    if ( $user_id ) {
        $custom_avatar = get_user_meta( $user_id, 'lwai_custom_avatar', true );
        if ( $custom_avatar ) {
            $args['url'] = $custom_avatar;
        }
    }
    
    return $args;
}
add_filter( 'get_avatar_data', 'lwai_custom_avatar_url', 10, 2 );

/**
 * On theme activation: set permalink structure and category base, then flush.
 * The category_base '.' removes /category/ from archive URLs natively.
 */
add_action( 'after_switch_theme', function () {
	update_option( 'permalink_structure', '/%category%/%postname%/' );
	update_option( 'category_base', '.' );
	flush_rewrite_rules();
} );

/**
 * Set 9 posts per page on category and author archive pages.
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_admin() && $query->is_main_query() && ( $query->is_category() || $query->is_author() ) ) {
		$query->set( 'posts_per_page', 9 );
	}
} );

/**
 * Permalink: prefer a non-"Breaking" category slug in the URL.
 * Respects Yoast primary category when set; otherwise falls back to first
 * non-breaking category. Only keeps "breaking" when it is the sole category.
 */
add_filter( 'post_link_category', function ( $cat, $cats, $post ) {
	// Honour Yoast primary category if one is set and it is not "breaking".
	$primary_id = (int) get_post_meta( $post->ID, '_yoast_wpseo_primary_category', true );
	if ( $primary_id ) {
		foreach ( $cats as $c ) {
			if ( (int) $c->term_id === $primary_id && 'breaking' !== $c->slug ) {
				return $c;
			}
		}
	}

	// Fallback: skip the "breaking" category.
	if ( 'breaking' !== $cat->slug ) {
		return $cat;
	}
	foreach ( $cats as $c ) {
		if ( 'breaking' !== $c->slug ) {
			return $c;
		}
	}
	return $cat;
}, 10, 3 );

/**
 * Remove the /category/ base from category archive URLs.
 * Sets the option once and flushes rewrite rules only when the value changes,
 * so there is no per-request overhead.
 */
add_action( 'init', function () {
	if ( get_option( 'category_base' ) !== '.' ) {
		update_option( 'category_base', '.' );
		flush_rewrite_rules( false );
	}
} );

/**
 * Redirect date archives (/2025/, /2025/05/, /2025/05/29/) to homepage.
 * Date archives are duplicate content with no editorial value on this site.
 */
add_action( 'template_redirect', function () {
	if ( is_date() ) {
		wp_redirect( home_url( '/' ), 301 );
		exit;
	}
}, 1 );

/**
 * Redirect the /breaking/ category archive to the homepage.
 * "Breaking" is used as a post label, not a browsable section.
 */
add_action( 'template_redirect', function () {
	if ( is_category( 'breaking' ) ) {
		wp_redirect( home_url( '/' ), 301 );
		exit;
	}
}, 1 );

/**
 * Fix: Redirect attachment pages to their parent post.
 * Prevents WordPress from resolving /%category%/%postname%/ URLs as
 * attachment/image pages instead of the actual post.
 */
add_action( 'template_redirect', function () {
	if ( ! is_attachment() ) {
		return;
	}
	$parent_id = wp_get_post_parent_id( get_the_ID() );
	if ( $parent_id ) {
		wp_redirect( get_permalink( $parent_id ), 301 );
	} else {
		wp_redirect( home_url( '/' ), 301 );
	}
	exit;
} );

/**
 * Auto-redirect slug-only or wrong-category URLs to the canonical permalink.
 *
 * Handles two cases:
 *  - /article-slug/           → 301 to /correct-category/article-slug/
 *  - /any-category/article-slug/ that 404s (e.g. old "breaking" URL after
 *    the category was swapped) → 301 to the canonical permalink
 *
 * Runs on priority 5 so it fires before other template_redirect hooks.
 */
add_action( 'template_redirect', function () {
	if ( ! is_404() ) {
		return;
	}

	$path     = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
	$segments = array_values( array_filter( explode( '/', $path ) ) );

	if ( empty( $segments ) ) {
		return;
	}

	// The post slug is always the last path segment.
	$slug = sanitize_title( end( $segments ) );

	if ( empty( $slug ) ) {
		return;
	}

	$posts = get_posts( array(
		'name'           => $slug,
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	) );

	if ( ! empty( $posts ) ) {
		wp_redirect( get_permalink( $posts[0]->ID ), 301 );
		exit;
	}
}, 5 );

/**
 * Noindex thin/duplicate archive pages: search, tags, and date archives.
 */
add_action( 'wp_head', function () {
	if ( is_search() || is_tag() || is_date() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	}
} );

/**
 * C-7: Exclude attachment pages from the core XML sitemap.
 * Attachment pages are thin-content and should not be indexed.
 */
add_filter( 'wp_sitemaps_post_types', function ( $post_types ) {
	unset( $post_types['attachment'] );
	return $post_types;
} );

/**
 * robots.txt: block thin content + explicitly allow AI search crawlers.
 * Each crawler gets its own group so their Allow overrides any wildcard block.
 */
add_filter( 'robots_txt', function ( $output, $public ) {
	// Thin/duplicate paths — applied to all crawlers via the wildcard group
	$output .= "\nDisallow: /?s=\nDisallow: /attachment/\nDisallow: /tag/\nDisallow: /20\n";

	// Reference the news sitemap so Google News picks it up automatically
	$output .= "\nSitemap: " . home_url( '/news-sitemap.xml' ) . "\n";

	// Explicitly allow AI search engine crawlers (they respect robots.txt)
	$ai_bots = array(
		'GPTBot',           // ChatGPT / OpenAI
		'Google-Extended',  // Gemini / Google AI
		'PerplexityBot',    // Perplexity AI
		'anthropic-ai',     // Claude web index
		'ClaudeBot',        // Claude
		'Applebot-Extended',// Apple Intelligence
		'Meta-ExternalAgent',// Meta AI
		'cohere-ai',        // Cohere
		'YouBot',           // You.com
	);
	foreach ( $ai_bots as $bot ) {
		$output .= "\nUser-agent: {$bot}\nAllow: /\n";
	}

	return $output;
}, 10, 2 );

// ============================================================
// AI & NEWS SEARCH ENGINE INTEGRATION
// ============================================================

/**
 * IndexNow key — used to verify site ownership with Bing / Yandex / others.
 * The key is served at /{LWAI_INDEXNOW_KEY}.txt and referenced in pings.
 */
define( 'LWAI_INDEXNOW_KEY', 'lwai7e8912ab3cd45f01a7f3d8e2b1c940' );

/**
 * Register URL endpoints for: Google News sitemap, IndexNow key, llms.txt.
 */
add_action( 'init', function () {
	add_rewrite_rule( '^news-sitemap\.xml$',                    'index.php?lwai_news_sitemap=1', 'top' );
	add_rewrite_rule( '^' . LWAI_INDEXNOW_KEY . '\.txt$',      'index.php?lwai_indexnow_key=1', 'top' );
	add_rewrite_rule( '^llms\.txt$',                            'index.php?lwai_llms_txt=1',     'top' );
} );

add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'lwai_news_sitemap';
	$vars[] = 'lwai_indexnow_key';
	$vars[] = 'lwai_llms_txt';
	return $vars;
} );

/**
 * Serve news-sitemap.xml, IndexNow key file, and llms.txt.
 */
add_action( 'template_redirect', function () {
	if ( get_query_var( 'lwai_news_sitemap' ) ) {
		lwai_serve_news_sitemap();
	}
	if ( get_query_var( 'lwai_indexnow_key' ) ) {
		header( 'Content-Type: text/plain; charset=UTF-8' );
		echo LWAI_INDEXNOW_KEY;
		exit;
	}
	if ( get_query_var( 'lwai_llms_txt' ) ) {
		lwai_serve_llms_txt();
	}
}, 1 );

/**
 * Generate and stream the Google News sitemap.
 * Covers posts from the last 7 days (Google News focuses on recent content).
 */
function lwai_serve_news_sitemap() {
	header( 'Content-Type: application/xml; charset=UTF-8' );
	header( 'X-Robots-Tag: noindex' );

	$posts = get_posts( array(
		'posts_per_page' => 1000,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'date_query'     => array( array( 'after' => '7 days ago' ) ),
		'no_found_rows'  => true,
	) );

	$pub_name = get_bloginfo( 'name' );

	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
	echo '        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

	foreach ( $posts as $post ) {
		echo "\t<url>\n";
		echo "\t\t<loc>" . esc_url( get_permalink( $post->ID ) ) . "</loc>\n";
		echo "\t\t<news:news>\n";
		echo "\t\t\t<news:publication>\n";
		echo "\t\t\t\t<news:name>" . esc_html( $pub_name ) . "</news:name>\n";
		echo "\t\t\t\t<news:language>en</news:language>\n";
		echo "\t\t\t</news:publication>\n";
		echo "\t\t\t<news:publication_date>" . esc_html( get_the_date( 'c', $post->ID ) ) . "</news:publication_date>\n";
		echo "\t\t\t<news:title>" . esc_html( get_the_title( $post->ID ) ) . "</news:title>\n";
		echo "\t\t</news:news>\n";
		echo "\t</url>\n";
	}

	echo '</urlset>';
	exit;
}

/**
 * Generate and stream llms.txt — a plain-text site description for AI systems.
 * Spec: https://llmstxt.org
 */
function lwai_serve_llms_txt() {
	header( 'Content-Type: text/plain; charset=UTF-8' );

	$name = get_bloginfo( 'name' );
	$desc = get_bloginfo( 'description' );
	$url  = trailingslashit( home_url() );
	$cats = get_categories( array( 'hide_empty' => true, 'number' => 20, 'orderby' => 'count', 'order' => 'DESC' ) );

	$out  = "# {$name}\n\n";
	$out .= "> {$desc}\n\n";
	$out .= "{$name} is an AI journalism publication covering artificial intelligence news, analysis, ";
	$out .= "research, and business insights. Content is written by journalists and editors.\n\n";
	$out .= "## Sections\n\n";
	foreach ( $cats as $cat ) {
		$out .= "- [{$cat->name}](" . get_category_link( $cat->term_id ) . "): {$cat->description}\n";
	}
	$out .= "\n## Feeds & Discovery\n\n";
	$out .= "- [RSS Feed]({$url}feed/)\n";
	$out .= "- [News Sitemap]({$url}news-sitemap.xml)\n";
	$out .= "- [Full Sitemap]({$url}wp-sitemap.xml)\n\n";
	$out .= "## Usage\n\n";
	$out .= "Content may be indexed and cited by AI systems. ";
	$out .= "Attribution to {$name} ({$url}) is appreciated.\n";

	echo $out;
	exit;
}

/**
 * Reference the Google News sitemap in WordPress's built-in sitemap index.
 */
add_filter( 'wp_sitemaps_index_entry', function ( $sitemap_url, $provider_name ) {
	return $sitemap_url;
}, 10, 2 );

/**
 * Ping IndexNow (Bing hub) the moment a post is published.
 * Bing distributes the ping to Yandex, Seznam.cz, and other IndexNow partners.
 * Non-blocking — runs in the background, does not slow down post saving.
 */
add_action( 'publish_post', function ( $post_id ) {
	$url = get_permalink( $post_id );
	if ( ! $url ) {
		return;
	}

	$key      = LWAI_INDEXNOW_KEY;
	$key_url  = home_url( "/{$key}.txt" );
	$ping_url = add_query_arg( array(
		'url'         => rawurlencode( $url ),
		'key'         => $key,
		'keyLocation' => rawurlencode( $key_url ),
	), 'https://api.indexnow.org/indexnow' );

	wp_remote_get( $ping_url, array(
		'timeout'  => 3,
		'blocking' => false,
		'headers'  => array( 'User-Agent' => 'LWAI WordPress Theme/1.0' ),
	) );
}, 10, 1 );

/**
 * C-1: Output JSON-LD structured data via wp_head.
 * - WebSite (front page only, enables Sitelinks Searchbox)
 * - NewsArticle (single posts)
 * - Organization (publisher block, reused in NewsArticle)
 */
function lwai_output_schema() {
	$logo_id  = get_theme_mod( 'custom_logo' );
	$logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';

	$org = array(
		'@type' => 'Organization',
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
	);
	if ( $logo_url ) {
		$org['logo'] = array( '@type' => 'ImageObject', 'url' => $logo_url );
	}

	if ( is_front_page() ) {
		$schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'WebSite',
			'name'            => get_bloginfo( 'name' ),
			'url'             => home_url( '/' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}

	if ( is_single() ) {
		$img_src = '';
		if ( has_post_thumbnail() ) {
			$_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'hero' );
			if ( $_img ) { $img_src = $_img[0]; }
		}

		$schema = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'NewsArticle',
			'headline'      => get_the_title(),
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'url'           => get_permalink(),
			'author'        => array( '@type' => 'Person', 'name' => get_the_author() ),
			'publisher'     => $org,
		);
		if ( $img_src ) {
			$schema['image'] = array( '@type' => 'ImageObject', 'url' => $img_src, 'width' => 1200, 'height' => 675 );
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";

		// BreadcrumbList as JSON-LD (invisible — no visible breadcrumb UI)
		$cats = get_the_category();
		$crumb_items = array(
			array( '@type' => 'ListItem', 'position' => 1, 'name' => get_bloginfo( 'name' ), 'item' => home_url( '/' ) ),
		);
		if ( $cats ) {
			$crumb_items[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => $cats[0]->name, 'item' => get_category_link( $cats[0]->term_id ) );
			$crumb_items[] = array( '@type' => 'ListItem', 'position' => 3, 'name' => get_the_title() );
		} else {
			$crumb_items[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => get_the_title() );
		}
		$breadcrumb = array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $crumb_items );
		echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'lwai_output_schema' );


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
 * Remove /category/ prefix from category URLs.
 *
 * Strategy: filter generated links to strip /category/, then register a specific
 * rewrite rule for every known category slug so WordPress can resolve them.
 * Flush runs on theme activation and whenever a category is created or updated.
 */
function lwai_remove_category_base( $link ) {
	return preg_replace( '|/category/|', '/', $link, 1 );
}
add_filter( 'category_link', 'lwai_remove_category_base' );

function lwai_register_category_rewrites() {
	$categories = get_categories( array( 'hide_empty' => false, 'number' => 200 ) );
	foreach ( $categories as $cat ) {
		$slug = preg_quote( $cat->slug, '/' );
		add_rewrite_rule( '^' . $slug . '/page/([0-9]+)/?$', 'index.php?category_name=' . $cat->slug . '&paged=$matches[1]', 'top' );
		add_rewrite_rule( '^' . $slug . '/?$',               'index.php?category_name=' . $cat->slug, 'top' );
	}
}
add_action( 'init', 'lwai_register_category_rewrites' );

function lwai_flush_category_rewrites() {
	lwai_register_category_rewrites();
	flush_rewrite_rules();
}
add_action( 'created_category', 'lwai_flush_category_rewrites' );
add_action( 'edited_category',  'lwai_flush_category_rewrites' );

/**
 * On theme activation: set permalink structure to /%category%/%postname%/
 * so article URLs become /ai-tech/article-slug/ instead of /article-slug/
 */
add_action( 'after_switch_theme', function () {
	update_option( 'permalink_structure', '/%category%/%postname%/' );
	lwai_register_category_rewrites();
	flush_rewrite_rules();
} );

/**
 * Set permalink structure to /%category%/%postname%/
 * so article URLs read as /ai-tech/article-slug/ instead of /article-slug/
 *
 * Uses a versioned option so this runs once automatically even on an
 * already-active theme — no need to deactivate/reactivate.
 */
function lwai_set_permalink_structure() {
	if ( get_option( 'permalink_structure' ) !== '/%category%/%postname%/' ) {
		update_option( 'permalink_structure', '/%category%/%postname%/' );
		flush_rewrite_rules();
	}
}

add_action( 'after_switch_theme', 'lwai_set_permalink_structure' );

// One-time trigger for themes that are already active
add_action( 'wp_loaded', function () {
	if ( ! get_option( 'lwai2_permalink_v1' ) ) {
		lwai_set_permalink_structure();
		lwai_register_category_rewrites();
		flush_rewrite_rules();
		update_option( 'lwai2_permalink_v1', '1' );
	}
} );

/**
 * M-4: Noindex search result pages — they are thin/duplicate content.
 */
add_action( 'wp_head', function () {
	if ( is_search() ) {
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
 * C-7: robots.txt additions — block search results and attachments.
 */
add_filter( 'robots_txt', function ( $output, $public ) {
	$output .= "\nDisallow: /?s=\nDisallow: /attachment/\n";
	return $output;
}, 10, 2 );

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
	}
}
add_action( 'wp_head', 'lwai_output_schema' );

/**
 * H-1: Breadcrumb navigation helper.
 * Outputs semantic <nav> with BreadcrumbList microdata (schema.org).
 * Call lwai_breadcrumb() at the top of single.php and category.php.
 */
function lwai_breadcrumb() {
	if ( is_front_page() ) {
		return;
	}

	$crumbs = array(
		array( 'url' => home_url( '/' ), 'name' => get_bloginfo( 'name' ) ),
	);

	if ( is_singular() ) {
		$cats = get_the_category();
		if ( $cats ) {
			$crumbs[] = array( 'url' => get_category_link( $cats[0]->term_id ), 'name' => $cats[0]->name );
		}
		$crumbs[] = array( 'url' => '', 'name' => get_the_title() );
	} elseif ( is_category() ) {
		$crumbs[] = array( 'url' => '', 'name' => single_cat_title( '', false ) );
	} elseif ( is_author() ) {
		$crumbs[] = array( 'url' => '', 'name' => get_the_author_meta( 'display_name', get_queried_object_id() ) );
	}

	echo '<nav class="lwai-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'lwai' ) . '">';
	echo '<ol itemscope itemtype="https://schema.org/BreadcrumbList">';
	$total = count( $crumbs );
	foreach ( $crumbs as $i => $crumb ) {
		$is_last = ( $i === $total - 1 );
		echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
		if ( ! $is_last && $crumb['url'] ) {
			echo '<a itemprop="item" href="' . esc_url( $crumb['url'] ) . '"><span itemprop="name">' . esc_html( $crumb['name'] ) . '</span></a>';
			echo '<span class="lwai-breadcrumb-sep" aria-hidden="true">&rsaquo;</span>';
		} else {
			echo '<span itemprop="name">' . esc_html( $crumb['name'] ) . '</span>';
		}
		echo '<meta itemprop="position" content="' . ( $i + 1 ) . '">';
		echo '</li>';
	}
	echo '</ol></nav>';
}

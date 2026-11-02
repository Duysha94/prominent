<?php
/**
 * Small helpers that write core block markup, so the launch copy stays
 * readable here and arrives in the editor as ordinary, editable blocks.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

function ak_b_p( string $html, string $class = '' ): string {
	$attrs = $class ? ' {"className":"' . $class . '"}' : '';
	$cls   = $class ? ' class="' . $class . '"' : '';
	return "<!-- wp:paragraph{$attrs} -->\n<p{$cls}>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
}

function ak_b_h( string $html, int $level = 2 ): string {
	$attrs = 2 === $level ? '' : ' {"level":' . $level . '}';
	return "<!-- wp:heading{$attrs} -->\n<h{$level} class=\"wp-block-heading\">{$html}</h{$level}>\n<!-- /wp:heading -->\n\n";
}

/**
 * @param string[] $items HTML of each item.
 */
function ak_b_list( array $items, bool $ordered = false, string $class = '' ): string {
	$json = array();
	if ( $ordered ) {
		$json[] = '"ordered":true';
	}
	if ( $class ) {
		$json[] = '"className":"' . $class . '"';
	}
	$attrs = $json ? ' {' . implode( ',', $json ) . '}' : '';
	$tag   = $ordered ? 'ol' : 'ul';
	$cls   = 'wp-block-list' . ( $class ? ' ' . $class : '' );
	$li    = '';
	foreach ( $items as $item ) {
		$li .= "<!-- wp:list-item -->\n<li>{$item}</li>\n<!-- /wp:list-item -->\n\n";
	}
	return "<!-- wp:list{$attrs} -->\n<{$tag} class=\"{$cls}\">{$li}</{$tag}>\n<!-- /wp:list -->\n\n";
}

function ak_b_quote( string $text, string $cite = '' ): string {
	$c = $cite ? "<cite>{$cite}</cite>" : '';
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>{$text}</p>\n<!-- /wp:paragraph -->{$c}</blockquote>\n<!-- /wp:quote -->\n\n";
}

function ak_b_details( string $summary, string $answer_html ): string {
	return "<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>{$summary}</summary><!-- wp:paragraph -->\n<p>{$answer_html}</p>\n<!-- /wp:paragraph --></details>\n<!-- /wp:details -->\n\n";
}

function ak_b_group( string $inner, string $class ): string {
	return "<!-- wp:group {\"className\":\"{$class}\",\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group {$class}\">{$inner}</div>\n<!-- /wp:group -->\n\n";
}

function ak_b_shortcode( string $code ): string {
	return "<!-- wp:shortcode -->\n{$code}\n<!-- /wp:shortcode -->\n\n";
}

/**
 * @param array<int,array{0:string,1:string,2?:string}> $buttons label, url, optional className.
 */
function ak_b_buttons( array $buttons ): string {
	$inner = '';
	foreach ( $buttons as $b ) {
		$cls    = isset( $b[2] ) && $b[2] ? $b[2] : '';
		$attrs  = $cls ? ' {"className":"' . $cls . '"}' : '';
		$wrap   = 'wp-block-button' . ( $cls ? ' ' . $cls : '' );
		$inner .= "<!-- wp:button{$attrs} -->\n<div class=\"{$wrap}\"><a class=\"wp-block-button__link wp-element-button\" href=\"{$b[1]}\">{$b[0]}</a></div>\n<!-- /wp:button -->\n\n";
	}
	return "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\">{$inner}</div>\n<!-- /wp:buttons -->\n\n";
}

/**
 * FAQ: a group of details blocks.
 *
 * @param array<int,array{0:string,1:string}> $pairs question, answer HTML.
 */
function ak_b_faq( array $pairs, string $heading = 'Questions' ): string {
	$inner = ak_b_h( $heading );
	foreach ( $pairs as $pair ) {
		$inner .= ak_b_details( $pair[0], $pair[1] );
	}
	return ak_b_group( $inner, 'ak-faq' );
}

/**
 * Numbered steps: an ordered list styled as steps; each item "<strong>Title</strong> text".
 *
 * @param array<int,array{0:string,1:string}> $steps title, text.
 */
function ak_b_steps( array $steps ): string {
	return ak_b_list( array_map( static fn( $s ) => '<strong>' . $s[0] . '</strong> ' . $s[1], $steps ), true, 'is-style-steps' );
}

/* ── Blocks for the launch content (v2: every page built from blocks) ── */

/**
 * A dynamic AK block: <!-- wp:ak/name {...} /-->.
 *
 * @param array<string,mixed> $attrs Attributes.
 */
function ak_b_ak( string $name, array $attrs = array() ): string {
	$json = $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ) : '';
	return "<!-- wp:ak/{$name}{$json} /-->\n\n";
}

/** A group with one of the theme's styles (section, statement, panel, cta, founder). */
function ak_b_styled_group( string $inner, string $style ): string {
	return ak_b_group( $inner, 'is-style-' . $style );
}

function ak_b_section( string $inner ): string {
	return ak_b_styled_group( $inner, 'section' );
}

function ak_b_eyebrow( string $html ): string {
	return ak_b_p( $html, 'is-style-eyebrow' );
}

function ak_b_lead( string $html ): string {
	return ak_b_p( $html, 'is-style-lead' );
}

function ak_b_display( string $html, int $level = 2, string $style = 'display' ): string {
	return "<!-- wp:heading {\"level\":{$level},\"className\":\"is-style-{$style}\"} -->\n<h{$level} class=\"wp-block-heading is-style-{$style}\">{$html}</h{$level}>\n<!-- /wp:heading -->\n\n";
}

/**
 * Columns.
 *
 * @param string[] $cols   Inner markup of each column.
 * @param string   $style  Optional block style name.
 */
function ak_b_columns( array $cols, string $style = '' ): string {
	$attrs = $style ? ' {"className":"is-style-' . $style . '"}' : '';
	$cls   = 'wp-block-columns' . ( $style ? ' is-style-' . $style : '' );
	$inner = '';
	foreach ( $cols as $col ) {
		$inner .= "<!-- wp:column -->\n<div class=\"wp-block-column\">{$col}</div>\n<!-- /wp:column -->\n\n";
	}
	return "<!-- wp:columns{$attrs} -->\n<div class=\"{$cls}\">{$inner}</div>\n<!-- /wp:columns -->\n\n";
}

/**
 * Seed media as block-ready data.
 *
 * @return array{id:int,url:string,full:string,alt:string,caption:string}
 */
function ak_b_media( string $file, string $size = 'large' ): array {
	$id = function_exists( 'ak_brand_seed_attachment' ) ? ak_brand_seed_attachment( $file ) : 0;
	if ( ! $id ) {
		return array( 'id' => 0, 'url' => '', 'full' => '', 'alt' => '', 'caption' => '' );
	}
	$url = wp_attachment_is_image( $id ) ? (string) wp_get_attachment_image_url( $id, $size ) : (string) wp_get_attachment_url( $id );
	return array(
		'id'      => $id,
		'url'     => $url,
		'full'    => (string) wp_get_attachment_url( $id ),
		'alt'     => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
		'caption' => (string) wp_get_attachment_caption( $id ),
	);
}

/**
 * Gallery of seed images, linking to the full size (lightbox).
 *
 * @param string[] $files Seed file names.
 */
function ak_b_gallery( array $files, bool $crop = false ): string {
	$images = '';
	foreach ( $files as $file ) {
		$m = ak_b_media( $file );
		if ( ! $m['id'] ) {
			continue;
		}
		$alt      = esc_attr( $m['alt'] );
		$caption  = $m['caption'] ? '<figcaption class="wp-element-caption">' . esc_html( $m['caption'] ) . '</figcaption>' : '';
		$images  .= "<!-- wp:image {\"id\":{$m['id']},\"sizeSlug\":\"large\",\"linkDestination\":\"media\"} -->\n<figure class=\"wp-block-image size-large\"><a href=\"" . esc_url( $m['full'] ) . "\"><img src=\"" . esc_url( $m['url'] ) . "\" alt=\"{$alt}\" class=\"wp-image-{$m['id']}\"/></a>{$caption}</figure>\n<!-- /wp:image -->\n\n";
	}
	if ( '' === $images ) {
		return '';
	}
	$crop_attr = $crop ? '' : ',"imageCrop":false';
	$crop_cls  = $crop ? ' is-cropped' : '';
	return "<!-- wp:gallery {\"linkTo\":\"media\"{$crop_attr},\"className\":\"is-style-masonry\"} -->\n<figure class=\"wp-block-gallery has-nested-images columns-default{$crop_cls} is-style-masonry\">{$images}</figure>\n<!-- /wp:gallery -->\n\n";
}

/**
 * One seed image as an image block (wide by default).
 */
function ak_b_image( string $file, string $align = 'wide' ): string {
	$m = ak_b_media( $file, 'full' );
	if ( ! $m['id'] ) {
		return '';
	}
	$alt   = esc_attr( $m['alt'] );
	$attrs = '{"id":' . $m['id'] . ',"sizeSlug":"full","linkDestination":"none"' . ( $align ? ',"align":"' . $align . '"' : '' ) . '}';
	$cls   = 'wp-block-image' . ( $align ? ' align' . $align : '' ) . ' size-full';
	return "<!-- wp:image {$attrs} -->\n<figure class=\"{$cls}\"><img src=\"" . esc_url( $m['url'] ) . "\" alt=\"{$alt}\" class=\"wp-image-{$m['id']}\"/></figure>\n<!-- /wp:image -->\n\n";
}

/**
 * A film with sound, played from a poster.
 */
function ak_b_video( string $file, string $poster_file, string $caption = '' ): string {
	$v = ak_b_media( $file );
	if ( ! $v['id'] ) {
		return '';
	}
	$p   = $poster_file ? ak_b_media( $poster_file, 'full' ) : array( 'url' => '' );
	$pos = $p['url'] ? ' poster="' . esc_url( $p['url'] ) . '"' : '';
	$cap = $caption ? '<figcaption class="wp-element-caption">' . esc_html( $caption ) . '</figcaption>' : '';
	return "<!-- wp:video {\"id\":{$v['id']},\"className\":\"is-style-film\"} -->\n<figure class=\"wp-block-video is-style-film\"><video controls{$pos} preload=\"none\" src=\"" . esc_url( $v['url'] ) . "\"></video>{$cap}</figure>\n<!-- /wp:video -->\n\n";
}

/**
 * A synced pattern by its key ("cta", "footer"), once the installer has made it.
 */
function ak_b_pattern( string $key ): string {
	$ids = get_option( 'akbrand_patterns', array() );
	$id  = is_array( $ids ) ? (int) ( $ids[ $key ] ?? 0 ) : 0;
	return $id ? "<!-- wp:block {\"ref\":{$id}} /-->\n\n" : '';
}

/**
 * A theme asset URL that stays valid whichever theme is active during install.
 */
function ak_b_theme_asset( string $path ): string {
	return content_url( 'themes/ak-studio/assets/' . ltrim( $path, '/' ) );
}

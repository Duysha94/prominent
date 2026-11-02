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

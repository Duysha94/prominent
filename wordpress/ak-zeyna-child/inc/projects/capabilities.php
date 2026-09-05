<?php
/**
 * The factual layer: every service the studio actually offers.
 *
 * Two layers, deliberately separate:
 *
 *   FACTUAL      the practice areas and services below. This is the record of
 *                what AK does. Nothing may be dropped from it.
 *   PRESENTATION the six movements. An editorial grouping over the factual
 *                layer, never a replacement for it.
 *
 * The rule that governs both: creative naming must never hide what the agency
 * actually does. Every service below stays discoverable beneath the movement
 * that carries it — named in full on Services, filterable in Work, and
 * addressable by its own anchor. A movement is a heading, not a summary that
 * absorbs its contents.
 *
 * Structure maps 1:1 onto the `ak_capability` taxonomy: the six movements are
 * the parent terms, the services are their children. That is what makes the
 * editorial layer navigable rather than decorative — a visitor who arrives
 * looking for "fashion show production" finds that exact phrase, under
 * EXPERIENCE, and can filter Work by it.
 *
 * SOURCE OF TRUTH: the practice areas and services supplied by the owner in
 * the AK Brand Development Studio source document. Every entry below appears
 * there. Three plausible-sounding services that did NOT — "editorial
 * photography", "fashion film" and "event curation" — were removed once the
 * document arrived: a service list is a claim about what the studio sells, and
 * inventing a line item is inventing a claim, however reasonable it sounds.
 *
 * Adding a service here makes it a taxonomy term, a Services entry and a
 * filterable capability. Only add one the owner has confirmed.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The six movements, each carrying its confirmed practice areas and services.
 *
 * `areas` are the confirmed practice areas supplied by the owner. `services`
 * are the individual capabilities inside them, and each becomes a term.
 *
 * @return array[] Keyed by movement slug.
 */
function ak_movements() {
	$movements = array(
		'strategy'   => array(
			'number'  => '01',
			'name'    => __( 'Strategy', 'ak-zeyna-child' ),
			'summary' => __( 'What the brand is, and who it is for.', 'ak-zeyna-child' ),
			'areas'   => array(
				__( 'Brand Strategy & Development', 'ak-zeyna-child' ),
				__( 'Personal Brand Development', 'ak-zeyna-child' ),
			),
			'services' => array(
				'brand-concept-development'   => __( 'Brand concept development', 'ak-zeyna-child' ),
				'brand-positioning'           => __( 'Brand positioning', 'ak-zeyna-child' ),
				'brand-strategy'              => __( 'Brand strategy', 'ak-zeyna-child' ),
				'brand-relaunch'              => __( 'Brand relaunch', 'ak-zeyna-child' ),
				'repositioning'               => __( 'Repositioning', 'ak-zeyna-child' ),
				'brand-philosophy'            => __( 'Brand philosophy', 'ak-zeyna-child' ),
				'identity-foundations'        => __( 'Identity foundations', 'ak-zeyna-child' ),
				'strategic-guidance'          => __( 'Strategic guidance for business growth', 'ak-zeyna-child' ),
				'personal-brand-strategy'     => __( 'Personal brand strategy', 'ak-zeyna-child' ),
				'personal-positioning'        => __( 'Personal positioning', 'ak-zeyna-child' ),
				'personal-identity'           => __( 'Personal identity', 'ak-zeyna-child' ),
				'personal-communication'      => __( 'Personal communication strategy', 'ak-zeyna-child' ),
				'personal-visual-direction'   => __( 'Personal visual direction', 'ak-zeyna-child' ),
				'personal-content-direction'  => __( 'Personal content direction', 'ak-zeyna-child' ),
			),
		),
		'identity'   => array(
			'number'  => '02',
			'name'    => __( 'Identity', 'ak-zeyna-child' ),
			'summary' => __( 'What it looks and sounds like.', 'ak-zeyna-child' ),
			'areas'   => array( __( 'Brand Identity & Creative Direction', 'ak-zeyna-child' ) ),
			'services' => array(
				'brand-identity-development' => __( 'Brand identity development', 'ak-zeyna-child' ),
				'visual-direction'           => __( 'Visual direction', 'ak-zeyna-child' ),
				'logo-and-identity-design'   => __( 'Logo and identity design', 'ak-zeyna-child' ),
				'brand-guidelines'           => __( 'Brand guidelines', 'ak-zeyna-child' ),
				'visual-storytelling'        => __( 'Visual storytelling', 'ak-zeyna-child' ),
				'creative-direction'         => __( 'Creative direction', 'ak-zeyna-child' ),
			),
		),
		'image'      => array(
			'number'  => '03',
			'name'    => __( 'Image', 'ak-zeyna-child' ),
			'summary' => __( 'The pictures and the films.', 'ak-zeyna-child' ),
			'areas'   => array( __( 'Photo & Video Production', 'ak-zeyna-child' ) ),
			'services' => array(
				'photo-campaigns'             => __( 'Photo campaigns', 'ak-zeyna-child' ),
				'promotional-video'           => __( 'Promotional video production', 'ak-zeyna-child' ),
				'campaign-production'         => __( 'Campaign production', 'ak-zeyna-child' ),
				'image-visual-storytelling'   => __( 'Visual storytelling', 'ak-zeyna-child' ),
			),
		),
		'experience' => array(
			'number'  => '04',
			'name'    => __( 'Experience', 'ak-zeyna-child' ),
			'summary' => __( 'What happens in a room.', 'ak-zeyna-child' ),
			'areas'   => array( __( 'Events & Fashion Production', 'ak-zeyna-child' ) ),
			'services' => array(
				'brand-presentations'      => __( 'Brand presentations', 'ak-zeyna-child' ),
				'product-launches'         => __( 'Product launches', 'ak-zeyna-child' ),
				'creative-events'          => __( 'Creative events', 'ak-zeyna-child' ),
				'independent-fashion-shows' => __( 'Independent fashion shows', 'ak-zeyna-child' ),
				'fashion-show-production'  => __( 'Fashion show production', 'ak-zeyna-child' ),
				'fashion-week-production'  => __( 'Fashion-week-related production', 'ak-zeyna-child' ),
			),
		),
		'digital'    => array(
			'number'  => '05',
			'name'    => __( 'Digital', 'ak-zeyna-child' ),
			'summary' => __( 'Where it lives online.', 'ak-zeyna-child' ),
			'areas'   => array( __( 'Digital Presence', 'ak-zeyna-child' ) ),
			'services' => array(
				'website-creation'          => __( 'Website creation', 'ak-zeyna-child' ),
				'website-development'       => __( 'Website development', 'ak-zeyna-child' ),
				'social-media-presence'     => __( 'Social media presence', 'ak-zeyna-child' ),
				'digital-content-production' => __( 'Digital content production', 'ak-zeyna-child' ),
				'online-brand-positioning'  => __( 'Online brand positioning', 'ak-zeyna-child' ),
			),
		),
		'visibility' => array(
			'number'  => '06',
			'name'    => __( 'Visibility', 'ak-zeyna-child' ),
			'summary' => __( 'How it is seen and heard.', 'ak-zeyna-child' ),
			'areas'   => array(
				__( 'Marketing & Communication', 'ak-zeyna-child' ),
				__( 'Digital Promotion', 'ak-zeyna-child' ),
			),
			/*
			 * Two clusters, presented with equal weight and never merged into
			 * one list: PR must not be swallowed by advertising, and
			 * advertising must not be hidden. `cluster` keeps them apart on
			 * the front end without splitting the movement in two.
			 */
			'clusters' => array(
				'communication' => __( 'Communication & PR', 'ak-zeyna-child' ),
				'promotion'     => __( 'Promotion & Paid Media', 'ak-zeyna-child' ),
			),
			'services' => array(
				'marketing-strategy'     => array( __( 'Marketing strategy', 'ak-zeyna-child' ), 'communication' ),
				'brand-communication'    => array( __( 'Brand communication', 'ak-zeyna-child' ), 'communication' ),
				'pr-support'             => array( __( 'PR support', 'ak-zeyna-child' ), 'communication' ),
				'campaign-development'   => array( __( 'Campaign development', 'ak-zeyna-child' ), 'communication' ),
				'digital-advertising'    => array( __( 'Digital advertising campaigns', 'ak-zeyna-child' ), 'promotion' ),
				'google-promotion'       => array( __( 'Google promotion', 'ak-zeyna-child' ), 'promotion' ),
				'youtube-promotion'      => array( __( 'YouTube promotion', 'ak-zeyna-child' ), 'promotion' ),
				'meta-advertising'       => array( __( 'Meta / Facebook / Instagram advertising', 'ak-zeyna-child' ), 'promotion' ),
				'audience-growth'        => array( __( 'Audience growth strategies', 'ak-zeyna-child' ), 'promotion' ),
				'digital-visibility'     => array( __( 'Digital visibility', 'ak-zeyna-child' ), 'promotion' ),
				'engagement'             => array( __( 'Engagement', 'ak-zeyna-child' ), 'promotion' ),
			),
		),
	);

	/**
	 * Filter the movement / service model.
	 *
	 * The factual layer. Adding a service here makes it a taxonomy term, a
	 * Services entry and a Work filter without further code.
	 *
	 * @param array[] $movements Movements keyed by slug.
	 */
	return apply_filters( 'ak_movements', $movements );
}

/**
 * One movement's services as slug => label, discarding cluster assignment.
 *
 * @param array $movement A movement from ak_movements().
 * @return string[]
 */
function ak_movement_services( $movement ) {
	$out = array();
	foreach ( $movement['services'] as $slug => $service ) {
		$out[ $slug ] = is_array( $service ) ? $service[0] : $service;
	}
	return $out;
}

/**
 * One movement's services grouped by cluster, for movements that have them.
 *
 * @param array $movement A movement from ak_movements().
 * @return array[] cluster slug => array( label, services )
 */
function ak_movement_clusters( $movement ) {
	if ( empty( $movement['clusters'] ) ) {
		return array();
	}
	$out = array();
	foreach ( $movement['clusters'] as $slug => $label ) {
		$out[ $slug ] = array( 'label' => $label, 'services' => array() );
	}
	foreach ( $movement['services'] as $slug => $service ) {
		if ( is_array( $service ) && isset( $out[ $service[1] ] ) ) {
			$out[ $service[1] ]['services'][ $slug ] = $service[0];
		}
	}
	return $out;
}

/**
 * Total number of distinct services in the factual layer.
 *
 * Used on Services to state the scope as a fact rather than an adjective.
 *
 * @return int
 */
function ak_service_count() {
	$n = 0;
	foreach ( ak_movements() as $movement ) {
		$n += count( $movement['services'] );
	}
	return $n;
}

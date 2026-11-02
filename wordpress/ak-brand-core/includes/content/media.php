<?php
/**
 * Launch media: photographs and films from the studio's own platforms,
 * London Fashion Day and Odessa Fashion Day. Alt text describes the image;
 * the caption credits the season and, where published, the people.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string,array{alt:string,caption:string}>
 */
function ak_brand_seed_media(): array {
	$s8  = 'London Fashion Day Season 8 campaign. Look by Gabrielle Nitu (LCCA). Photo: Mariana Zabramna.';
	$s7c = 'London Fashion Day Season 7 campaign shoot, Regent Street, London. Photo: Natalia Wise.';
	$s7  = static fn( $who ) => $who . ' at London Fashion Day Season 7, St John’s Church, Hyde Park.';
	$s6  = static fn( $who ) => $who . ' at London Fashion Day Season 6, “The Art of Being”, St John’s Church, Hyde Park.';
	$s5  = static fn( $who ) => $who . ' at London Fashion Day Season 5, “HUMAN”, St John’s Church, Hyde Park.';
	$o32 = 'Odessa Fashion Day Season 32, “ONE OF A KIND”, Luxury Gallery, Odesa.';
	$o31 = static fn( $where ) => 'Odessa Fashion Day Season 31, “UNYIELDING”, ' . $where . ', Odesa.';
	$sw  = 'SUMMER WEEKEND by OFD 2026, Botanical Garden of Odesa I. I. Mechnikov National University.';

	return array(
		// London Fashion Day — Season 8 campaign.
		'lfd-s8-campaign-portrait.jpg'   => array( 'alt' => 'A model in a white sculptural look with fringed sleeves, lit in deep blue, face painted with white lines.', 'caption' => $s8 ),
		'lfd-s8-campaign-profile.jpg'    => array( 'alt' => 'Model in profile in a white satin halter dress with a large curved collar, against a blue backdrop.', 'caption' => $s8 ),
		'lfd-s8-campaign-close-up.jpg'   => array( 'alt' => 'Close-up of a model’s face with white graphic make-up, hands raised in fringed sleeves, in blue light.', 'caption' => $s8 ),
		'lfd-s8-campaign-seated.jpg'     => array( 'alt' => 'Model seated on the floor in a white gown, head tilted back, in blue studio light.', 'caption' => $s8 ),
		'lfd-s8-campaign-fringe.jpg'     => array( 'alt' => 'Model with arms spread wide in fringed white sleeves, blue background.', 'caption' => $s8 ),
		'lfd-s8-campaign-detail.jpg'     => array( 'alt' => 'Model framed by a white sculptural collar and fringed sleeves, blue light.', 'caption' => $s8 ),
		'lfd-s8-campaign-movement.jpg'   => array( 'alt' => 'Model in a long white dress mid-movement, one arm raised, blue backdrop.', 'caption' => $s8 ),
		'lfd-s8-campaign-standing.jpg'   => array( 'alt' => 'Full-length shot of a model in a white satin dress with a halo-like collar, blue studio.', 'caption' => $s8 ),
		// Season 7 — campaign and runway.
		'lfd-s7-campaign-regent-street.jpg' => array( 'alt' => 'Model in a sheer white shirt with lilac organza flowers on Regent Street at sunrise.', 'caption' => $s7c . ' Look by YADVIGA NETYKSHA.' ),
		'lfd-s7-campaign-sunrise.jpg'    => array( 'alt' => 'Model with eyes closed on a quiet Regent Street at dawn, holding lilac organza.', 'caption' => $s7c ),
		'lfd-s7-campaign-on-set.jpg'     => array( 'alt' => 'A camera monitor showing the model during the Season 7 campaign shoot, Regent Street in the background.', 'caption' => $s7c ),
		'lfd-s7-campaign-crossing.jpg'   => array( 'alt' => 'Model standing by traffic lights on Regent Street in a sheer white look.', 'caption' => $s7c ),
		'lfd-s7-gorsset.jpg'             => array( 'alt' => 'A white bridal gown with a sheer cape on the runway in front of a carved wooden screen.', 'caption' => $s7( 'Gorsset' ) ),
		'lfd-s7-riri-couture.jpg'        => array( 'alt' => 'A floor-length dress in yellow and black print with a black sash on the runway.', 'caption' => $s7( 'Riri Couture' ) ),
		'lfd-s7-keka.jpg'                => array( 'alt' => 'A white double-breasted coat dress on the runway, guests photographing from the front row.', 'caption' => $s7( 'KEKA' ) ),
		'lfd-s7-mur-mur.jpg'             => array( 'alt' => 'A sequinned bronze dress with a long open coat on the runway.', 'caption' => $s7( 'Mur Mur' ) ),
		'lfd-s7-anabelle-clothing.jpg'   => array( 'alt' => 'A red draped skirt with a gold embellished top and a floral headpiece on the runway.', 'caption' => $s7( 'Anabelle Clothing' ) ),
		'lfd-s7-backstage-lacing.jpg'    => array( 'alt' => 'Backstage: hands lacing a beaded corset before the show.', 'caption' => 'Backstage at London Fashion Day Season 7.' ),
		'lfd-s7-kostiantyn-lieontiev-runway.jpg' => array( 'alt' => 'Kostiantyn Lieontiev addressing guests on the runway at St John’s Church, Hyde Park.', 'caption' => 'Kostiantyn Lieontiev, founder of London Fashion Day, opening Season 7.' ),
		// Season 6.
		'lfd-s6-lenie-boya-malusha.jpg'  => array( 'alt' => 'A white sculptural collar over black tailoring on the runway.', 'caption' => $s6( 'LENIE BOYA × MALUSHA' ) ),
		'lfd-s6-lcca-showcase.jpg'       => array( 'alt' => 'A red lace two-piece on the runway under stained glass.', 'caption' => $s6( 'London College of Contemporary Arts showcase' ) ),
		'lfd-s6-sara-k-couture.jpg'      => array( 'alt' => 'A flowing turquoise and blue printed kaftan on the runway.', 'caption' => $s6( 'Sara K Couture' ) ),
		'lfd-s6-mogardi.jpg'             => array( 'alt' => 'A long red coat over a printed slip on the runway.', 'caption' => $s6( 'Mogardi' ) ),
		'lfd-s6-relique-london.jpg'      => array( 'alt' => 'A denim corset top and painted denim trousers on the runway.', 'caption' => $s6( 'Relique London' ) ),
		'lfd-s6-maison-sophy-g.jpg'      => array( 'alt' => 'A tailored suit in a golden check on the runway.', 'caption' => $s6( 'Maison Sophy G' ) ),
		'lfd-s6-kochhouse.jpg'           => array( 'alt' => 'A couple walking the runway: a black tuxedo and a magenta draped mini dress.', 'caption' => $s6( 'KochHouse' ) ),
		'lfd-s6-kostiantyn-lieontiev-backstage.jpg' => array( 'alt' => 'Kostiantyn Lieontiev with a headset and a cup, producing the show.', 'caption' => 'Kostiantyn Lieontiev producing London Fashion Day Season 6.' ),
		'lfd-s6-runway-loop.jpg'         => array( 'alt' => 'Runway at London Fashion Day Season 6 in red and blue light.', 'caption' => 'London Fashion Day Season 6 teaser.' ),
		// Season 5.
		'lfd-s5-elen-ost.jpg'            => array( 'alt' => 'A cobalt satin gown with a feather hem on the runway.', 'caption' => $s5( 'ELEN OST' ) ),
		'lfd-s5-elen-ost-2.jpg'          => array( 'alt' => 'A sequinned royal-blue gown with a high slit on the runway.', 'caption' => $s5( 'ELEN OST' ) ),
		'lfd-s5-keka-by-novikov.jpg'     => array( 'alt' => 'A black pinafore dress over a white blouse with puffed sleeves on the runway.', 'caption' => $s5( 'KEKA by Novikov' ) ),
		'lfd-s5-keka-by-novikov-2.jpg'   => array( 'alt' => 'A corset and wide trousers with a ruffled collar on the runway.', 'caption' => $s5( 'KEKA by Novikov' ) ),
		'lfd-s5-gorsset.jpg'             => array( 'alt' => 'An orange brocade waistcoat over a black gown on the runway.', 'caption' => $s5( 'Gorsset' ) ),
		'lfd-s5-panache-by-elvira.jpg'   => array( 'alt' => 'A green brocade robe over a matching dress on the runway.', 'caption' => $s5( 'Panache by Elvira' ) ),
		// Odessa Fashion Day.
		'ofd-s32-runway-poppies.jpg'     => array( 'alt' => 'A white dress embroidered with large red poppies on the runway.', 'caption' => $o32 ),
		'ofd-s32-runway-blue.jpg'        => array( 'alt' => 'A short ruffled dress in deep blue with a lace mask on the runway.', 'caption' => $o32 ),
		'ofd-s32-runway-embroidery.jpg'  => array( 'alt' => 'A man in an embroidered white shirt and black trousers on the runway.', 'caption' => $o32 ),
		'ofd-s32-runway-summer.jpg'      => array( 'alt' => 'A wide straw hat and a pale blue draped top on the runway.', 'caption' => $o32 ),
		'ofd-s32-runway-silk.jpg'        => array( 'alt' => 'A pale silk slip dress on the runway.', 'caption' => $o32 ),
		'ofd-s32-runway-draped.jpg'      => array( 'alt' => 'A white shirt with layered khaki drapes on the runway.', 'caption' => $o32 ),
		'ofd-s32-runway-white.jpg'       => array( 'alt' => 'A sheer white dress with floral appliqué and a long veil on the runway.', 'caption' => $o32 ),
		'ofd-s32-fashion-talk.jpg'       => array( 'alt' => 'Designers and guests talking at a terrace table during a Fashion Talk session.', 'caption' => 'Odessa Fashion Day Fashion Talk, Odesa.' ),
		'ofd-s31-londonska-hotel.jpg'    => array( 'alt' => 'A long ivory skirt and draped blouse on a runway under chandeliers.', 'caption' => $o31( 'Londonska Hotel' ) ),
		'ofd-s31-luxury-gallery.jpg'     => array( 'alt' => 'A black look with a vivid blue ruffled sleeve on the runway.', 'caption' => $o31( 'Luxury Gallery' ) ),
		'ofd-s31-londonska-red.jpg'      => array( 'alt' => 'A red satin slip dress with a high slit under chandeliers.', 'caption' => $o31( 'Londonska Hotel' ) ),
		'ofd-s31-luxury-gallery-2.jpg'   => array( 'alt' => 'A white blouse with ruffled cuffs, a black skirt and a wide-brimmed hat on the runway.', 'caption' => $o31( 'Luxury Gallery' ) ),
		'ofd-s31-londonska-embroidery.jpg' => array( 'alt' => 'A black jacket with a gold embroidered horse, seen from behind on the runway.', 'caption' => $o31( 'Londonska Hotel' ) ),
		'ofd-s31-londonska-patchwork.jpg' => array( 'alt' => 'A patchwork robe in blue, rust and print on the runway.', 'caption' => $o31( 'Londonska Hotel' ) ),
		'ofd-s31-luxury-gallery-3.jpg'   => array( 'alt' => 'A green satin dress with a high collar on the runway.', 'caption' => $o31( 'Luxury Gallery' ) ),
		'ofd-s31-londonska-lace.jpg'     => array( 'alt' => 'A sheer lace gown with feather cuffs on the runway.', 'caption' => $o31( 'Londonska Hotel' ) ),
		'sw26-botanical-garden.jpg'      => array( 'alt' => 'A crochet fringed dress on an outdoor runway lined with trees and bunting.', 'caption' => $sw ),
		'sw26-denim.jpg'                 => array( 'alt' => 'A layered denim look on the garden runway.', 'caption' => $sw ),
		'sw26-linen.jpg'                 => array( 'alt' => 'A white linen trouser suit on the garden runway.', 'caption' => $sw ),
		'sw26-outerwear.jpg'             => array( 'alt' => 'A check coat with a faux-fur trim on the garden runway.', 'caption' => $sw ),
		'sw26-cruise.jpg'                => array( 'alt' => 'A white cruise set with embroidered trim on the garden runway.', 'caption' => $sw ),
		'sw26-black.jpg'                 => array( 'alt' => 'A black swing dress and fan on the garden runway.', 'caption' => $sw ),
		'sw26-tailoring.jpg'             => array( 'alt' => 'A pale yellow waistcoat and sheer skirt on the garden runway.', 'caption' => $sw ),
		'sw26-maramax-academy.jpg'       => array( 'alt' => 'A DJ in headphones at the decks during the opening night.', 'caption' => 'SUMMER WEEKEND by OFD 2026 opening at MarAmax Academy, Odesa.' ),
		// Stills from the campaign films.
		'still-lfd-s7-campaign-21.jpg'   => array( 'alt' => 'Model with arms wide open in a sheer white shirt on the curve of Regent Street.', 'caption' => 'London Fashion Day Season 7 campaign film, Regent Street.' ),
		'still-lfd-westminster-9.jpg'    => array( 'alt' => 'Model in a flowing red tulle dress on Westminster Bridge at sunrise.', 'caption' => 'London Fashion Day campaign film, Westminster Bridge.' ),
		'still-lfd-westminster-17.jpg'   => array( 'alt' => 'Model in a red draped dress in front of the Houses of Parliament.', 'caption' => 'London Fashion Day campaign film, Westminster.' ),
		'still-ofd-s31-campaign-13.jpg'  => array( 'alt' => 'Model in a sculptural navy satin gown against a coral backdrop.', 'caption' => 'Odessa Fashion Day Season 31 campaign film.' ),
		'still-ofd-s31-campaign-21.jpg'  => array( 'alt' => 'Full-length shot of a model in a navy satin gown with a pointed shoulder, coral light.', 'caption' => 'Odessa Fashion Day Season 31 campaign film.' ),
		// Films.
		'lfd-s7-campaign-film.mp4'       => array( 'alt' => '', 'caption' => 'London Fashion Day Season 7 campaign film, shot at sunrise on Regent Street.' ),
		'lfd-s7-campaign-film.jpg'       => array( 'alt' => 'Model on Regent Street at sunrise.', 'caption' => 'London Fashion Day Season 7 campaign film.' ),
		'ofd-s31-campaign-film.mp4'      => array( 'alt' => '', 'caption' => 'Odessa Fashion Day Season 31 campaign film.' ),
		'ofd-s31-campaign-film.jpg'      => array( 'alt' => 'Model in a navy satin gown against a coral backdrop.', 'caption' => 'Odessa Fashion Day Season 31 campaign film.' ),
		'lfd-s6-runway-loop.mp4'         => array( 'alt' => '', 'caption' => 'London Fashion Day Season 6 teaser.' ),
		'ofd-s31-campaign-loop.mp4'      => array( 'alt' => '', 'caption' => 'Odessa Fashion Day Season 31 campaign.' ),
		'ofd-s31-campaign-loop.jpg'      => array( 'alt' => 'Model in a sculptural navy satin gown against a coral backdrop.', 'caption' => 'Odessa Fashion Day Season 31 campaign.' ),
		'lfd-westminster-film.mp4'       => array( 'alt' => '', 'caption' => 'London Fashion Day campaign film, Westminster Bridge, April 2024.' ),
		'lfd-westminster-film.jpg'       => array( 'alt' => 'Model in a flowing red tulle dress on Westminster Bridge.', 'caption' => 'London Fashion Day campaign film, Westminster Bridge.' ),
		'still-lfd-westminster-15.jpg'   => array( 'alt' => 'Pink tulle lifted by the wind with Big Ben behind.', 'caption' => 'London Fashion Day campaign film, Westminster.' ),
		'ak-site-icon.png'               => array( 'alt' => 'AK', 'caption' => '' ),
		'ak-share.jpg'                   => array( 'alt' => 'AK Brand Development Studio', 'caption' => '' ),
	) + ak_brand_seed_media_websites();
}

/**
 * Portfolio images for the websites the studio designed and built.
 *
 * @return array<string,array{alt:string,caption:string}>
 */
function ak_brand_seed_media_websites(): array {
	$sites = array(
		'smyn-salon' => array( 'Show Me Your Nails', 'showmeyournails.co.uk' ),
		'lenieboya'  => array( 'Lenie Boya', 'lenieboya.com' ),
		'wolax'      => array( 'Wolax Interiors', 'wolax.co.uk' ),
		'keka'       => array( 'KEKA', 'keka.design' ),
		'utrend'     => array( 'Ultimate Trend Store', 'utrendstore.co.uk' ),
		'prominent'  => array( 'PROMINENT Magazine', 'prominentmagazine.co.uk' ),
		'frontier'   => array( 'Fashion Frontier', 'fashionfrontier.uk' ),
		'coolbaba'   => array( 'COOL’BABA', 'coolbaba.in.ua' ),
		'lfd'        => array( 'London Fashion Day', 'londonfashionday.co.uk' ),
		'ofd'        => array( 'Odessa Fashion Day', 'ofd.org.ua' ),
	);
	$out = array();
	foreach ( $sites as $key => $site ) {
		list( $name, $domain ) = $site;
		$out[ "web-{$key}-cover.jpg" ]  = array( 'alt' => "The {$name} website on a laptop and a phone.", 'caption' => "{$domain}" );
		$out[ "web-{$key}-hero.jpg" ]   = array( 'alt' => "The {$name} website, desktop and mobile.", 'caption' => "{$domain} — home page" );
		$out[ "web-{$key}-page-1.jpg" ] = array( 'alt' => "A page of the {$name} website.", 'caption' => "{$domain}" );
		$out[ "web-{$key}-page-2.jpg" ] = array( 'alt' => "Another page of the {$name} website.", 'caption' => "{$domain}" );
		$out[ "web-{$key}-mobile.jpg" ] = array( 'alt' => "The {$name} website on three phone screens.", 'caption' => "{$domain} on a phone" );
	}
	$out['web-wall.jpg'] = array( 'alt' => 'Pages of websites designed and built by AK Brand Development Studio.', 'caption' => '' );
	// Only files that exist (short sites have no page sections).
	return array_filter( $out, static fn( $v, $file ) => is_readable( AK_BRAND_CORE_DIR . 'seed/media/' . $file ), ARRAY_FILTER_USE_BOTH );
}

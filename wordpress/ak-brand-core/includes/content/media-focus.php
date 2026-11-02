<?php
/**
 * Where to keep the frame when a photograph or film is cropped: the centre of
 * the main face (found by face detection and checked by eye), as CSS
 * object-position. Editable afterwards per image under Media → Focus point.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string,string> file => "x% y%"
 */
function ak_brand_seed_media_focus(): array {
	return array(
		'lfd-s5-elen-ost-2.jpg' => '52% 19%',
		'lfd-s5-elen-ost.jpg' => '48% 26%',
		'lfd-s5-gorsset.jpg' => '50% 14%',
		'lfd-s5-keka-by-novikov-2.jpg' => '50% 14%',
		'lfd-s5-keka-by-novikov.jpg' => '49% 21%',
		'lfd-s5-panache-by-elvira.jpg' => '50% 21%',
		'lfd-s6-kochhouse.jpg' => '34% 21%',
		'lfd-s6-kostiantyn-lieontiev-backstage.jpg' => '48% 15%',
		'lfd-s6-lcca-showcase.jpg' => '51% 29%',
		'lfd-s6-lenie-boya-malusha.jpg' => '45% 21%',
		'lfd-s6-maison-sophy-g.jpg' => '50% 27%',
		'lfd-s6-mogardi.jpg' => '53% 26%',
		'lfd-s6-relique-london.jpg' => '43% 27%',
		'lfd-s6-runway-loop.jpg' => '47% 35%',
		'lfd-s6-runway-loop.mp4' => '47% 35%',
		'lfd-s6-sara-k-couture.jpg' => '47% 20%',
		'lfd-s7-anabelle-clothing.jpg' => '50% 27%',
		'lfd-s7-backstage-lacing.jpg' => '45% 40%',
		'lfd-s7-campaign-crossing.jpg' => '48% 52%',
		'lfd-s7-campaign-film.jpg' => '60% 40%',
		'lfd-s7-campaign-on-set.jpg' => '74% 53%',
		'lfd-s7-campaign-regent-street.jpg' => '38% 30%',
		'lfd-s7-campaign-sunrise.jpg' => '32% 41%',
		'lfd-s7-gorsset.jpg' => '49% 22%',
		'lfd-s7-keka.jpg' => '49% 23%',
		'lfd-s7-kostiantyn-lieontiev-runway.jpg' => '45% 25%',
		'lfd-s7-mur-mur.jpg' => '52% 21%',
		'lfd-s7-riri-couture.jpg' => '46% 22%',
		'lfd-s8-campaign-close-up.jpg' => '42% 25%',
		'lfd-s8-campaign-detail.jpg' => '43% 35%',
		'lfd-s8-campaign-fringe.jpg' => '42% 33%',
		'lfd-s8-campaign-movement.jpg' => '46% 25%',
		'lfd-s8-campaign-portrait.jpg' => '55% 38%',
		'lfd-s8-campaign-profile.jpg' => '40% 20%',
		'lfd-s8-campaign-seated.jpg' => '48% 33%',
		'lfd-s8-campaign-standing.jpg' => '42% 22%',
		'lfd-westminster-film.jpg' => '62% 40%',
		'ofd-s31-campaign-film.jpg' => '30% 30%',
		'ofd-s31-campaign-loop.jpg' => '30% 30%',
		'ofd-s31-campaign-loop.mp4' => '30% 30%',
		'ofd-s31-londonska-embroidery.jpg' => '50% 18%',
		'ofd-s31-londonska-hotel.jpg' => '47% 30%',
		'ofd-s31-londonska-lace.jpg' => '51% 22%',
		'ofd-s31-londonska-patchwork.jpg' => '53% 18%',
		'ofd-s31-londonska-red.jpg' => '53% 19%',
		'ofd-s31-luxury-gallery-2.jpg' => '48% 18%',
		'ofd-s31-luxury-gallery-3.jpg' => '49% 15%',
		'ofd-s31-luxury-gallery.jpg' => '50% 18%',
		'ofd-s32-fashion-talk.jpg' => '23% 46%',
		'ofd-s32-runway-blue.jpg' => '49% 18%',
		'ofd-s32-runway-draped.jpg' => '48% 17%',
		'ofd-s32-runway-embroidery.jpg' => '48% 15%',
		'ofd-s32-runway-poppies.jpg' => '49% 15%',
		'ofd-s32-runway-silk.jpg' => '47% 18%',
		'ofd-s32-runway-summer.jpg' => '47% 17%',
		'ofd-s32-runway-white.jpg' => '49% 19%',
		'still-lfd-s7-campaign-21.jpg' => '50% 35%',
		'still-lfd-westminster-15.jpg' => '55% 40%',
		'still-lfd-westminster-17.jpg' => '50% 40%',
		'still-lfd-westminster-9.jpg' => '67% 46%',
		'still-ofd-s31-campaign-13.jpg' => '41% 24%',
		'still-ofd-s31-campaign-21.jpg' => '68% 15%',
		'sw26-black.jpg' => '51% 19%',
		'sw26-botanical-garden.jpg' => '48% 17%',
		'sw26-cruise.jpg' => '49% 17%',
		'sw26-denim.jpg' => '52% 18%',
		'sw26-linen.jpg' => '49% 20%',
		'sw26-maramax-academy.jpg' => '46% 23%',
		'sw26-outerwear.jpg' => '50% 21%',
		'sw26-tailoring.jpg' => '48% 18%',
	);
}

<?php
/**
 * Title: Website project
 * Slug: ak-studio/project-website
 * Categories: ak-studio
 * Block Types: core/post-content
 * Post Types: ak_project
 * Keywords: website, project, case study
 * Description: A website case study: the name, the site itself large, the facts, the story and more screens. Tick "Websites" under Project type so it appears in the websites portfolio.
 *
 * @package AK_Studio
 */

$ak_cta = function_exists( 'ak_brand_pattern_id' ) ? ak_brand_pattern_id( 'cta' ) : 0;
?>
<!-- wp:ak/page-header {"eyebrow":"<a href="/work/?type=website">Work</a> / Websites","heading":"Client name","lede":"What the site is, for whom, in one sentence."} /-->

<!-- wp:image {"sizeSlug":"full","linkDestination":"none","align":"wide"} -->
<figure class="wp-block-image alignwide size-full"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:ak/facts {"items":[{"label":"Client","value":"Client name"},{"label":"What","value":"Online store"},{"label":"Our part","value":"Design and development"}],"linkLabel":"example.co.uk","linkUrl":"https://example.co.uk/"} /-->

<!-- wp:group {"className":"is-style-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-section"><!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead">The client and what they needed, in one or two sentences.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>What we designed and built, and why it works for the client's customers.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-section"><!-- wp:gallery {"linkTo":"media","imageCrop":false,"className":"is-style-masonry"} -->
<figure class="wp-block-gallery has-nested-images columns-default is-style-masonry"></figure>
<!-- /wp:gallery --></div>
<!-- /wp:group -->
<?php if ( $ak_cta ) : ?>

<!-- wp:block {"ref":<?php echo (int) $ak_cta; ?>} /-->
<?php endif; ?>

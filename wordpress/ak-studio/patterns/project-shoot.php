<?php
/**
 * Title: Shoot, show or brand project
 * Slug: ak-studio/project-shoot
 * Categories: ak-studio
 * Block Types: core/post-content
 * Post Types: ak_project
 * Keywords: shoot, show, campaign, season, project
 * Description: A case study told in pictures: a full-screen cover, the facts, the story with a quote and credits, the film and the photographs.
 *
 * @package AK_Studio
 */

$ak_cta = function_exists( 'ak_brand_pattern_id' ) ? ak_brand_pattern_id( 'cta' ) : 0;
?>
<!-- wp:ak/page-header {"eyebrow":"<a href="/work/">Work</a> / Photo & film","heading":"Project name","size":"cover"} /-->

<!-- wp:ak/facts {"items":[{"label":"When","value":"Date"},{"label":"Where","value":"Place, city"},{"label":"Our part","value":"Concept, production, film"}]} /-->

<!-- wp:group {"className":"is-style-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-section"><!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead">The idea behind the project, in one or two sentences.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>How it was made: the place, the people, the day.</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>A line from the client, the designer or the founder.</p>
<!-- /wp:paragraph --><cite>Name, role</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Credits</h3>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-credits"} -->
<ul class="wp-block-list is-style-credits"><!-- wp:list-item -->
<li><strong>Photography</strong> Name</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Film</strong> Name</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-section"><!-- wp:video {"className":"is-style-film"} -->
<figure class="wp-block-video is-style-film"></figure>
<!-- /wp:video --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-section"><!-- wp:gallery {"linkTo":"media","imageCrop":false,"className":"is-style-masonry"} -->
<figure class="wp-block-gallery has-nested-images columns-default is-style-masonry"></figure>
<!-- /wp:gallery --></div>
<!-- /wp:group -->
<?php if ( $ak_cta ) : ?>

<!-- wp:block {"ref":<?php echo (int) $ak_cta; ?>} /-->
<?php endif; ?>

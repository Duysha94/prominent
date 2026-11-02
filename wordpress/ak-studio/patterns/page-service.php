<?php
/**
 * Title: Service page
 * Slug: ak-studio/page-service
 * Categories: ak-studio
 * Block Types: core/post-content
 * Post Types: page
 * Keywords: service, page, offer
 * Description: A new service: header with a photo, what we do, what you get, how it runs, related work, questions and a closing call to action. Put the page under Services so it joins the services list.
 *
 * @package AK_Studio
 */

?>
<!-- wp:ak/page-header {"eyebrow":"Service 07 — Name","heading":"What the client gets, <em>in one line.</em>","lede":"Who this service is for and what it changes for them, in two sentences.","question":"Who does this?"} /-->

<!-- wp:group {"className":"is-style-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-section"><!-- wp:heading -->
<h2 class="wp-block-heading">What we do</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The problem a client has before they come to us, and how we solve it.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What you get</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>First thing the client receives</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Second thing the client receives</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">How it runs</h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true,"className":"is-style-steps"} -->
<ol class="wp-block-list is-style-steps"><!-- wp:list-item -->
<li><strong>First step.</strong> What happens.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Second step.</strong> What happens.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Third step.</strong> What happens.</li>
<!-- /wp:list-item --></ol>
<!-- /wp:list --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-section"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Our work</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-display"} -->
<h2 class="wp-block-heading is-style-display">Projects <em>like this.</em></h2>
<!-- /wp:heading -->

<!-- wp:ak/work {"count":3,"columns":3,"moreLabel":"All work","moreUrl":"/work/"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-section"><!-- wp:group {"className":"ak-faq","layout":{"type":"constrained"}} -->
<div class="wp-block-group ak-faq"><!-- wp:heading -->
<h2 class="wp-block-heading">Questions</h2>
<!-- /wp:heading -->

<!-- wp:details -->
<details class="wp-block-details"><summary>A question clients ask</summary><!-- wp:paragraph -->
<p>The answer.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary>Another question</summary><!-- wp:paragraph -->
<p>The answer.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-cta","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-cta"><!-- wp:heading {"className":"is-style-display"} -->
<h2 class="wp-block-heading is-style-display">Start <em>with us.</em></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead">Tell us what you are working on. You will receive a written proposal — scope, timeline and fee — before any work begins.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">Start a project</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

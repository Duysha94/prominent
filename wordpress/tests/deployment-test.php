<?php
/**
 * Deployment engine test suite.
 *
 * Runs against a REAL WordPress install — the engine's failure modes are all
 * database behaviour, and three rounds of static review of its predecessor
 * missed every one of them.
 *
 * Usage: copy beside wp-load.php and run
 *
 *   php deployment-test.php
 *
 * It mutates the database it runs against. Never point it at production.
 *
 * Not shipped in the theme zip: test code has no business on a live site.
 *
 * @package ak-zeyna-child
 */

$_SERVER['HTTP_HOST']='127.0.0.1:9410'; $_SERVER['REQUEST_URI']='/wp-admin/'; $_SERVER['REQUEST_METHOD']='GET';
define('WP_ADMIN', true);
require dirname(__FILE__).'/wp-load.php';
wp_set_current_user(1);

$pass=0; $fail=0;
function ok($c,$m){ global $pass,$fail; if($c){$pass++; echo "  PASS  $m\n";} else {$fail++; echo "  FAIL  $m\n";} }

// Content state only — the version marker is expected to change.
function content_state(){
  $out=[];
  foreach(get_posts(['post_type'=>['page','post','portfolio'],'post_status'=>'any','numberposts'=>-1,'orderby'=>'ID','order'=>'ASC']) as $p)
    $out[]="post:{$p->post_type}:{$p->post_name}:#{$p->ID}:".(get_post_meta($p->ID,'_ak_seed_key',true)?:'-');
  foreach(wp_get_nav_menus() as $m){
    $out[]="menu:{$m->name}:#{$m->term_id}";
    foreach((wp_get_nav_menu_items($m->term_id)?:[]) as $i) $out[]="  nav:{$i->title}:".(get_post_meta($i->ID,'_ak_seed_key',true)?:'-');
  }
  $out[]='front:'.get_option('page_on_front').' posts:'.get_option('page_for_posts').' privacy:'.get_option('wp_page_for_privacy_policy');
  $r = get_option('pe-redux'); $out[]='redux:'.json_encode($r);
  return $out;
}
function redeploy(){ delete_option('akbrand_content_version'); delete_transient('akbrand_deploy_lock'); delete_transient('ak_deploy_report'); do_action('admin_init'); return get_transient('ak_deploy_report'); }

echo "\n=== T1  idempotency: content state identical across three deployments ===\n";
$a = content_state(); redeploy();
$b = content_state(); redeploy();
$c = content_state();
ok($a===$b, 'run 1 vs run 2 identical');
ok($b===$c, 'run 2 vs run 3 identical');
if($a!==$b){ foreach(array_diff($b,$a) as $d) echo "     +$d\n"; foreach(array_diff($a,$b) as $d) echo "     -$d\n"; }

echo "\n=== T2  version gate: no work when versions match ===\n";
update_option('akbrand_content_version', AK_CHILD_VERSION);
delete_transient('ak_deploy_report');
do_action('admin_init');
ok(get_transient('ak_deploy_report')===false, 'deployment did not run at matching version');

echo "\n=== T3  duplicate self-heal ===\n";
$about = get_posts(['post_type'=>'page','meta_key'=>'_ak_seed_key','meta_value'=>'ak_about','numberposts'=>1]);
$dupe = wp_insert_post(['post_type'=>'page','post_title'=>'About','post_name'=>'about-dupe','post_status'=>'publish']);
update_post_meta($dupe,'_ak_seed_key','ak_about'); update_post_meta($dupe,'_ak_managed','1');
$r = redeploy();
$still = get_posts(['post_type'=>'page','meta_key'=>'_ak_seed_key','meta_value'=>'ak_about','numberposts'=>-1,'post_status'=>'any']);
ok(count($still)===1, 'exactly one ak_about survives (was 2)');
ok((int)$still[0]->ID === (int)$about[0]->ID, 'the ORIGINAL id was kept, not the duplicate');

echo "\n=== T4  obsolete managed content is deleted ===\n";
$orphan = wp_insert_post(['post_type'=>'page','post_title'=>'Retired Page','post_name'=>'retired','post_status'=>'publish']);
update_post_meta($orphan,'_ak_managed','1'); update_post_meta($orphan,'_ak_seed_key','ak_gone_in_this_release');
redeploy();
ok(!get_post($orphan), 'managed page absent from manifest was deleted');

echo "\n=== T5  confirmed-legacy content is purged even after a first deploy ===\n";
// Evidence, not absence: the WXR importer preserves the vendor guid verbatim.
global $wpdb;
$demo = wp_insert_post(['post_type'=>'page','post_title'=>'Demo Team','post_name'=>'demo-team','post_status'=>'publish']);
$wpdb->update($wpdb->posts, ['guid'=>'https://themes.pethemes.com/zeyna/?page_id=42'], ['ID'=>$demo]);
clean_post_cache($demo);
$dmenu = wp_create_nav_menu('Demo Menu '.wp_rand(1000,9999));
wp_update_nav_menu_item($dmenu,0,['menu-item-title'=>'Demo','menu-item-url'=>'https://themes.pethemes.com/zeyna/','menu-item-type'=>'custom','menu-item-status'=>'publish']);
update_option('pe-redux', array_merge((array)get_option('pe-redux'), ['contact_address'=>'Main Hub, NYC']));
redeploy();
ok(!get_post($demo), 'page carrying a vendor GUID purged');
ok(!wp_get_nav_menu_object($dmenu), 'menu linking to a vendor host purged');
remove_all_filters('option_pe-redux');
$raw = get_option('pe-redux');
ok(empty($raw['contact_address']), 'demo contact address cleared from pe-redux');

// The mirror of the above: the same clearing must NOT happen on a site with
// no demo residue at all, because pe-redux is a plugin-owned option.
update_option('pe-redux', array_merge((array)get_option('pe-redux'), ['contact_address'=>'Typed by the owner']));
redeploy();
remove_all_filters('option_pe-redux');
$raw2 = get_option('pe-redux');
ok($raw2['contact_address'] === 'Typed by the owner', 'pe-redux left alone when the site shows no demo evidence');

echo "\n=== T6  no pe-redux churn: repeated deploys stop reporting changes ===\n";
redeploy(); $r1 = get_transient('ak_deploy_report');
redeploy(); $r2 = get_transient('ak_deploy_report');
$churn = false;
foreach(($r2['deleted']??[]) as $d) if (strpos($d,'pe-redux')!==false) $churn = true;
ok(!$churn, 'pe-redux not rewritten on a steady-state deployment');

echo "\n=== T7  failure does not advance the version marker ===\n";
update_option('akbrand_content_version','0.0.0');
// Force a genuine WP_Error out of wp_insert_post() for one specific entry,
// via the hook core itself uses to reject a post.
add_filter('ak_manifest', function($m){ $m['posts'][] = ['key'=>'ak_boom','type'=>'page','slug'=>'ak-boom','title'=>'AK_BOOM_TEST']; return $m; });
add_filter('wp_insert_post_empty_content', function($empty, $data){
  return ($data['post_title'] ?? '') === 'AK_BOOM_TEST' ? true : $empty;
}, 10, 2);
delete_transient('akbrand_deploy_lock'); delete_transient('ak_deploy_report');
do_action('admin_init');
$r = get_transient('ak_deploy_report');
ok(!empty($r['errors']), 'the broken entry produced an error');
ok(get_option('akbrand_content_version')==='0.0.0', 'version marker did NOT advance after a failed run');
$log = get_option('akbrand_deploy_log', []);
ok(!empty($log) && $log[count($log)-1]['ok']===false, 'failure recorded in the deployment log');

echo "\n=== T9  deletion scope: only AK and confirmed-legacy namespaces ===\n";
remove_all_filters('ak_manifest');

// SYSTEM — a page an editor wrote by hand. No AK marker, no demo evidence.
$hand = wp_insert_post(['post_type'=>'page','post_title'=>'Editor Wrote This','post_name'=>'editor-page','post_status'=>'publish','post_content'=>'Ordinary content.']);
// SYSTEM — another plugin's content type, inside a purgeable type name.
$plugin_page = wp_insert_post(['post_type'=>'page','post_title'=>'Plugin Landing','post_name'=>'plugin-landing','post_status'=>'publish']);
update_option('woocommerce_shop_page_id', $plugin_page);
// SYSTEM — a plugin-owned post type.
if (!post_type_exists('wpcf7_contact_form')) register_post_type('wpcf7_contact_form',['public'=>false,'label'=>'Forms']);
$form = wp_insert_post(['post_type'=>'wpcf7_contact_form','post_title'=>'Owner Form','post_status'=>'publish']);
// LEGACY — vendor guid, exactly what the WXR importer preserves.
$leg_guid = wp_insert_post(['post_type'=>'page','post_title'=>'Demo Agency','post_name'=>'demo-agency','post_status'=>'publish']);
global $wpdb; $wpdb->update($wpdb->posts, ['guid'=>'https://themes.pethemes.com/zeyna/?page_id=91'], ['ID'=>$leg_guid]); clean_post_cache($leg_guid);
// LEGACY — vendor asset URL inside Elementor's stored JSON.
$leg_el = wp_insert_post(['post_type'=>'page','post_title'=>'Demo Built Page','post_name'=>'demo-built','post_status'=>'publish']);
update_post_meta($leg_el,'_elementor_data','[{"settings":{"image":{"url":"https://themes.pethemes.com/zeyna/wp-content/uploads/x.jpg"}}}]');
// LEGACY menu — an item pointing at a vendor URL.
$leg_menu = wp_create_nav_menu('Demo Nav '.wp_rand(1000,9999));
wp_update_nav_menu_item($leg_menu,0,['menu-item-title'=>'Humana Studio','menu-item-url'=>'https://zeyna.pethemes.com/agency/','menu-item-type'=>'custom','menu-item-status'=>'publish']);
// SYSTEM menu — hand-built, ordinary links.
$own_menu = wp_create_nav_menu('Owner Footer Nav '.wp_rand(1000,9999));
wp_update_nav_menu_item($own_menu,0,['menu-item-title'=>'Stockists','menu-item-url'=>'https://akbrand.studio/stockists/','menu-item-type'=>'custom','menu-item-status'=>'publish']);
// A widget in a theme sidebar.
update_option('sidebars_widgets', ['sidebar-1'=>['text-9'], 'wp_inactive_widgets'=>[]]);

$r = redeploy();

ok(get_post($hand) && get_post_status($hand)!=='trash', 'RULE 5 — hand-written page NOT deleted (unknown scope)');
ok((bool)get_post($plugin_page), 'RULE 4 — plugin-referenced page NOT deleted');
ok((bool)get_post($form), 'RULE 4 — plugin-owned post type NOT deleted');
ok(!get_post($leg_guid), 'RULE 2 — page with vendor GUID purged');
ok(!get_post($leg_el), 'RULE 2 — page with vendor asset URL in _elementor_data purged');
ok(!wp_get_nav_menu_object($leg_menu), 'RULE 2 — menu linking to a vendor host purged');
ok((bool)wp_get_nav_menu_object($own_menu), 'RULE 5 — hand-built menu NOT deleted');
$obs = $r['observed'] ?? [];
$seen = false; foreach($obs as $o) if (strpos($o,'Editor Wrote This')!==false) $seen = true;
ok($seen, 'unclaimed content is REPORTED rather than silently ignored');
$sb = get_option('sidebars_widgets');
ok(empty($sb['sidebar-1']) && in_array('text-9', $sb['wp_inactive_widgets'] ?? [], true),
   'widgets DEACTIVATED not deleted (recoverable from Appearance -> Widgets)');

echo "\n=== T10  a managed page referenced by a site setting is kept, not deleted ===\n";
$mp = wp_insert_post(['post_type'=>'page','post_title'=>'Managed But Wired','post_name'=>'managed-wired','post_status'=>'publish']);
update_post_meta($mp,'_ak_managed','1'); update_post_meta($mp,'_ak_seed_key','ak_no_longer_shipped');
update_option('woocommerce_cart_page_id', $mp);
$r = redeploy();
ok((bool)get_post($mp), 'obsolete managed page referenced by a setting was kept');
$kept=false; foreach(($r['skipped']??[]) as $k) if (strpos($k,'Managed But Wired')!==false) $kept=true;
ok($kept, 'and the reason was reported');
update_option('woocommerce_cart_page_id', 0);
delete_post_meta($mp,'_ak_managed'); wp_delete_post($mp,true);
update_option('woocommerce_shop_page_id', 0);

echo "\n=== T11  legacy purge still fires when a demo lands AFTER a deployment ===\n";
$late = wp_insert_post(['post_type'=>'page','post_title'=>'Late Demo','post_name'=>'late-demo','post_status'=>'publish']);
$wpdb->update($wpdb->posts, ['guid'=>'https://themes.pethemes.com/zeyna/?page_id=777'], ['ID'=>$late]); clean_post_cache($late);
redeploy();
ok(!get_post($late), 'demo imported after a deployment is still purged on the next one');

echo "\n=== T8  concurrency lock ===\n";
remove_all_filters('ak_manifest');
update_option('akbrand_content_version','0.0.0');
set_transient('akbrand_deploy_lock', 1, 300);
delete_transient('ak_deploy_report');
do_action('admin_init');
ok(get_transient('ak_deploy_report')===false, 'a second concurrent request did not deploy');
delete_transient('akbrand_deploy_lock');

echo "\n".str_repeat('=',52)."\n  $pass passed, $fail failed\n".str_repeat('=',52)."\n";
exit($fail ? 1 : 0);

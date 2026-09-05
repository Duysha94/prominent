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

echo "\n=== T5  unmanaged demo content is purged even after first deploy ===\n";
$demo = wp_insert_post(['post_type'=>'page','post_title'=>'Demo Team','post_name'=>'demo-team','post_status'=>'publish']);
$dmenu = wp_create_nav_menu('Demo Menu '.wp_rand(1000,9999));
update_option('pe-redux', array_merge((array)get_option('pe-redux'), ['contact_address'=>'Main Hub, NYC']));
redeploy();
ok(!get_post($demo), 'unmanaged demo page purged');
ok(!wp_get_nav_menu_object($dmenu), 'unmanaged demo menu purged');
remove_all_filters('option_pe-redux');
$raw = get_option('pe-redux');
ok(empty($raw['contact_address']), 'demo contact address cleared from pe-redux');

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

<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Administration panel - Manager of ratings
 *
 * @package Ratings
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'ratings');
cot_block($usr['isadmin']);

require_once cot_incfile('ratings', 'plug');

$t = new XTemplate(cot_tplfile('ratings.admin', 'plug', true));

$adminHelp = $L['adm_help_ratings'];
$adminTitle = $L['Ratings'];

$id = cot_import('id', 'G', 'TXT');
$maxperpage = ($cfg['maxrowsperpage'] && is_numeric($cfg['maxrowsperpage']) && $cfg['maxrowsperpage'] > 0) ? $cfg['maxrowsperpage'] : 15;
list($pg, $d, $durl) = cot_import_pagenav('d', $maxperpage);

/* === Hook  === */
foreach (cot_getextplugins('admin.ratings.first') as $pl) {
	include $pl;
}
/* ===== */

if ($a == 'delete') {
	cot_check_xg();
	Cot::$db->delete($db_ratings, 'rating_code = ' . Cot::$db->quote($id));
	Cot::$db->delete($db_rated, 'rated_code = ' . Cot::$db->quote($id));
	/* === Hook  === */
	foreach (cot_getextplugins('admin.ratings.delete.done') as $pl) {
		include $pl;
	}
	/* ===== */
	
	cot_message('adm_ratings_already_del');
	cot_redirect(cot_url('admin', 'm=other&p=ratings', '', true));
}

$totalitems = Cot::$db->countRows($db_ratings);
$pagenav = cot_pagenav(
	'admin',
	'm=other&p=ratings',
	$d,
	$totalitems,
	$maxperpage,
	'd',
	'',
	$cfg['jquery'] && $cfg['turnajax']
);

$sql = Cot::$db->query("SELECT * FROM $db_ratings WHERE 1 ORDER by rating_id DESC LIMIT $d, ".$maxperpage);

$ii = 0;
$jj = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('admin.ratings.loop');
/* ===== */
foreach ($sql->fetchAll() as $row) {
	$id2 = $row['rating_code'];
	$sql1 = Cot::$db->query("SELECT COUNT(*) FROM $db_rated WHERE rated_code = " . Cot::$db->quote($id2));//@TODO don't make requests in the loop
	$votes = $sql1->fetchColumn();

	$rat_type = $row['rating_area'];
	$rat_value = $row['rating_code'];

	switch ($rat_type) {
		case 'page':
			$rat_url = cot_url('page', 'id=' . $rat_value);
			break;
		default:
			$rat_url = '';
			break;
	}

	$t->assign([
		'ADMIN_RATINGS_ROW_URL_DEL' => cot_url('admin', 'm=other&p=ratings&a=delete&id=' . $row['rating_code'] . '&d=' . $durl . '&' . cot_xg()),
		'ADMIN_RATINGS_ROW_RATING_CODE' => $row['rating_code'],
		'ADMIN_RATINGS_ROW_RATING_AREA' => $row['rating_area'],
		'ADMIN_RATINGS_ROW_CREATIONDATE' => cot_date('datetime_medium', $row['rating_creationdate']),
		'ADMIN_RATINGS_ROW_CREATIONDATE_STAMP' => $row['rating_creationdate'],
		'ADMIN_RATINGS_ROW_VOTES' => $votes,
		'ADMIN_RATINGS_ROW_RATING_AVERAGE' => $row['rating_average'],
		'ADMIN_RATINGS_ROW_RAT_URL' => $rat_url,
		'ADMIN_RATINGS_ROW_ODDEVEN' => cot_build_oddeven($ii),
	]);
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl) {
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN.RATINGS_ROW');
	$ii++;
	$jj = $jj + $votes;
}

$t->assign([
	'ADMIN_RATINGS_URL_CONFIG' => cot_url('admin', 'm=config&n=edit&o=plug&p=ratings'),
	'ADMIN_RATINGS_ON_PAGE' => $ii,
	'ADMIN_RATINGS_TOTALVOTES' => $jj,
]);

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.25
    $t->assign([
        'ADMIN_RATINGS_PAGINATION_PREV' => $pagenav['prev'],
        'ADMIN_RATINGS_PAGNAV' => $pagenav['main'],
        'ADMIN_RATINGS_PAGINATION_NEXT' => $pagenav['next'],
        'ADMIN_RATINGS_TOTALITEMS' => $totalitems,
    ]);
}
$t->assign(cot_generatePaginationTags($pagenav));

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('admin.ratings.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');

if (COT_AJAX) {
	$t->out('MAIN');
} else {
	$adminMain = $t->text('MAIN');
}
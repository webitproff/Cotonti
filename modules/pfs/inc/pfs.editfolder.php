<?php
/**
 * Personal File Storage, edit folder.
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\users\UsersRepository;

defined('COT_CODE') or die('Wrong URL');

$f = cot_import('f','G','INT');
$c1 = cot_import('c1','G','ALP');
$c2 = cot_import('c2','G','ALP');
$userid = cot_import('userid','G','INT');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('pfs', 'a');
cot_block(Cot::$usr['auth_write']);

$more = '';
if (!Cot::$usr['isadmin'] || is_null($userid)) {
	$userid = Cot::$usr['id'];
} else {
	$more = 'userid=' . $userid;
}

if ($userid != Cot::$usr['id']) {
	cot_block(Cot::$usr['isadmin']);
}

$standalone = FALSE;
$uid = ($userid > 0) ? $userid : Cot::$usr['id'];
$user_info = UsersRepository::getInstance()->getById($uid);
$maingroup = ($userid == 0) ? COT_GROUP_SUPERADMINS : $user_info['user_maingrp'];

$pfs_dir_user = cot_pfs_path($userid);
$thumbs_dir_user = cot_pfs_thumbpath($userid);

reset($cot_extensions);
foreach ($cot_extensions as $k => $line) {
	$icon[$line[0]] = cot_rc('pfs_icon_type', array('type' => $line[2], 'name' => $line[1]));
	$filedesc[$line[0]] = $line[1];
}

if (!empty($c1) || !empty($c2)) {
	$more .= empty($more) ? 'c1='.$c1.'&c2='.$c2 : '&c1='.$c1.'&c2='.$c2;
	$standalone = TRUE;
}

/* ============= */

$pfsTitle = ($userid == 0) ? Cot::$L['SFS'] : Cot::$L['pfs_myFiles'];
$breadcrumbs = [
    [cot_url('pfs', $more), $pfsTitle],
];

/* === Hook === */
foreach (cot_getextplugins('pfs.editfolder.first') as $pl) {
	include $pl;
}
/* ===== */

if ($userid != Cot::$usr['id']) {
	cot_block(Cot::$usr['isadmin']);
	($userid == 0) || $breadcrumbs[] = array(cot_url('users', 'm=details&id='.$user_info['user_id']), $user_info['user_name']);
}

$sql_pfs = Cot::$db->query(
    'SELECT * FROM ' . Cot::$db->pfs_folders . " WHERE pff_userid = :userId AND pff_id = :folderId LIMIT 1",
    ['userId' => $userid, 'folderId' => $f]
);

if ($row = $sql_pfs->fetch()) {
	$pff_id=$row['pff_id'];
	$pff_date = $row['pff_date'];
	$pff_updated = $row['pff_updated'];
	$pff_title = $row['pff_title'];
	$pff_desc = $row['pff_desc'];
	$pff_ispublic = $row['pff_ispublic'];
	$pff_isgallery = $row['pff_isgallery'];
	$pff_count = $row['pff_count'];
} else {
	cot_die();
}

$breadcrumbs[] = htmlspecialchars($pff_title) . ' (' . Cot::$L['Edit'] . ')';

if ($a === 'update' && !empty($f)) {
	$rtitle = cot_import('rtitle','P','TXT');
	$rdesc = cot_import('rdesc','P','TXT');
	$folderid = cot_import('folderid','P','INT');
	$rispublic = (int) cot_import('rispublic','P','BOL');
	$risgallery = (int) cot_import('risgallery','P','BOL');
	$sql_pfs_pffcount = $db->query("SELECT pff_id FROM $db_pfs_folders WHERE pff_userid=$userid AND pff_id=$f");
	cot_die($sql_pfs_pffcount->rowCount()==0);

	$sql_pfs_update = $db->query("UPDATE $db_pfs_folders SET
		pff_title='".$db->prep($rtitle)."',
		pff_updated='".$sys['now']."',
		pff_desc='".$db->prep($rdesc)."',
		pff_ispublic=$rispublic,
		pff_isgallery=$risgallery
		WHERE pff_userid=$userid AND pff_id=$f" );

	cot_redirect(cot_url('pfs', $more, '', true));
}

/* ============= */

Cot::$out['subtitle'] = $pfsTitle;

if (!$standalone) {
	require_once Cot::$cfg['system_dir'] . '/header.php';
}

$t = new XTemplate(cot_tplfile('pfs.editfolder'));

if ($standalone) {
    cot_sendheaders();

	$html = Resources::render();
    if ($html) {
        Cot::$out['head_head'] = $html . (isset(Cot::$out['head_head']) ? Cot::$out['head_head'] : '');
    }

    $t->assign(array(
        'PFS_HEAD' => $out['head_head'],
        'PFS_C1' => $c1,
        'PFS_C2' => $c2
    ));

	$t->parse('MAIN.STANDALONE_HEADER');
	$t->parse('MAIN.STANDALONE_FOOTER');
}

$t->assign(array(
	'PFS_TITLE' => cot_breadcrumbs($breadcrumbs, $cfg['homebreadcrumb']),
    'PFS_BREADCRUMBS' => cot_breadcrumbs($breadcrumbs, Cot::$cfg['homebreadcrumb']),
	'PFS_ACTION' => cot_url('pfs', 'm=editfolder&a=update&f=' . $pff_id . '&' . $more),
//	'PFF_FOLDER' => cot_selectbox_folders($userid, '', $row['pff_parentid'], 'rparentid'),
	'PFF_TITLE' => cot_inputbox('text', 'rtitle', htmlspecialchars($pff_title), 'size="56" maxlength="255"'),
	'PFF_DESC' => cot_inputbox('text', 'rdesc',  htmlspecialchars($pff_desc), 'size="56" maxlength="255"'),
	'PFF_DATE' => cot_date('datetime_medium', $row['pff_date']),
	'PFF_DATE_STAMP' => $row['pff_date'],
	'PFF_ISGALLERY' => cot_radiobox($pff_isgallery, 'risgallery', array('1', '0'), array($L['Yes'], $L['No']), '', ' '),
	'PFF_ISPUBLIC' => cot_radiobox($pff_ispublic, 'rispublic', array('1', '0'), array($L['Yes'], $L['No']), '', ' '),
	'PFF_UPDATED' => cot_date('datetime_medium', $row['pff_updated']),
	'PFF_UPDATED_STAMP' => $row['pff_updated'],
    'PFS_IS_STANDALONE' => $standalone,
));

cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('pfs.editfolder.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

if (!$standalone)
{
	require_once $cfg['system_dir'] . '/footer.php';
}

<?php
/**
 * Personal File Storage, edit
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\users\UsersRepository;

defined('COT_CODE') or die('Wrong URL');

$id = cot_import('id', 'G', 'INT');
$c1 = cot_import('c1', 'G', 'ALP');
$c2 = cot_import('c2', 'G', 'ALP');
$userid = cot_import('userid', 'G', 'INT');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('pfs', 'a');
cot_block(Cot::$usr['auth_write']);

$more = '';
if (!Cot::$usr['isadmin'] || $userid === null) {
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
foreach ($cot_extensions as $k => $line)
{
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
foreach (cot_getextplugins('pfs.edit.first') as $pl) {
	include $pl;
}
/* ===== */

if ($userid != Cot::$usr['id']) {
	cot_block(Cot::$usr['isadmin']);
	($userid == 0) || $breadcrumbs[] = array(cot_url('users', 'm=details&id='.$user_info['user_id']), $user_info['user_name']);
}

$sql_pfs = Cot::$db->query(
    'SELECT * FROM ' . Cot::$db->pfs . ' WHERE pfs_userid = :userId AND pfs_id = :pfsId LIMIT 1',
    ['userId' => $userid, 'pfsId' =>$id]
);

if ($row = $sql_pfs->fetch()) {
	$pfs_id = $row['pfs_id'];
	$pfs_file = $row['pfs_file'];
	$pfs_date = $row['pfs_date'];
	$pfs_folderid = $row['pfs_folderid'];
	$pfs_extension = $row['pfs_extension'];
	$pfs_desc = htmlspecialchars($row['pfs_desc']);
	$pfs_size = floor($row['pfs_size'] / 1024); // in KiB; deprecated but kept for compatibility
	$pfs_size_bytes = $row['pfs_size'];
	$ff = $pfs_dir_user.$pfs_file;
} else {
	cot_die();
}

$breadcrumbs[] = htmlspecialchars($pfs_file) . ' (' . Cot::$L['Edit'] . ')';

if ($a === 'update' && !empty($id)) {
	$rdesc = cot_import('rdesc','P','TXT');
	$folderid = cot_import('folderid','P','INT');
	if ($folderid>0) {
		$sql_pfs_pffcount = $db->query("SELECT pff_id FROM $db_pfs_folders WHERE pff_userid=$userid AND pff_id=$folderid");
		cot_die($sql_pfs_pffcount->rowCount()==0);
	} else {
		$folderid = 0;
	}

	$sql_pfs_update = $db->query("UPDATE $db_pfs SET
		pfs_desc='".$db->prep($rdesc)."',
		pfs_folderid=$folderid
		WHERE pfs_userid=$userid AND pfs_id=$id");

	cot_redirect(cot_url('pfs', "f=$pfs_folderid&".$more, '', true));
}

/* ============= */

Cot::$out['subtitle'] = $pfsTitle;

if (!$standalone) {
	require_once Cot::$cfg['system_dir'] . '/header.php';
}

$t = new XTemplate(cot_tplfile('pfs.edit'));

if ($standalone) {
	cot_sendheaders();

	$html = Resources::render();
	if ($html) {
        Cot::$out['head_head'] = $html . (isset(Cot::$out['head_head']) ? Cot::$out['head_head'] : '');
    }

    $t->assign([
        'PFS_HEAD' => Cot::$out['head_head'],
    ]);

	$t->parse('MAIN.STANDALONE_HEADER');
	$t->parse('MAIN.STANDALONE_FOOTER');
}

$t->assign([
	'PFS_TITLE' => cot_breadcrumbs($breadcrumbs, $cfg['homebreadcrumb']),
    'PFS_BREADCRUMBS' => cot_breadcrumbs($breadcrumbs, Cot::$cfg['homebreadcrumb']),
	'PFS_ACTION'=> cot_url('pfs', 'm=edit&a=update&id='.$pfs_id.'&'.$more),
	'PFS_FILE' => htmlspecialchars($pfs_file),
	'PFS_DATE' => cot_date('datetime_medium', $pfs_date),
	'PFS_DATE_STAMP' => $pfs_date,
	'PFS_FOLDER' => cot_selectbox_folders($userid, '', $pfs_folderid),
	'PFS_URL' => $ff,
	'PFS_DESC' => cot_inputbox('text', 'rdesc', $pfs_desc, 'maxlength="255"'),
	'PFS_SIZE' => cot_build_filesize($pfs_size_bytes, 1),
	'PFS_SIZE_BYTES' => $pfs_size_bytes,
    'PFS_IS_STANDALONE' => $standalone,
	'PFS_SIZE_KB' => $pfs_size_bytes / 1024, // in KiB; deprecated but kept for compatibility
]);

cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('pfs.edit.tags') as $pl)
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

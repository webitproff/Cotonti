<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topics.query
[END_COT_EXT]
==================== */

/**
 * Polls
 *
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\modules\forums\inc\ForumsDictionary;

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['forums']['cat_' . $s]['allowpolls']) {
    $where['poll'] = isset($where['poll']) ? $where['poll'] : '';
	$where['poll'] .= " (poll_type = '" . ForumsDictionary::SOURCE_TOPIC . "' OR poll_id IS NULL)";
	$join_columns = ', p.poll_id, p.poll_type';
	$join_condition = " LEFT JOIN $db_polls AS p ON t.ft_id=p.poll_code";
}

<?php
namespace App\Models;

use Lib\BaseSolr;

/**
 * Class JCG
 * @package App\Models
 */
class JCG extends BaseSolr
{

	/**
	 * 数据库名
	 * @return mixed
	 */
	public function getConnectionName() {
		return 'jcg';
	}

	/**
	 * 表名
	 * @return mixed
	 */
	public function getCoreName() {
		return 'jcg';
	}
}
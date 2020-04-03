<?php

namespace Model\Spider;


/**
 * 新闻主键表
 * Class NewsSeq
 * @package Model\Spider
 */
class NewsSeq extends SeqCommon {

	/**
	 * 表名
	 * @return mixed
	 */
	public function getTableName() {
		return 'news_seq';
		// TODO: Implement getTableName() method.
	}

}
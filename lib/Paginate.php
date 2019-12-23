<?php
namespace Lib;

use Slim\Http\Request;

/**
 * 分页
 * Class Paginate
 * @package Lib
 */
class Paginate
{
	private static $instance_list;

	private static $_guid_count = 1;
	private $guid;
	private $page_info;
	private $pagesize_flag = false;     //pagesize是否来自于GET
	private $params = array();

	private $config = array(
		'show_dot'     => true,
		'num_offset'   => 1,
		'page_size'    => 10,
		'page_key'     => 'page',
		'pagesize_key' => 'page_size',
		'mode'         => 'first,prev,num,next,last,info',

		'lang' => array(
			'page_first' => '<<',
			'page_prev'  => '<',
			'page_next'  => '>',
			'page_last'  => '>>',
			'page_info'  => 'Total:%s, Page:%d, Per:%i',
			'page_jump'  => '跳转',
			'page_size'  => '每页条数：',
			'page_sel'   => '第%s页',
		),
	);

	/**
	 * 私有构造方法,防止非单例调用
	 * @param $config
	 * @param array $params
	 */
	private function __construct($config, array $params = [])
	{
		$this->params = $params;
		$this->config = array_merge($this->config, $config);
		$this->guid = self::$_guid_count++;
		$this->setConfig($config);
	}

	/**
	 * 获取单例
	 * @param Request | array $param
	 * @param string $identify 分页唯一性ID
	 * @param array $config 配置
	 * @return Paginate
	 * @internal param IndexController $indexController
	 * @internal param Request $request
	 */
	public static function instance($param, $identify = 'page', $config = array())
	{
		if (!self::$instance_list[$identify]) {
			if ($param instanceof Request) {
				$params = $param->getParams();
			} else {
				$params = $param;
			}
			self::$instance_list[$identify] = new self($config, $params);
		}
		return self::$instance_list[$identify];
	}

	/**
	 * 设置配置
	 * @param $config
	 * @return $this
	 */
	public function setConfig($config)
	{
		$this->config = array_merge($this->config, $config);
		return $this;
	}

	/**
	 * 设置每页数量
	 * @param $num
	 */
	public function setPageSize($num)
	{
		$this->config['page_size'] = $num;
	}

	/**
	 * 获取配置
	 * @param string $key
	 * @return array
	 */
	public function getConfig($key = '')
	{
		return $key ? $this->config[$key] : $this->config;
	}

	/**
	 * 设置总数量
	 * @param int $item_total
	 * @return $this
	 */
	public function setItemTotal($item_total = 0)
	{
		$this->page_info['item_total'] = $item_total;
		return $this;
	}

	/**
	 * 获取分页信息
	 * @param string $key
	 * @return mixed
	 */
	public function getInfo($key = '')
	{
		$this->updatePageInfo();
		return $key ? $this->page_info[$key] : $this->page_info;
	}

	/**
	 * 获取limit信息
	 * @return array
	 */
	public function getLimit()
	{
		$this->updatePageInfo();
		$start = ($this->page_info['page_index'] - 1) * $this->page_info['page_size'];
		return array($start, $this->page_info['page_size']);
	}

	/**
	 * 更新(重载)分页信息
	 * @return $this
	 */
	private function updatePageInfo()
	{
		$page_index = $this->params[$this->config['page_key']];
		$page_index = $page_index > 0 ? $page_index : 1;

		$page_size = $this->params[$this->config['pagesize_key']];
		if ($page_size) {
			$this->pagesize_flag = true;
		} else {
			$page_size = $this->getConfig('page_size');
		}
		$item_total = $this->page_info['item_total'];

		$page_total = (int)ceil($item_total / $page_size);

		$this->page_info['page_index'] = $page_index;
		$this->page_info['page_size'] = $page_size;
		$this->page_info['page_total'] = $page_total;
		return $this;
	}

	/**
	 * 获取分页链接URL
	 * @param int $num 页码(1开始)
	 * @param null $page_size
	 * @return string
	 */
	private function getUrl($num = null, $page_size = null)
	{
		$page_key = $this->config['page_key'];
		$queryParam=[$page_key=>$num]+$this->params;
		return "?".http_build_query($queryParam);
	}

	/**
	 * 转换字符串
	 * @return string
	 */
	public function __toString()
	{
		$page_modes = array_map('trim', explode(',', $this->config['mode']));
		$this->updatePageInfo();
		$page_info = $this->getInfo();
		$page_config = $this->getConfig();
		$lang = $this->getConfig('lang');
		$html = '';

		foreach ($page_modes as $mode) {
			//first page
			if ($mode == 'first') {
				if ($page_info['page_index'] == 1) {
					$html .= '<li><span>' . $lang['page_first'] . '</span></li>';
				} else {
					$html .= '<li><a href="' . $this->getUrl(1, $page_info['page_size']) . '" class="page_first">' . $lang['page_first'] . '</a></li>';
				}
			} //last page
			else if ($mode == 'last') {
				$tmp = $lang['page_last'];
				$tmp = str_replace('%d', $page_info['page_total'], $tmp);
				if (empty($page_info['page_total']) || $page_info['page_index'] == $page_info['page_total']) {
					$html .= '<li><span>' . $tmp . '</span></li>';
				} else {
					$html .= '<li><a href="' . $this->getUrl($page_info['page_total'], $page_info['page_size']) . '" class="page_last">' . $tmp . '</a></li>';
				}
			} //next page
			else if ($mode == 'next') {
				$tmp = $lang['page_next'];
				if ($page_info['page_index'] < $page_info['page_total']) {
					$html .= '<li><a href="' . $this->getUrl($page_info['page_index'] + 1, $page_info['page_size']) . '" class="page_next">' . $tmp . '</a></li>';
				} else {
					$html .= '<li><span>' . $tmp . '</span></li>';
				}
			} //prev page
			else if ($mode == 'prev') {
				$tmp = $lang['page_prev'];
				$_html = "";
				if ($page_info['page_index'] > 1) {
					$_html = '<li><a href="' . $this->getUrl($page_info['page_index'] - 1, $page_info['page_size']) . '" class="page_prev">' . $tmp . '</a></li>';
				} else {
					$_html .= '<li><span>' . $tmp . '</span></li>';
				}
				$html .= $_html;
			} //page num
			else if ($mode == 'num') {
				$offset_len = $page_config['num_offset'];
				if (($page_info['page_index'] - $offset_len > 1) && $page_config['show_dot']) {
					$html .= '<li><a><em class="page_dots">...</em></a></li>';
				}
				for ($i = $page_info['page_index'] - $offset_len; $i <= $page_info['page_index'] + $offset_len; $i++) {
					if ($i > 0 && $i <= $page_info['page_total']) {
						$html .= ($page_info['page_index'] != $i) ? '<li><a href="' . $this->getUrl($i, $page_info['page_size']) . '">' . $i . '</a></li>' : '<li><a><em class="page_current">' . $i . '</a></em></li>';
					}
				}
				$html .= (($page_info['page_index'] + $offset_len < $page_info['page_total']) && $page_config['show_dot']) ? '<li><a><em class="page_dots">...</a></em></li>' : null;
				$html .= '</li>';
			} //total
			else if ($mode == 'info') {
				$html .= '<li><span>';
				$tmp = $lang['page_info'];
				$tmp = str_replace('%s', $page_info['item_total'], $tmp);
				$tmp = str_replace('%d', $page_info['page_total'], $tmp);
				$tmp = str_replace('%i', $page_info['page_size'], $tmp);
				$html .= $tmp;
				$html .= '</span></li>';
			}
		}
		return '<nav><ul class="pagination">' . $html . '</ul></nav>';
	}
}
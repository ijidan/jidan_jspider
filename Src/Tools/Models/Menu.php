<?php
namespace Tools\Models;

use Lib\Session;

/**
 * 菜单管理
 * Class Menu
 * @package Tools\Models
 */
class Menu {
	/**
	 * 获取菜单
	 * @return array
	 */
	public function getMenus() {
		$roleId = Session::get(Session::TOOLS_LOGIN_ROLE);
		if(!$roleId){
			return [];
		}
		$menus = $this->getAll();
		$role = AdminRole::findOne("role_id=?", [$roleId]);
		if ($role["role_id"] != AdminRole::ROLE_ID_ADMIN) {
			$roleAccess = $role["role_access"] ? \unserialize($role["role_access"]) : [];
			foreach ($menus as $title => $subMenu) {
				foreach ($subMenu as $id => $menu) {
					if (!in_array(self::cleanUrl($menu["url"]), $roleAccess)) {
						unset($menus[$title][$id]);
					}
				}
				if (empty($menus[$title])) {
					unset($menus[$title]);
				}
			}
		}
		return $menus;
	}

	/**
	 * 获取菜单
	 * @return array
	 */
	public function getAll() {
		$menus = [
			/*
			'Order' => [
				['name' => 'List', 'url' => '/order/showList'],
			],
			'Goods' => [
				['name' => 'List', 'url' => '/goods/showList'],
			],
			*/
			'常用网站' => [
				['name' => '网站列表', 'url' => '/websites/index'],
			],
			'商品分类' => [
				['name' => '分类列表', 'url' => '/trade/showCategoryList'],
			],
			'商品管理' => [
				['name' => '商品列表', 'url' => '/trade/showList'],
			],
			'工厂管理' => [
				['name' => '工厂列表', 'url' => '/factory/showList'],
			],
			/*
			'Order'  => [
				['name' => 'Pending List', 'url' => '/productOrder/showList'],
				['name' => 'Bought List', 'url' => '/productOrder/showBoughtList'],
				['name' => 'Canceled Bought List', 'url' => '/productOrder/showCanceledBoughtList'],
				['name' => 'Deleted List', 'url' => '/productOrder/showDeletedList'],
				//['name' => 'List', 'url' => '/user/showList'],
			],
			'Chat'  => [
				['name' => 'Chat List', 'url' => '/chat/showList']
			],
			'User'  => [
				['name' => 'List', 'url' => '/user/showAddressList'],
				//['name' => 'List', 'url' => '/user/showList'],
			],
			'Blog'  => [
				['name' => 'Article Category', 'url' => '/blog/showArticleCategory'],
				['name' => 'Article List', 'url' => '/blog/showArticleList'],
			],
			'Translation'  => [
				['name' => 'Search Keywords', 'url' => '/trans/showSearchKeywordsList'],
				['name' => 'Keywords Category', 'url' => '/trans/showKeywordsCategory'],
			],
			'Site'=>[
				['name' => 'Business', 'url' => '/site/showBusinessList'],
			],
			'Tools'  => [
				['name' => 'List', 'url' => '/tools/showList'],
			],
			*/
			'管理' => [
				['name' => '用户列表', 'url' => '/admin/operator'],
				['name' => '添加用户', 'url' => '/admin/addOperator'],
				['name' => '角色列表', 'url' => '/admin/showRoleList'],
				['name' => '添加角色', 'url' => '/admin/addRole'],
			]
		];
		return $menus;
	}

	/**
	 * 获取所有的URL
	 * @return array
	 */
	public function getAllUrls() {
		$menus = $this->getAll();
		$urls = [];
		foreach ($menus as $parent_item) {
			foreach ($parent_item as $item) {
				array_push($urls, Menu::cleanUrl($item["url"]));
			}
		}
		return $urls;
	}

	/**
	 * @param $url
	 * @return string
	 */
	public static function cleanUrl($url) {
		return strtolower(rtrim($url, "/"));
	}
}
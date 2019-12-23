<?php
namespace Tools\Models;

class UserManager {
	private function getRefModels($uid) {
		return [
			'User'            => [
				'model' => '\App\Models\User',
				'where' => ['_id' => $uid]
			],
			'UserBet'         => [
				'model' => '\App\Models\UserBet',
				'where' => ['uid' => $uid]
			],
			'UserTransaction' => [
				'model' => '\App\Models\UserTransaction',
				'where' => ['uid' => $uid]
			],
			'UserCart'        => [
				'model' => '\App\Models\UserCart',
				'where' => ['uid' => $uid]
			],
			'UserDailyQuest'  => [
				'model' => '\App\Models\UserDailyQuest',
				'where' => ['_id' => $uid]
			],
			'UserInventory'   => [
				'model' => '\App\Models\UserInventory',
				'where' => ['uid' => $uid]
			],
			'UserOrder'       => [
				'model' => '\App\Models\UserOrder',
				'where' => ['uid' => $uid]
			],
			'IdMap'           => [
				'model' => '\App\Models\IdMap',
				'where' => ['uid' => $uid]
			]
		];
	}

	/*
	 * 删除用户
	 * 用于测试账号
	 */
	public function clearUserData($uid, $specific) {
		$models = $this->getRefModels($uid);
		//只处理指定的model
		if ($specific && $models[$specific]) {
			$models = [$specific => $models[$specific]];
		}

		foreach ($models as $key => $data) {
			$model = $data['model'];
			if (!class_exists($model)) {
				continue;
			}
			$class = new $model($uid);
			$class->deleteMany($data['where']);
		}
	}
}
<?php
// 网站配置模型
class ConfigModel extends Model {
	/**
	 * 获取网站配置列表数组
	 */
	function getHash() {
		$list = $this->getField('name,cfgval');
		return $list;
	}
}

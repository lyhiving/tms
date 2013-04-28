<?php

/**
 *  _                 _
 * | |               (_)
 * | |_ _ __ ___  ___ _ _ __
 * | __| '_ ` _ \/ __| | '_ \
 * | |_| | | | | \__ \ | |_) |
 *  \__|_| |_| |_|___/_| .__/
 *                     | |
 *                     |_|
 * tms IP 控制函数集
 * @see http://www.taobao.com/go/rgn/market/docs/ip.php
 * @version 0.9.1
 */

/**
 * 获取客户端的真实 IP 地址
 * @return string
 */
if (!function_exists('get_client_ip')) {

	function get_client_ip () {

		// 预览参数劫持当前客户端 IP 地址
		if (isset($_GET['ip'])) {
			$ip = trim(htmlspecialchars($_GET['ip']));
		} // 获取客户端 IP 地址
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		// 判断是否符合 IP 格式
		if (preg_match('/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/', $ip)) {
			return $ip;
		} else {
			return '0.0.0.0';
		}

	}

}

/**
 * 判断是否内网 IP 地址
 * @return boolean
 */
if (!function_exists('is_taobao_ip')) {

	function is_taobao_ip () {

		// 配置内网 IP 段
		$blocks = array(
			'42.120.72' => '0,255',
			'42.120.73' => '0,255',
			'42.120.74' => '0,255',
			'42.120.75' => '0,255',
			'115.236.52' => '106,106',
			'121.0.29' => '0,255',
			'121.0.31' => '120,127',
			'182.92.247' => '0,15',
			'202.165.107' => '100,101',
			'220.181.68' => '208,211'
		);

		// 分割当前用户 IP 地址
		$bytes = explode('.', get_client_ip());
		$key = $bytes[0] . '.' . $bytes[1] . '.' . $bytes[2];
		$value = $bytes[3];

		// 判断最后一位是否在范围内
		if (array_key_exists($key, $blocks)) {
			$r = explode(',', $blocks[$key]);
			if ($value >= $r[0] && $value <= $r[1]) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

}
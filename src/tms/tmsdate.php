<?php

/**
 *  _                     _       _
 * | |                   | |     | |
 * | |_ _ __ ___  ___  __| | __ _| |_ ___
 * | __| '_ ` _ \/ __|/ _` |/ _` | __/ _ \
 * | |_| | | | | \__ \ (_| | (_| | ||  __/
 *  \__|_| |_| |_|___/\__,_|\__,_|\__\___|
 *
 * tms 日期控制函数集
 * @see http://www.taobao.com/go/rgn/market/docs/date.php
 * @version 0.9.1
 */

/* = 校正系统时区设置
----------------------------------------------- */
date_default_timezone_set('Asia/Shanghai');

/* = 获取当前系统时间戳
----------------------------------------------- */
$GLOBALS['_tms_time'] = time();

/* = 使用参数替换当前系统时间戳
----------------------------------------------- */
if (isset($_GET['date'])) {

	$pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}(\s[0-9]{2}:[0-9]{2}:[0-9]{2})?$/';
	$subject = trim(htmlspecialchars($_GET['date']));

	if (preg_match($pattern, $subject, $matches)) {
		$GLOBALS['_tms_time'] = strtotime($matches[0]);
	}

}

/**
 * 获取日期字符串
 * @param string $date 日期字符串
 * @return boolean
 */
if (!function_exists('get_format')) {

	function get_format ($date) {

		// 定义日期精度正则
		$r1 = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/';
		$r2 = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}$/';

		// 判断是否精确到天
		if (preg_match($r1, $date)) {
			return 'Y-m-d';
		}

		// 判断是否精确到秒
		if (preg_match($r2, $date)) {
			return 'Y-m-d H:i:s';
		}

	}

}

/**
 * 判断是否某个日期
 * @param string $date 日期字符串
 * @return boolean
 */
if (!function_exists('is_date')) {

	function is_date ($date) {

		$format = get_format($date);
		$current_date = date($format, $GLOBALS['_tms_time']);

		if (strtotime($current_date) == strtotime($date)) {
			return true;
		} else {
			return false;
		}

	}

}

/**
 * 判断是否某个日期之前
 * @param string $date 日期字符串
 * @return boolean
 */
if (!function_exists('is_before')) {

	function is_before ($date) {

		$format = get_format($date);
		$current_date = date($format, $GLOBALS['_tms_time']);

		if (strtotime($current_date) < strtotime($date)) {
			return true;
		} else {
			return false;
		}

	}

}

/**
 * 判断是否某个日期之后
 * @param string $date 日期字符串
 * @return boolean
 */
if (!function_exists('is_after')) {

	function is_after ($date) {

		$format = get_format($date);
		$current_date = date($format, $GLOBALS['_tms_time']);

		if (strtotime($current_date) > strtotime($date)) {
			return true;
		} else {
			return false;
		}

	}

}

/**
 * 判断是否在时间段内
 * @param string $start 开始日期字符串
 * @param string $end 结束日期字符串
 * @return boolean
 */
if (!function_exists('in_period')) {

	function in_period ($start, $end) {

		// 起始日期格式不同时直接返回
		if (get_format($start) != get_format($end)) {
			return false;
		}

		$format = get_format($start);
		$current_date = date($format, $GLOBALS['_tms_time']);

		if (strtotime($current_date) >= strtotime($start) && strtotime($current_date) <= strtotime($end)) {
			return true;
		} else {
			return false;
		}

	}

}
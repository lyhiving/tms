<?php

/**
 *  _                 _
 * | |_ _ __ ___  ___| |_ __ _  __ _
 * | __| '_ ` _ \/ __| __/ _` |/ _` |
 * | |_| | | | | \__ \ || (_| | (_| |
 *  \__|_| |_| |_|___/\__\__,_|\__, |
 *                             |___/
 * tms 标签解析函数集
 * @see http://wiki.ued.taobao.net/doku.php?id=tms:php:start
 * @version 0.9.1
 */

/* = 错误报告模式
----------------------------------------------- */
if (isset($_REQUEST['debug'])) {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

/* = 定义默认数据
----------------------------------------------- */
defined('_TMS_IMAGE') or define('_TMS_IMAGE', 'http://img.f2e.taobao.net/img.png');
defined('_TMS_LINK') or define('_TMS_LINK', 'http://www.taobao.com/');
defined('_TMS_TEXT') or define('_TMS_TEXT', 'string');

/* = 设置全局数据堆栈
----------------------------------------------- */
$GLOBALS['_tms_import'] = array();
$GLOBALS['_tms_export'] = array();

/* = 基础函数
----------------------------------------------- */
/**
 * 将字符串编码为 utf-8 格式
 * @param string $args
 * @return array
 * @private
 */
if (!function_exists('_tms_encode')) {

	function _tms_encode ($str) {

		if (!@mb_check_encoding($str, 'UTF-8')) {
			$str = @mb_convert_encoding($str, 'UTF-8', @mb_detect_encoding($str));
		}
		return $str;

	}

}

/**
 * 抛出标签错误信息
 * @param string $error_msg
 * @return string
 * @private
 */
if (!function_exists('_tms_error')) {

	function _tms_error ($error_msg) {

		try {
			$error = '<b>Fatal error: </b> ';
			throw new Exception();
		} catch (Exception $e) {
			$trace = $e->getTrace();
			$trace = array_pop($trace);
			$error .= str_replace('{function}', $trace['function'], $error_msg);
			$error .= 'in <b>' . $trace['file'] . '</b> ';
			$error .= 'on line <b>' . $trace['line'] . '</b><br>';
			exit($error);
		}

	}

}

/**
 * 检查标签参数语法
 * @param array $args
 * @param array $keys
 * @return string
 * @private
 */
if (!function_exists('_tms_syntax')) {

	function _tms_syntax ($args, $keys) {

		// 检查参数是否合法
		if (empty($args)) {
			_tms_error('Invalid argument in call to <b>{function}()</b> ');
		}

		// 检查参数是否缺少字段，或字段值错误
		foreach ($keys as $v) {
			if (empty($args[$v])) {
				_tms_error('Undefined index: <b>' . $v . '</b> ');
			}
		}

		// 校验通过
		return true;

	}

}

/**
 * 将 JSON 字符串转换为可读格式
 * @param string $json
 * @return string
 */
if (!function_exists('_tms_format')) {

	function _tms_format ($json) {

		$result = '';
		$pos = 0;
		$strLen = strlen($json);
		$indentStr = '	';
		$newLine = "\n";
		$prevChar = '';
		$outOfQuotes = true;

		for ($i = 0; $i <= $strLen; $i++) {
			$char = substr($json, $i, 1);
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;
			} elseif ($char == ':') {
				$char .= ' ';
			} elseif (($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos--;
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}
			$result .= $char;
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos++;
				}
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}
			$prevChar = $char;
		}
		return $result;
	}

}

/**
 * 标签输入输出控制器
 * @param string $filename
 * @param string $io
 * @return void
 */
if (!function_exists('_tms_io')) {

	function _tms_io ($filename, $io) {

		$data = @file_get_contents($filename);
		$data = _tms_encode($data);
		$data = json_decode($data, true);
		$data = $data ? $data : array();
		$GLOBALS['_tms_' . $io] = array_merge($GLOBALS['_tms_' . $io], $data);

	}

}

/**
 * 导入标签数据文件
 * @param string $filename
 * @return void
 */
if (!function_exists('_tms_import')) {

	function _tms_import ($filename) {

		// 从指定文件导入数据
		if (file_exists($filename)) {
			_tms_io($filename, 'import');
		} else {
			_tms_error('No such file <b>' . $filename . '</b> ');
		}

	}

}

/**
 * 导出标签数据文件
 * @param string $filename
 * @return void
 */
if (!function_exists('_tms_export')) {

	function _tms_export ($filename) {

		// 预读并保留原始数据
		if (file_exists($filename)) {
			_tms_io($filename, 'export');
		}

		// 导出数据到指定文件
		$data = json_encode($GLOBALS['_tms_export'], true);
		$replace = "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))";
		$data = preg_replace('#\\\u([0-9a-f]{4})#ie', $replace, $data);
		@file_put_contents($filename, _tms_format($data));
		$GLOBALS['_tms_export'] = array();

	}

}

/**
 * 将标签参数解析为数组
 * @param string $args
 * @return array
 * @private
 */
if (!function_exists('_tms_parse_args')) {

	function _tms_parse_args ($args, $keys) {

		$args = _tms_encode($args);
		$args = json_decode($args, true);

		// 标签语法校验
		if (_tms_syntax($args, $keys)) {
			return $args;
		}

	}

}

/**
 * 通用标签
 * @param string $args '{"key1":"value1","key2":"value2"}'
 * @param array $attributes
 * @return array
 * @private
 */
if (!function_exists('_tms_common')) {

	function _tms_common ($args = '{}', $attributes) {

		// 校验标签参数
		$required = array('name', 'title', 'group', 'row', 'defaultRow');
		$args = _tms_parse_args($args, $required);

		// 读取自定义数据
		$name = $args['name'];
		if (isset($GLOBALS['_tms_import'][$name])) {
			return $GLOBALS['_tms_import'][$name];
		}

		// 填充默认数据
		$defaults = array();
		for ($i = 0; $i < $args['defaultRow']; $i++) {
			$defaults[] = $attributes;
		}
		return $GLOBALS['_tms_export'][$name] = $defaults;

	}

}

/* = 数据标签
----------------------------------------------- */
/**
 * 单行文本标签
 * @param string $args 标签参数
 * @return array
 */
if (!function_exists('_tms_text')) {

	function _tms_text ($args) {

		$attributes = array(
			'text' => _TMS_TEXT
		);
		return _tms_common($args, $attributes);

	}

}

/**
 * 多行文本标签
 * @param string $args 标签参数
 * @return array
 */
if (!function_exists('_tms_textArea')) {

	function _tms_textArea ($args) {

		$attributes = array(
			'text' => _TMS_TEXT
		);
		return _tms_common($args, $attributes);

	}

}

/**
 * 文字链接标签
 * @param string $args 标签参数
 * @return array
 */
if (!function_exists('_tms_textLink')) {

	function _tms_textLink ($args) {

		$attributes = array(
			'text' => _TMS_TEXT,
			'href' => _TMS_LINK
		);
		return _tms_common($args, $attributes);

	}

}

/**
 * 图片标签
 * @param string $args 标签参数
 * @return array
 */
if (!function_exists('_tms_image')) {

	function _tms_image ($args) {

		$attributes = array(
			'text' => _TMS_TEXT,
			'img' => _TMS_IMAGE
		);
		return _tms_common($args, $attributes);

	}

}

/**
 * 图片链接标签
 * @param string $args 标签参数
 * @return array
 */
if (!function_exists('_tms_imageLink')) {

	function _tms_imageLink ($args) {

		$attributes = array(
			'text' => _TMS_TEXT,
			'img' => _TMS_IMAGE,
			'href' => _TMS_LINK
		);
		return _tms_common($args, $attributes);

	}

}

/**
 * 自定义标签
 * @param string $args 标签参数
 * @return array
 */
if (!function_exists('_tms_custom')) {

	function _tms_custom ($args) {

		// 校验标签参数
		$required = array('fields');
		$_args = _tms_parse_args($args, $required);

		// 解析自定义字段
		$fields = explode(',', $_args['fields']);
		$attributes = array();
		$sizes = array();
		foreach ($fields as $k => $v) {
			$field = explode(':', $v);
			$attributes[$field[0]] = $field[2];
			if (preg_match('/\[(\d+x\d+)]\:img$/', $v, $match)) {
				$sizes[$field[0]] = $match[1];
			}
		}

		// 根据字段类型取值
		foreach ($attributes as $k => &$v) {
			switch ($v) {
				case 'string':
				case 'multilString':
				case 'html':
					$v = _TMS_TEXT;
					break;
				case 'href':
					$v = _TMS_LINK;
					break;
				case 'img':
					$v = _TMS_IMAGE . ($sizes[$k] ? '?x=' . $sizes[$k] : '');
					break;
				case 'boolean':
					$v = 'true';
					break;
				case 'date':
					$v = date('YmdHis');
					break;
				case 'email':
					$v = 'youmame@example.com';
					break;
				default:
					$v = _TMS_TEXT;
			}
		}
		return _tms_common($args, $attributes);

	}

}

/**
 * 区块引用标签
 * @param string $args 标签参数
 * @return array
 */
if (!function_exists('_tms_subArea')) {

	function _tms_subArea ($args) {

		$args = str_replace('/home/admin/go', '', $args);
		$args = 'http://www.taobao.com/go' . $args;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $args);
		$data = curl_exec($ch);
		curl_close($ch);
		return eval(' ?>' . $data . '<?php ');

	}

}

/**
 * 资讯全自动抽取标签
 * @param string $args
 * @return array
 */
if (!function_exists('_tms_articleList')) {

	function _tms_articleList ($args) {

		$attributes = array(
			// 资讯编号
			'id' => '',
			// 标题1
			'title1' => _TMS_TEXT,
			// 标题2
			'title2' => _TMS_TEXT,
			// 标题3
			'title3' => _TMS_TEXT,
			// 作者序列号
			'authorId' => '',
			// 作者名
			'author' => '',
			// 作者链接
			'authorUrl' => _TMS_LINK,
			// 类目序列号
			'articleCatalogId' => '',
			// 类型
			'articleType' => '',
			// 标签
			'tag' => '',
			// 带链接的标签
			'tagLink' => '',
			// 权重
			'priority' => '',
			// 来源
			'source' => '',
			// 来源链接
			'sourceUrl' => _TMS_LINK,
			// 导语、摘要
			'articleAbstract' => '',
			// 正文
			'articleBody' => '',
			// 图1 1:1
			'image1' => _TMS_IMAGE,
			// 图2 250x165
			'image2' => _TMS_IMAGE . '?x=250x165',
			// 图3 190×150
			'image3' => _TMS_IMAGE . '?x=190x150',
			// 图4 110x90
			'image4' => _TMS_IMAGE . '?x=110x90',
			// 图5 110x70
			'image5' => _TMS_IMAGE . '?x=110×70',
			// 图6 165×120
			'image6' => _TMS_IMAGE . '?x=165×120',
			// 图7 260*230
			'image7' => _TMS_IMAGE . '?x=260*230',
			// 图8 115x155
			'image8' => _TMS_IMAGE . '?x=115x155',
			// 图9 165x150
			'image9' => _TMS_IMAGE . '?x=165x150',
			// 关联模板序列号
			'templateId' => '',
			// 链接
			'url' => _TMS_LINK,
			// 位置标签
			'positionTag' => '',
			// 文章正文存储路径
			'articlePath' => '',
			// 发布链接
			'publishedUrl' => _TMS_LINK,
			// 发布日期
			'publishDate' => ''
		);
		return _tms_common($args, $attributes);

	}

}

/**
 * 全自动抽取输入和输出标签
 * @params string $args 标签参数
 * @return array
 */
if (!function_exists('_tms_autoExtract')) {

	function _tms_autoExtract ($args) {

		// 本地全自动抽取标签是自定义标签的映射
		return _tms_custom($args);

	}

}

/* = 容器标签
----------------------------------------------- */

/**
 * 模块标签
 * @param string $args 标签参数
 * @return void
 */
if (!function_exists('_tms_module_begin')) {

	function _tms_module_begin ($args = '{}') {

		$required = array('name');
		_tms_parse_args($args, $required);

		// 线上模块标签会输出 spm 埋点容器
		echo '<div class="module">';

	}

}

if (!function_exists('_tms_module_end')) {

	function _tms_module_end () {

		echo '</div>';

	}

}

/**
 * 重复标签
 * 注：使用缓冲区解决片段重复的问题，不支持标签嵌套
 * @param string $args 标签参数
 * @return void
 */
if (!function_exists('_tms_repeat_begin')) {

	function _tms_repeat_begin ($args = '{}') {

		$required = array('name', 'title', 'group', 'row');
		$args = _tms_parse_args($args, $required);

		// 导出标签数据
		$row = $args['row'];
		$data = array();
		for ($i = 1; $i < $row; $i++) {
			$data[] = array(
				'text' => 'true'
			);
		}
		$GLOBALS['_tms_repeat_row'] = $row;
		$GLOBALS['_tms_export'][$args['name']] = $data;
		ob_start();

	}

}

if (!function_exists('_tms_repeat_end')) {

	function _tms_repeat_end () {

		$row = $GLOBALS['_tms_repeat_row'];
		$buffer = ob_get_contents();
		for ($i = 1; $i < $row; $i++) {
			echo $buffer;
		}
		unset($GLOBALS['_tms_repeat_row']);
		ob_end_flush();

	}

}
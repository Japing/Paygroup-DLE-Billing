<?php	if( ! defined( 'DATALIFEENGINE' ) ) die( "Hacking attempt!" );
/**
 * DLE Billing
 *
 * @link          https://github.com/mr-Evgen/dle-billing-module
 * @author        dle-billing.ru <evgeny.tc@gmail.com>
 * @copyright     Copyright (c) 2012-2017, mr_Evgen
 */

define( 'BILLING_MODULE', TRUE );
define( 'MODULE_PATH', ENGINE_DIR . "/modules/billing" );
define( 'MODULE_DATA', ENGINE_DIR . "/data/billing" );

$_Config = include MODULE_DATA . '/plugin.paygroups.php';
$_ConfigGroups = include MODULE_DATA . '/plugin.paygroups_list.php';
$_ConfigBilling = include MODULE_DATA . '/config.php';

require_once MODULE_PATH . "/plugins/paygroups/lang.php";
require_once MODULE_PATH . '/OutAPI.php';

$group_id = intval( $_GET['group_id'] );
$group_settings = $_ConfigGroups['group_' . $group_id];

$_TimePay = intval( $_GET['days'] );

if( ! $group_id )
{
	die();
}

if( ! $user_group[$group_id]['group_name'] )
{
	die( $plugin_lang['error_group'] );
}

$_TPL = @file_get_contents( ROOT_DIR . "/templates/" . $config['skin'] . "/billing/plugins/paygroup.tpl" ) or die( $plugin_lang['error_tpl'] );

$_Content = '';

# Требуется авторизация
#
if( ! $is_logged )
{
	$_Content = ThemePregMatch( $_TPL, 'need_login' );
}
# Плагин отключен
#
else if( ! $_Config['status'] or ! $group_settings['status'] or in_array($group_id , explode(",", $_Config['stop'] ) ) )
{
	$_Content = ThemePregMatch( $_TPL, 'plugin_off' );
}
# Доступ из группы запрещен
#
else if( ! in_array( $member_id['user_group'], explode(",", $group_settings['start']) ) )
{
	$_Content = ThemePregMatch( $_TPL, 'group_denied' );
}
# Уже в группе ( для единовременной оплаты )
#
else if( $member_id['user_group'] == $group_id and ! $group_settings['type'] )
{
	$_Content = ThemePregMatch( $_TPL, 'group_was_paid' );
}
# Процесс оплаты
#
else if( $_TimePay and $_GET['pay'] )
{
	# .. цена выбранной опции
	#
	$_Price = 0;

	if( $group_settings['type'] )
	{
		foreach( explode("\n", $group_settings['price']) as $price_str )
		{
			$price_ex = explode("|", $price_str );

			if( $price_ex[0] == $_TimePay )
			{
				$_Price = $price_ex[2];

				break;
			}
		}
	}
	# .. единоразовая оплата
	#
	else
	{
		$_Price = $group_settings['price'];
	}

	# .. ошибки
	#
	if( ! $_Price )
	{
		$_Content = ThemePregMatch( $_TPL, 'pay_error_time' );
	}
	else if( $member_id[$_ConfigBilling['fname']] < $BillingAPI->Convert( $_Price ) )
	{
		$_Content = ThemePregMatch( $_TPL, 'pay_error_balance' );
	}
	# .. оплата
	#
	else
	{
		$_Content = ThemePregMatch( $_TPL, 'pay_ok' );

		# .. время перехода
		#
		if( $group_settings['type'] )
		{
			$time_limit = $member_id['time_limit']
							? $member_id['time_limit'] + $_TimePay * 86400
							: $_TIME + $_TimePay * 86400;

			$_Content = str_replace( '{date}', langdate('d.m.Y G:i', $time_limit), $_Content );

			$_Content = str_replace( '[pay_time]', '', $_Content );
			$_Content = str_replace( '[/pay_time]', '', $_Content );

			$_Content = preg_replace("'\\[pay_one\\].*?\\[/pay_one\\]'si", '', $_Content);
		}
		else
		{
			$time_limit = '';

			$_Content = str_replace( '[pay_one]', '', $_Content );
			$_Content = str_replace( '[/pay_one]', '', $_Content );

			$_Content = preg_replace("'\\[pay_time\\].*?\\[/pay_time\\]'si", '', $_Content);
		}

		$BillingAPI->MinusMoney(
			$member_id['name'],
			$_Price,
			sprintf( $plugin_lang['log'], $user_group[$group_id]['group_name'] ) . ( $group_settings['type'] ? sprintf( $plugin_lang['time'], $_TimePay, langdate('d.m.Y G:i', $time_limit) ) : $plugin_lang['fulltime'] ),
			'paygroups',
			$group_id
		);

		$db->query( "UPDATE " . PREFIX . "_users
						SET user_group='$group_id', time_limit='$time_limit'
						WHERE name='$member_id[name]'" );
	}
}
# Форма оплаты
#
else
{
	$_Price = 0;

	# Повременная оплата
	#
	if( $group_settings['type'] )
	{
		$selects = '';
		$_tpl_select_buffer = '';
		$_tpl_select = ThemePregMatch( $_TPL, 'select' );

		foreach( explode("\n", $group_settings['price']) as $price_str )
		{
			$price_ex = explode("|", $price_str );

			if( ! $_Price )
			{
				$_Price = $price_ex[2];
			}

			$_tpl_select_buffer = $_tpl_select;

			$_tpl_select_buffer = str_replace('{days}', $price_ex[0], $_tpl_select_buffer);
			$_tpl_select_buffer = str_replace('{price}', $BillingAPI->Convert( $price_ex[2] ), $_tpl_select_buffer);
			$_tpl_select_buffer = str_replace('{currency}', $BillingAPI->Declension( $price_ex[2] ), $_tpl_select_buffer);
			$_tpl_select_buffer = str_replace('{title}', $price_ex[1], $_tpl_select_buffer);

			$selects .= $_tpl_select_buffer;
		}

		$_TPL = preg_replace("'\\[select\\].*?\\[/select\\]'si", $selects, $_TPL);

		$_TPL = str_replace( '[pay_time]', '', $_TPL );
		$_TPL = str_replace( '[/pay_time]', '', $_TPL );

		$_TPL = preg_replace("'\\[pay_one\\].*?\\[/pay_one\\]'si", '', $_TPL);
	}
	# .. единоразовая оплата
	#
	else
	{
		$_Price = $group_settings['price'];

		$_TPL = str_replace( '[pay_one]', '', $_TPL );
		$_TPL = str_replace( '[/pay_one]', '', $_TPL );

		$_TPL = preg_replace("'\\[pay_time\\].*?\\[/pay_time\\]'si", '', $_TPL);
	}

	$_TPL = str_replace( '{pay.sum}', $BillingAPI->Convert( $_Price ), $_TPL );
	$_TPL = str_replace( '{pay.sum.currency}', $BillingAPI->Declension( $_Price ), $_TPL );

	$_Content = ThemePregMatch( $_TPL, 'form' );
}


foreach( array('need_login', 'plugin_off', 'group_denied', 'group_was_paid', 'pay_error_time', 'pay_error_balance', 'pay_ok', 'form') as $tag )
{
	$_TPL = preg_replace("'\\[$tag\\].*?\\[/$tag\\]'si", '', $_TPL);
}

$_TPL = str_replace( '{content}', $_Content, $_TPL );
$_TPL = str_replace( '{module.skin}', $config['skin'], $_TPL );
$_TPL = str_replace( '{module.currency}', $_ConfigBilling['currency'], $_TPL );

$_TPL = str_replace( '{pay.group_name}', $user_group[$group_id]['group_name'], $_TPL );
$_TPL = str_replace( '{pay.group_id}', $group_id, $_TPL );

$_TPL = str_replace( '{user.group_name}', $user_group[$member_id['user_group']]['group_name'], $_TPL );
$_TPL = str_replace( '{user.balance}', $BillingAPI->Convert( $member_id[$_ConfigBilling['fname']] ), $_TPL );
$_TPL = str_replace( '{user.balance.currency}', $BillingAPI->Declension( $member_id[$_ConfigBilling['fname']] ), $_TPL );

unset($BillingAPI);

echo $_TPL;

function ThemePregMatch( $theme, $tag )
{
	$answer = array();

	preg_match('~\[' . $tag . '\](.*?)\[/' . $tag . '\]~is', $theme, $answer);

	return $answer[1];
}
?>

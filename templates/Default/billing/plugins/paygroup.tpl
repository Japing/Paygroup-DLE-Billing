<div id='paygrouptpl' title='Переход в группу {pay.group_name}' style='display:none'>

	<link media="screen" href="/templates/{module.skin}/billing/css/styles.css" type="text/css" rel="stylesheet" />

	[need_login]
		<p>Требуется авторизация</p>
	[/need_login]

	[plugin_off]
		<p>Смена группы временно недоступна</p>
	[/plugin_off]

	[group_denied]
		<p>Для вашей текущей группы невозможен переход в данную группу</p>
	[/group_denied]

	[group_was_paid]
		<p>Вы уже находитесь в данной группе</p>
	[/group_was_paid]

	[pay_error_time]
		<p>Невозможно оплатить указанное количество дней</p>
	[/pay_error_time]

	[pay_error_balance]
		<p>Недостаточно средств на балансе</p>
	[/pay_error_balance]

	[pay_ok]
		<p><b>Оплата выполнена</b><br>Вы перешли в группу {pay.group_name} [pay_time] до {date} [/pay_time] [pay_one][/pay_one]</p>
	[/pay_ok]

	[form]
		<table width="100%" class="billing-table">
			<tr>
				<td>Текущая группа:</td>
				<td>{user.group_name}</td>
			</tr>
			[pay_time]
				<tr>
					<td>Время перехода:</td>
					<td>
						<select id="BillingGroupDays" onchange="BillingGroup.Days()" style="width: 140px">
						[select]
							<option value="{days}" data-price="{price}" data-currency="{currency}">{title}</option>
						[/select]
					</select>
					</td>
				</tr>
			[/pay_time]
			[pay_one]
				<tr>
					<td>Время перехода: </td>
					<td>навсегда<input type="hidden" id="BillingGroupDays" data-price="{pay.sum}" data-currency="{pay.sum.currency}" value="1"></td>
				</tr>
			[/pay_one]
			<tr>
				<td>Ваш баланс:</td>
				<td>{user.balance} {user.balance.currency}</td>
			</tr>
			<tr>
				<td>К оплате:</td>
				<td><span id="BillingGroupBalancePay"></span><td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center" id="BillingGroupBalance">
					Баланс после оплаты: <span id="BillingGroupBalanceSum"></span>
				</td>
			</tr>
		</table>

		<input type="hidden" id="BillingGroupMyBalance" value="{user.balance}">
		<input type="hidden" id="BillingGroupCurrency" value="{module.currency}">
		
		<div style="text-align: center; padding-top: 10px">
			<span id="BillingGroupBtn">
				<button type="submit" class="btn" onClick="BillingGroup.Pay()">
					<span>Перейти в группу</span>
				</button>
			</span>
			
			<span id="BillingGroupBtnPay">
				<button type="submit" class="btn" id="BillingGroupBtnPayClick">
					<span>Пополнить баланс на <span id="BillingGroupNeedPay"></span></span>
				</button>
			</span>
		</div>
	[/form]

	{content}
</div>

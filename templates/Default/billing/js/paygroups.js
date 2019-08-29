/*
=====================================================
 Billing
-----------------------------------------------------
 evgeny.tc@gmail.com
-----------------------------------------------------
 This code is copyrighted
=====================================================
*/

function BillingGroup()
{
	this.group_id = 0;
	this.days = 0;

	this.Form = function( group_id, pay = 0 )
	{
		$("#paygrouptpl").remove();

		this.group_id = group_id;

		ShowLoading('');

		$.get("/engine/ajax/BillingAjax.php", { plugin: "paygroups", group_id: group_id, days: this.days, pay: pay }, function(data)
		{
			HideLoading('');

			BillingGroup.ShowModal( data );
			BillingGroup.Days();
		});
	};

	this.Pay = function()
	{
		BillingGroup.Form( this.group_id, 1 );
	}

	this.Days = function()
	{
		var change = $("#BillingGroupDays option:selected");

		if( ! change.attr('data-price') )
		{
			change = $("#BillingGroupDays");
		}

		var balance = parseFloat($("#BillingGroupMyBalance").val());
		var price = parseFloat(change.attr('data-price'));

		this.days = change.val();

		$("#BillingGroupBalancePay").html( price + ' ' + change.attr('data-currency'));

		if( price > balance )
		{
			$("#BillingGroupBalanceSum").html( ( balance - price ).toFixed(2) + ' ' + BillingGroup.Declension( parseInt( price - balance ), $("#BillingGroupCurrency").val() ) );
			$("#BillingGroupBtnPayClick").attr("onClick", "window.location.href = '/billing.html/pay/main/sum/" + ( price - balance ).toFixed(2) + "'");
			$("#BillingGroupNeedPay").html( ( price - balance ).toFixed(2) + ' ' + BillingGroup.Declension( parseInt( price - balance ), $("#BillingGroupCurrency").val() ) );

			$("#BillingGroupBtn").hide();
			$("#BillingGroupBtnPay").show();
			$("#BillingGroupBalance").css('color', 'red');
		}
		else
		{
			$("#BillingGroupBalanceSum").html( ( balance - price ).toFixed(2) + ' ' + BillingGroup.Declension( parseInt( balance - price ), $("#BillingGroupCurrency").val() ) );

			$("#BillingGroupBtnPay").hide();
			$("#BillingGroupBtn").show();
			$("#BillingGroupBalance").css('color', 'green');
		}
	}

	this.ShowModal = function( modal )
	{
		$("body").append(modal);

		$("#paygrouptpl").dialog(
		{
			autoOpen: true,
			show: 'fade',
			hide: 'fade',
			resizable: false,
			width: 400,
			position: 'middle'
		});

		$("#paygrouptpl").dialog( "option", "position", ['0','0'] );
	}

	this.Declension = function( number, currency )
	{
		currency = currency.split(',');
		cases = [2, 0, 1, 1, 1, 2];

		return ' ' + currency[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
	}
}

var BillingGroup = new BillingGroup();

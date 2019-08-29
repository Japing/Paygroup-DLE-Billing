<?php	if( !defined( 'BILLING_MODULE' ) ) die( "Hacking attempt!" );
/*
=====================================================
 Billing
-----------------------------------------------------
 evgeny.tc@gmail.com
-----------------------------------------------------
 This code is copyrighted
=====================================================
*/

$this->LQuery->DbWhere( array(
	"history_plugin = 'paygroups' " => 1,
	"history_date > " . mktime(0,0,0) => 1
));

return $this->TopInformerView(
	"?mod=billing&c=transactions",
	$this->lang['main_news'],
	$this->LQuery->DbGetHistoryNum(),
	"Сменили группу",
	"icon-user"
);
?>

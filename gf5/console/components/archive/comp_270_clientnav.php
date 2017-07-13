<style type="text/css">
#topNav li{
	list-style:none;
	float:left;
	padding:5px 10px;
	}
#topNav li:hover, #topNav li.active{
	background-color:aliceblue;
	}
#topNav li{
	border-bottom:1px solid #000;
	cursor:pointer;
	}
#topNav li.active{
	border:1px solid #000;
	border-bottom:none;
	cursor:auto;
	}
#topNav li{
	font-size:109%;
	/*
	font-weight:900;
	letter-spacing:0.02em;
	*/
	}
#topNav li a{
	text-decoration:none;
	color:#000;
	}
</style>
<ul id="topNav">
	<li <?php echo !$section || $section=='InvoicesProperties'?'class="active"':''?>><a href="home.php">Invoices &amp; Properties</a></li>
	<li <?php echo $section=='MyInfo'?'class="active"':''?>><a href="home.php?section=MyInfo">My Info</a></li>
	<li <?php echo $section=='Tools'?'class="active"':''?>><a href="home.php?section=Tools">Tools</a></li>
	<li <?php echo $section=='PaymentHistory'?'class="active"':''?>><a href="home.php?section=PaymentHistory">Pymt. History</a></li>
	<?php if($apSettings['clientPaymentGatewayActive']){ ?>
	<li <?php echo $section=='OnlinePayment'?'class="active"':''?>><a href="home.php?section=OnlinePayment">Make a Payment</a></li>
	<?php } ?>
</ul>
<div class="cb"> </div>
<?php require_once('Connections/newsletter.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

// Make unified connection variable
$conn_newsletter = new KT_connection($newsletter, $database_newsletter);

// Start trigger
$formValidation = new tNG_FormValidation();
$formValidation->addField("email", true, "text", "email", "", "", "");
$tNGs->prepareValidation($formValidation);
// End trigger

// Make an insert transaction instance
$ins_emails = new tNG_insert($conn_newsletter);
$tNGs->addTransaction($ins_emails);
// Register triggers
$ins_emails->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_emails->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_emails->registerTrigger("END", "Trigger_Default_Redirect", 99, "thanks.php");
// Add columns
$ins_emails->setTable("emails");
$ins_emails->addColumn("email", "STRING_TYPE", "POST", "email");
$ins_emails->setPrimaryKey("id", "NUMERIC_TYPE");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsemails = $tNGs->getRecordset("emails");
$row_rsemails = mysql_fetch_assoc($rsemails);
$totalRows_rsemails = mysql_num_rows($rsemails);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>s3mer - Digital Signage for Humans</title>
<link href="http://yui.yahooapis.com/2.5.0/build/reset-fonts-grids/reset-fonts-grids.css" rel="stylesheet" type="text/css" />
<link href="styles/s3merbeta.css" rel="stylesheet" type="text/css" />
<link href="includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<script src="includes/common/js/base.js" type="text/javascript"></script>
<script src="includes/common/js/utility.js" type="text/javascript"></script>
<script src="includes/skins/style.js" type="text/javascript"></script>
<?php echo $tNGs->displayValidationRules();?>
</head>

<body>
<div class="container">
	<div class="header"><img src="images/s3mer.gif" alt="s3mer" width="201" height="111" /></div>
    <div class="content">
		<table width="595" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="3"><div align="center"><img src="images/sample_show.jpg" alt="Sample Show" width="502" height="377" class="sampleshow"/></div></td>
    </tr>
  <tr>
    <td colspan="3"><p class="bigtext blue">Digital Signage for Humans</p>
      <p class="bigtext grey">Finally! Digital signage software that is easy to use, powerful and free.</p></td>
    </tr>
  <tr>
    <td width="302"><div class="subtitles">Best of it's kind</div></td>
    <td width="5">&nbsp;</td>
    <td width="288"><div class="subtitles">Features</div></td>
  </tr>
  <tr>
    <td valign="top"><div class="texto">
      <p>We have developed a modern web application wich will make setting a digital signage network as easy as creating a blog.</p>
      <p>&nbsp;</p>
      <p>Anyone can do it.</p>
    </div></td>
    <td valign="top">&nbsp;</td>
    <td valign="top"><div class="texto">
    	<ul>
        	<li>Live video support</li>
            <li>Multiple shows with one player</li>
    	    <li>MPEG-4, MOV, FLV, SWF, JPG, PNG</li>
    	    <li>RSS &amp; Podcast Integrations</li>
    	    <li>Easy to use web interface</li>
    	</ul>
    </div></td>
  </tr>
</table>
  </div>
    <div class="footer">
    	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="33" colspan="3">&nbsp;</td>
    </tr>
  <tr>
    <td width="51%" valign="top"><p><strong>Be the first to try s3mer, subscribe to our newsletter</strong></p>      </td>
    <td width="1%" valign="top">&nbsp;</td>
    <td width="48%" rowspan="2" valign="top"><strong>If you have specific questions about s3mer or if you are a journalist please contact us at <a href="mailto:private.beta@s3mer.com">private.beta@s3mer.com</a></strong></td>
  </tr>
  <tr>
    <td><form id="form1" name="form1" method="post" action="">
    </form>    
      <?php
	echo $tNGs->getErrorMsg();
?>
      <form method="post" id="form2" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>">
<input type="text" name="email" id="email" value="<?php echo KT_escapeAttribute($row_rsemails['email']); ?>" size="30" />
<?php echo $tNGs->displayFieldError("emails", "email"); ?>
<input type="submit" name="KT_Insert1" id="KT_Insert1" value="Subscribe" />

      </form>
      <p>&nbsp;</p></td>
    <td>&nbsp;</td>
    </tr>
</table>
  </div>
</div>
</body>
</html>

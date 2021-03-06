<?php

$plugin['name'] = 'sed_default_article_status';
$plugin['version'] = '0.4.1';
$plugin['author'] = 'Stef Dawson, Destry Wion & Netcarver';
$plugin['author_uri'] = 'http://forum.textpattern.com/viewtopic.php?pid=249855#p249855';
$plugin['description'] = 'Makes the Draft article status default.';
$plugin['order'] = '5';
$plugin['type'] = '3';
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001);
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002);
$plugin['flags'] = '2';

if (!defined('txpinterface')) @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---

if (@txpinterface=='admin') 
{
	defined('sed_das_prefix') || define( 'sed_das_prefix' , 'sed_das' );

	global $textarray;
	$textarray[sed_das_prefix.'_default_status'] = 'Default article status';

	register_callback('sed_das_article', 'article_ui', 'status');
	register_callback('sed_das_install', 'plugin_lifecycle.sed_default_article_status', 'installed' );
	register_callback('sed_das_enable',  'plugin_lifecycle.sed_default_article_status', 'enabled' );
	register_callback('sed_das_disable', 'plugin_lifecycle.sed_default_article_status', 'disabled' );
	register_callback('sed_das_delete',  'plugin_lifecycle.sed_default_article_status', 'deleted'   );

	function sed_das_status_list($name, $val)
	{
		$vals = array(
			'1' => gTxt('draft'),
			'2' => gTxt('hidden'),
			'3' => gTxt('pending'),
			'4' => gTxt('live'),
			'5' => gTxt('sticky'),
			);

		return selectInput($name, $vals, $val, '', '', $name);
	}


	function sed_das_install($evt, $stp='')
	{
		$status = get_pref( sed_das_prefix.'_default_status', '4' );
		set_pref( sed_das_prefix.'_default_status' , $status , 'publish' , 10 , 'sed_das_status_list' );
	}


	function sed_das_enable ($evt, $stp='')
	{
		$name = sed_das_prefix.'_default_status';
		safe_update( 'txp_prefs', "`type`='1'", "`name`='$name'", 0 );
	}


	function sed_das_disable ($evt, $stp='')
	{
		$name = sed_das_prefix.'_default_status';
		safe_update( 'txp_prefs', "`type`='10'", "`name`='$name'", 0 );
	}


	function sed_das_delete ($evt, $stp='')
	{
		safe_delete( 'txp_prefs' , "`name`='".sed_das_prefix."_default_status'" );
	}

	function sed_das_article($evt, $stp, $data, $rs) 
	{
		global $step;

		$js = '';

		if (in_array($step, array('', 'create'))) 
		{
			$level = get_pref( sed_das_prefix.'_default_status', '4' );
			$js = <<<EOJS
<script type="text/javascript">
jQuery(function() {
	jQuery('input[id="status-$level"]').prop('checked', true);
});
</script>
EOJS;
		}

		return $data.$js;
	}
}

# --- END PLUGIN CODE ---

/*
# --- BEGIN PLUGIN CSS ---
	<style type="text/css">
	div#sed_default_article_status td { vertical-align:top; }
	div#sed_default_article_status code { font-weight:bold; font: 105%/130% "Courier New", courier, monospace; background-color: #FFFFCC;}
	div#sed_default_article_status code.sed_code_tag { font-weight:normal; border:1px dotted #999; background-color: #f0e68c; display:block; margin:10px 10px 20px; padding:10px; }
	div#sed_default_article_status a:link, div#sed_default_article_status a:visited { color: blue; text-decoration: none; border-bottom: 1px solid blue; padding-bottom:1px;}
	div#sed_default_article_status a:hover, div#sed_default_article_status a:active { color: blue; text-decoration: none; border-bottom: 2px solid blue; padding-bottom:1px;}
	div#sed_default_article_status h1 { color: #369; font: 20px Georgia, sans-serif; margin: 0; text-align: center; }
	div#sed_default_article_status h2 { border-bottom: 1px solid black; padding:10px 0 0; color: #369; font: 17px Georgia, sans-serif; }
	div#sed_default_article_status h3 { color: #693; font: bold 12px Arial, sans-serif; letter-spacing: 1px; margin: 10px 0 0;text-transform: uppercase;}
	div#sed_default_article_status ul ul { font-size:85%; }
	div#sed_default_article_status h3 { color: #693; font: bold 12px Arial, sans-serif; letter-spacing: 1px; margin: 10px 0 0;text-transform: uppercase;}
	</style>
# --- END PLUGIN CSS ---
	
# --- BEGIN PLUGIN HELP ---

<div id="sed_default_article_status">

Once you have enabled the plugin just go to @Admin > Prefs > Advanced > Publish@ and choose the default status for articles.
Save the preferences and from then on, your articles will have that status by default.

</div>

# --- END PLUGIN HELP ---

*/

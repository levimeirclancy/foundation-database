<? function html_header($title=null) {
	//
	}


function amp_header($title=null, $canonical=null) {
	global $domain;
	global $publisher;
	global $google_analytics_code;
	global $color;
	global $page_temp;
	global $slug_temp;
	global $command_temp;
	global $header_array;
	global $information_array;
	global $login;	
	
	if (empty($title)): $title = $domain; endif;

	// https://www.ampproject.org/docs/tutorials/create/basic_markup

	// these must open the document
	echo "<!doctype html>" . "<html amp lang='en'>";

	// open html head
	echo "<head>" . "<meta charset='utf-8'>";

	// for google analytics, this must precede amp js
	if (!(empty($google_analytics_code))):
		echo '<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>';
		endif;

	// amp js
	echo "<script async src='https://cdn.ampproject.org/v0.js'></script>";

	if (empty($canonical)): $canonical=$domain; endif; // do some sort of url validation here
	echo "<link rel='canonical' href='https://$canonical'>"; // must define canonical url for amp

	// amp boilerplate code https://www.ampproject.org/docs/reference/spec/amp-boilerplate
	echo "<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>";

	// for amp-form
	echo '<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>';

	// for amp-bind
	echo '<script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>';
		
	// for lightbox search feature
	echo '<script async custom-element="amp-lightbox" src="https://cdn.ampproject.org/v0/amp-lightbox-0.1.js"></script>';

	// for youtube
	echo '<script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>';
	
	// for fitting text
	echo '<script async custom-element="amp-fit-text" src="https://cdn.ampproject.org/v0/amp-fit-text-0.1.js"></script>';	

	// for the parallax
	echo '<script async custom-element="amp-fx-collection" src="https://cdn.ampproject.org/v0/amp-fx-collection-0.1.js"></script>';
	
	// for the amp-selector
	echo '<script async custom-element="amp-selector" src="https://cdn.ampproject.org/v0/amp-selector-0.1.js"></script>';
	
	// font
	echo '<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">';
	
	echo "<title>" . $title . "</title>";

	echo '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">';
	
//	echo "<base href='/' />";
	echo "<meta name='viewport' content='width=device-width,minimum-scale=1,initial-scale=1'>"; // must define viewport for amp

	echo "<style amp-custom>";
	include_once('style.css');
	echo "</style>";

	echo "</head><body>";
	
	if (!(empty($google_analytics_code))):
		echo '<amp-analytics type="googleanalytics">';
		echo '<script type="application/json">';
		$google_analytics_array = [
			"vars" => ["account"=>$google_analytics_code],
			"triggers" => ["trackPageview" => ["on"=>"visible", "request"=>"pageview"] ] ];
		echo json_encode($google_analytics_array);
		echo '</script></amp-analytics>';
		endif;
	
	// These are the actions like log in, log out, etc plus the home button
	echo "<div id='navigation-header'>";

	// First of all, the home button
	echo "<a href='/'><span id='navigation-header-home'>".$domain."</span></a>";

	// Do not show the index if we are editing an article or our account
	if (!(array_intersect( [$page_temp, $command_temp], ["edit", "account"] ))):
		echo "<div class='navigation-header-item'>";
		echo "<span class='navigation-header-item-title'>Index</span>";
		echo "<div class='navigation-header-item-dropdown'>";
		foreach ($header_array as $header_backend => $header_frontend):
		$selected_temp = null; if ($header_backend == $page_temp): $selected_temp = "selected"; endif;
		echo "<a href='/". $header_backend ."'><div class='navigation-header-item-dropdown-option'>". $header_frontend ."</div></a>";
		endforeach;
		echo "</div></div>";
		endif;

	// The search popover
	echo "<div class='navigation-header-item'>";
	echo "<span role='button' tabindex='0' on='tap:search-popover' class='navigation-header-item-title'>Search</span>";
	echo "</div>";

	// If we are not signed in ...
	if (empty($login)):
		
		echo "<div class='navigation-header-item'>";
		echo "<span role='button' tabindex='0' on='tap:login-popover' class='navigation-header-item-title'>Log in</span>";
			echo "</div>";

		// this is the login popover
		echo "<amp-lightbox id='login-popover' layout='nodisplay'>"; ?>
		<button on='tap:login-popover.close'>Close</button>
		<form id='login' method='post' action-xhr='/?action=login-xhr' on='submit:submit-login-form.hide;submit-error:submit-login-form.show'>
		<input type='email' name='checkpoint_email' placeholder='email'>
		<input type='password' name='checkpoint_password' placeholder='password'>
		<span class='form-submit-button' id='submit-login-form' role='button' tabindex='0' on='tap:login.submit'>Log in</span>
		<div class='form-warning'>
			<div submitting>Submitting...</div>
			<div submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>
			<div submit-success><template type='amp-mustache'>{{{message}}}</template></div>
			</div>
		</form>
		<? echo "</amp-lightbox>";
	
	// If we are signed in ...
	elseif (!(empty($login))):
	
		// Account options
		echo "<div class='navigation-header-item'>";
		echo "<span class='navigation-header-item-title'>". $publisher ."</span>";
		echo "<div class='navigation-header-item-dropdown'>";
		echo "<a href='/account/'><div class='navigation-header-item-dropdown-option'>My account</div></a>";
		echo "<a href='/logout/'><div class='navigation-header-item-dropdown-option'>Log out</div></a>";
		echo "</div></div>";

		// Create new article
		echo "<a href='/new/' target='_blank'><span class='navigation-action-button'>New article</span></a>";
	
		// Edit existing article, if we are on an article and not already in edit mode
		if (!(empty($page_temp)) && !(empty($information_array[$page_temp])) && ($command_temp !== "edit")):
			echo "<a href='/".$page_temp."/edit/' target='_blank'><span class='navigation-action-button'>Edit</span></a>";
			endif;
	
		endif;

	// Close out the navigation-header
	echo "</div>";

	}


function json_result($domain, $result, $redirect, $message) {
	
	header("Content-type: application/json");
	header("Access-Control-Allow-Credentials: true");
	header("Access-Control-Allow-Origin: https://".$domain);
	header("AMP-Access-Control-Allow-Source-Origin: https://".$domain);
	
	// Immediately handle any error message, with no redirect
	if ($result !== "success"):
//		header("HTTP/1.0 412 Precondition Failed", true, 412);
		echo json_encode(["result"=>"error", "message"=>$message]);
		endif;

	if (empty($redirect)):
		header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");
		endif;

	if (!(empty($redirect))):	
		header("AMP-Redirect-To: https://".$domain."/".$redirect);
		header("Access-Control-Expose-Headers: AMP-Redirect-To, AMP-Access-Control-Allow-Source-Origin");
			endif;

	echo json_encode(["result"=>"success", "message"=>$message]);

	exit; }


function generate_messenger_code ($entry_id) {
	global $page_access_token;
	if (empty($entry_id)): return null; endif;
	unlink("messenger/".$entry_id.".png");
	$postdata = [
		"type"		=> "standard",
		"image_size"	=> 1000,
		"data"		=> ["ref"=>"entry_id=".$entry_id]
		];
	$opts = ["http" => ["method"=>"POST", "header"=>"Content-type: application/json", "content"=>http_build_query($postdata)]];
	$context = stream_context_create($opts);
	$result = file_get_contents("https://graph.facebook.com/v3.0/me/messenger_codes?access_token=".$page_access_token, false, $context);
	$json_decoded = json_decode($result, true);
	if (empty($json_decoded['uri'])): return null; endif;
	$photo = imagecreatefrompng($json_decoded['uri']);
	imagepng($photo, "messenger/".$entry_id.".png");
//	copy ($json_decoded['uri'], "messenger/".$entry_id.".png");
	}


function notfound() {
	echo "404ed";
	footer(); }


function footer() {
	echo "</body></html>";
	exit; } ?>

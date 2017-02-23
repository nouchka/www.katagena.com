<?php
/*
 * !
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */

// ----------------------------------------------------------------------------------------
// HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------
if ($_SERVER ["HTTP_HOST"] == "localhost") {
	$url = "http://localhost/Setagaya/library/index.php";
} else {
	$url = "http://www.katagena.com/setagaya/library/index.php";
}
return array (
		"base_url" => $url,
		
		"providers" => array (
				// openid providers
				"OpenID" => array (
						"enabled" => true 
				),
				
				"Yahoo" => array (
						"enabled" => true,
						"keys" => array (
								"id" => "",
								"secret" => "" 
						) 
				),
				
				"AOL" => array (
						"enabled" => true 
				),
				
				"Google" => array (
						"enabled" => true,
						"keys" => array (
								"id" => "",
								"secret" => "" 
						) 
				),
				
				"Facebook" => array (
						"enabled" => true,
						"keys" => array (
								"id" => "",
								"secret" => "" 
						) 
				),
				
				"Twitter" => array (
						"enabled" => true,
						"keys" => array (
								"key" => getenv ( "TWITTER_KEY" ),
								"secret" => getenv ( "TWITTER_SECRET" ) 
						) 
				),
				
				// windows live
				"Live" => array (
						"enabled" => true,
						"keys" => array (
								"id" => "",
								"secret" => "" 
						) 
				),
				
				"MySpace" => array (
						"enabled" => true,
						"keys" => array (
								"key" => "",
								"secret" => "" 
						) 
				),
				
				"LinkedIn" => array (
						"enabled" => true,
						"keys" => array (
								"key" => "",
								"secret" => "" 
						) 
				),
				
				"Foursquare" => array (
						"enabled" => true,
						"keys" => array (
								"id" => "",
								"secret" => "" 
						) 
				) 
		),
		
		// if you want to enable logging, set 'debug_mode' to true then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,
		
		"debug_file" => "" 
);
?>
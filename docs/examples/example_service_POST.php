<?php
// Example for proxied service with session support

// Load the settings from the central config file
include_once('config.php');
// Load the CAS lib
include_once($phpcas_path.'/CAS.php');

// Uncomment to enable debugging
phpCAS::setDebug();

// Initialize phpCAS
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

// For production use set the CA certificate that is the issuer of the cert 
// on the CAS server and uncomment the line below
// phpCAS::setCasServerCACert($cas_server_ca_cert_path);

// For quick testing you can disable SSL validation of the CAS server. 
// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION. 
// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL! 
phpCAS::setNoCasServerValidation();


// If you want your service to be proxied you have to enable it (default
// disabled) and define an accepable list of proxies that are allowed to 
// proxy your service. 
// 
// Add each allowed proxy definition object. For the normal CAS_ProxyChain
// class, the constructor takes an array of proxies to match. The list is in 
// reverse just as seen from the service. Proxies have to be defined in reverse 
// from the service to the user. If a user hits service A and gets proxied via
// B to service C the list of acceptable on C would be array(B,A). The definition
// of an individual proxy can be either a string or a regexp (preg_match is used)
// that will be matched against the proxy list supplied by the cas server 
// when validating the proxy tickets. The strings are compared starting from 
// the beginning and must fully match with the proxies in the list.
// Example:
// 		phpCAS::allowProxyingBy(new CAS_ProxyChain(array(
// 				'https://app.example.com/'
// 			)));
// 		phpCAS::allowProxyingBy(new CAS_ProxyChain(array(
// 				'/^https:\/\/app[0-9]\.example\.com\/rest\//',
// 				'http://client.example.com/'
// 			)));
phpCAS::allowProxyingBy(new CAS_ProxyChain(array($pgtUrlRegexp)));

// For quick testing or in certain production screnarios you might want to 
// allow allow any other valid service to proxy your service. To do so, add
// the "Any" chain: 
// 		phpcas::allowProxyingBy(new CAS_ProxyChain_Any);
// THIS SETTING IS HOWEVER NOT RECOMMENDED FOR PRODUCTION AND HAS SECURITY 
// IMPLICATIONS: YOU ARE ALLOWING ANY SERVICE TO ACT ON BEHALF OF A USER
// ON THIS SERVICE.
//phpcas::allowProxyingBy(new CAS_ProxyChain_Any);


// force CAS authentication
phpCAS::forceAuthentication();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('HTTP/1.1 400 Bad Request');
	print "<h1>I only respond to POST requests. This is a ".$_SERVER['REQUEST_METHOD']." request.</h1>";
	exit;
}
if (empty($_POST['favorite_color'])) {
	header('HTTP/1.1 400 Bad Request');
	print '<h1>You must post a <strong>favorite_color</strong>.</h1>';
	exit;
}

print '<h1>I am a service that responds to POST requests.</h1>';

// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().
include 'script_info.php';

// for this test, simply print that the authentication was successfull
echo '<p>The user\'s login is <b>'.phpCAS::getUser().'</b>.</p>';

print '<h1>Your favorite color is '.htmlentities($_POST['favorite_color']).'</h1>';

// increment the number of requests of the session and print it
if (!isset($_SESSION['n']))
	$_SESSION['n'] = 0;
echo '<p>request #'.(++$_SESSION['n']).'</p>';


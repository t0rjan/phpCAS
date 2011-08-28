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

// If you want your service to be proxied you have to enable it (default disabled)
// and define an accepable list of proxies that are allowed to proxy your 
// service. You have to define proxies as an array() of acceptable proxies. The 
// definition can be either a string or a regexp(preg_match is used) that will 
// be matched against the proxy list  supplied by the cas server when validatin 
// proxy // tickets. The strings are compared starting from the beginning and 
// must fully  match with the proxies in the list. 
phpCAS::allowToBeProxied(true,array('/^https:\/\/myservice\.com\/.*$/','https://myservice.com',$pgtUrl));

// For quick testing or in certain production screnarios you might want to allow
// allow any other valid service to proxy your service.  
// THIS SETTING IS HOWEVER NOT RECOMMENDED FOR PRODUCTION AND HAS SECURITY 
// IMPLICATIONS: YOU ARE ALLOWING ANY SERVICE TO ACT ON BEHALF OF A USER
// ON THIS SERVICE.
//phpCAS::allowToBeProxied(true);

// force CAS authentication
phpCAS::forceAuthentication();

print '<h1>I am a service that can be proxied.</h1>';

// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().
include 'script_info.php';

// for this test, simply print that the authentication was successfull
echo '<p>The user\'s login is <b>'.phpCAS::getUser().'</b>.</p>';

// increment the number of requests of the session and print it
if (!isset($_SESSION['n']))
	$_SESSION['n'] = 0;
echo '<p>request #'.(++$_SESSION['n']).'</p>';

?>

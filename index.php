<?php

// Allow using session variables
session_start();

// A pseudo autoloader
include_once "system/autoload.php";


// Routes used by the site
define ("PAGE_CONVERTOR", "/");          // Actual currency converter
define ("PAGE_START_ALL", "/index.php"); // Real start file used for every page
define ("PAGE_LOGIN",     "/login");     // Login page
define ("PAGE_LOGOUT",    "/logout");    // Logout page
define ("AJAX_GETRATE",   "/getrate");   // Endpoint for ajax call to get a rate
define ("AJAX_GETGRAPH",  "/getgraph");  // Endpoint for ajax call to get graph values


// Get path that was called
$requestPage = $_SERVER['REQUEST_URI'];
// Remove possible query part
$pos = strpos($requestPage,'?');
$requestPage = $pos===false ? $requestPage : substr($requestPage,0,$pos);


// Go to the route's controller
switch ($requestPage) {
    case PAGE_START_ALL:
    case PAGE_CONVERTOR:
        $convertor = new Convertor();
        $convertor->start();
        break;
    case AJAX_GETRATE:
        $convertor = new Convertor();
        $convertor->rate();
        break;
    case AJAX_GETGRAPH:
        $convertor = new Convertor();
        $convertor->graph();
        break;
    case PAGE_LOGIN:
        $convertLogin = new ConvertLogin();
        $convertLogin->request();
        break;
    case PAGE_LOGOUT:
        $convertLogin = new Convertlogin();
        $convertLogin->logout();
        break;
    default:
        header("HTTP/1.0 404 Not Found");
        echo '<h1>Error 404: Page not found</h1>';
        echo '<p><a href="/">Click here to continue</a></p>';
}

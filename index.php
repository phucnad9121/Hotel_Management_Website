<?php
/**
 * Main entry point for both:
 * - legacy MVC web app
 * - RESTful API (/api/*)
 */

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH) ?: '/';

$apiCandidates = [$path, (string) $requestUri];
foreach (['url', 'route', 'q'] as $key) {
    if (isset($_GET[$key]) && is_string($_GET[$key])) {
        $apiCandidates[] = '/' . ltrim($_GET[$key], '/');
    }
}
if (isset($_SERVER['PATH_INFO']) && is_string($_SERVER['PATH_INFO'])) {
    $apiCandidates[] = $_SERVER['PATH_INFO'];
}

$isApiRequest = false;
foreach ($apiCandidates as $candidate) {
    if (preg_match('#(^|/|=)api(?:/|$)#i', (string) $candidate)) {
        $isApiRequest = true;
        break;
    }
}

if ($isApiRequest) {
    require_once './MVC/Core/connectDB.php';
    require_once './MVC/Core/Request.php';
    require_once './MVC/Core/Response.php';
    require_once './MVC/Core/JWT.php';
    require_once './MVC/Core/ApiModel.php';
    require_once './MVC/Core/ApiController.php';
    require_once './MVC/Core/ApiRouter.php';

    $request = new Request();
    $response = new Response();

    $GLOBALS['api_request'] = $request;
    $GLOBALS['api_response'] = $response;

    $router = new ApiRouter($request, $response);
    require_once './MVC/Core/routes.php';
    $router->run();
    $response->send();
    exit();
}

require_once './MVC/Core/App.php';
require_once './MVC/Core/controller.php';
require_once './MVC/Core/connectDB.php';
require_once './Public/Classes/PHPExcel.php';
require_once './Public/Classes/PHPExcel/IOFactory.php';

$app = new App();

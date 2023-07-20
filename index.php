<?php

const _DEF = 1;
// Включение отчетности об ошибках
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 'on');
ini_set('error_log', __DIR__ . '/php-errors.log');
// Обработка ошибок и запись в лог
function log_error($errno, $errstr, $errfile, $errline): void
{
    $error_message = "Error [$errno] on ".$_SERVER['REQUEST_URI']."\n";
    $error_message .= "File: $errfile\n";
    $error_message .= "Line: $errline\n";
    $error_message .= "Message: $errstr\n";
    error_log($error_message);
}
// Установка пользовательского обработчика ошибок
set_error_handler('log_error');

try {
//автозагрузка классов
    spl_autoload_register(function (string $className) {
        require_once __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';
    });

    $route = $_SERVER['REQUEST_URI'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'];

    $routes = require __DIR__ . '/routes.php';

    $isRouteFound = false;
    foreach ($routes as $rout) {
        preg_match($rout[1], $route, $matches);
        if (!empty($matches) && preg_match("~$method~", $rout[0])) {
            $isRouteFound = true;
            break;
        }
    }

    if (!$isRouteFound) {
        throw new \Exceptions\NotFoundException();
    }

    unset($matches[0]);
    $controllerName = $rout[2][0];
    $actionName = $rout[2][1];

    if (!empty($rout[3])) {
        $argument = $rout[3];
    }
    $controller = new $controllerName();

    if (empty($argument)) {
        $controller->$actionName(...$matches);
    } else {
        $controller->$actionName($argument);
    }

} catch (\Exceptions\DbException $e) {
    $view = new \View\View(__DIR__ . '/templates/errors');
    $view->renderHtml('500.php', ['error' => $e->getMessage(), 'title' => 'Error 500'], 500);
} catch (\Exceptions\NotFoundException $e) {
    $view = new \View\View(__DIR__ . '/templates/errors');
    $view->renderHtml('404.php', ['error' => $e->getMessage(), 'title' => 'Страница не найдена'], 404);
} catch (\Exceptions\UnauthorizedException $e) {
    $view = new \View\View(__DIR__ . '/templates/errors');
    $view->renderHtml('401.php', ['error' => $e->getMessage(), 'title' => 'Error 401'], 401);
} catch (\Exceptions\ForbiddenException $e) {
    $view = new \View\View(__DIR__ . '/templates/errors');
    $view->renderHtml('403.php', ['error' => $e->getMessage(), 'title' => 'Error 403'], 403);
}
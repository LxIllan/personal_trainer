<?php

declare(strict_types=1);

use App\Application\Controllers\CustomerController;
use App\Application\Helpers\Util;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    /**
     * @api /customers
     * @method POST
     * @description Create user
     */
    $app->post('/customers', function (Request $request, Response $response) {
        $customerController = new CustomerController();
        $jwt = $request->getAttribute("token");
        $body = $request->getParsedBody();
        $body["branch_id"] = $jwt["branch_id"];
        $user = $customerController->create($body);
        $response->getBody()->write(Util::encodeData($user, "user", 201));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /customers
     * @method GET
     * @description Get all customers
     */
    $app->get('/customers', function (Request $request, Response $response) {
        $customerController = new CustomerController();
        $customers = $customerController->getCustomers();
        $response->getBody()->write(Util::encodeData($customers, "user"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /profile
     * @method GET
     * @description Get user by id
     */
    $app->get('/profile', function (Request $request, Response $response) {
        $customerController = new CustomerController();
        $token = $request->getAttribute("token");
        $user = $customerController->getCustomerById($token['user_id']);
        $response->getBody()->write(Util::encodeData($user, "user"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /customers
     * @method POST
     * @description Create user
     */
    $app->post('/customers/exist-email', function (Request $request, Response $response) {
        $customerController = new CustomerController();
        $body = $request->getParsedBody();
        $existEmail = $customerController->existEmail($body['email']);
        $response->getBody()->write(Util::encodeData($existEmail, "exist"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /cashiers/reset-password
     * @method POST
     * @description Reset cashier password
     */
    $app->post('/customers/reset-password', function (Request $request, Response $response) {
        $customerController = new CustomerController();
        $body = $request->getParsedBody();
        $wasUpdated = $customerController->resetPassword(intval($body['user_id']));
        $response->getBody()->write(Util::encodeData($wasUpdated, "response"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /customers/{id}
     * @method GET
     * @description Get user by id
     */
    $app->get('/customers/{id}', function (Request $request, Response $response, $args) {
        $customerController = new CustomerController();
        $user = $customerController->getCustomerById(intval($args['id']));
        $response->getBody()->write(Util::encodeData($user, "user"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /customers/{id}
     * @method PUT
     * @description Edit user by id
     */
    $app->put('/customers/{id}', function (Request $request, Response $response, $args) {
        $body = $request->getParsedBody();
        $customerController = new CustomerController();
        $user = $customerController->edit(intval($args['id']), $body);
        $response->getBody()->write(Util::encodeData($user, "user"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /customers/{id}
     * @method DELETE
     * @description Delete user by id
     */
    $app->delete('/customers/{id}', function (Request $request, Response $response, $args) {
        $customerController = new CustomerController();
        $user = $customerController->delete(intval($args['id']));
        $response->getBody()->write(Util::encodeData($user, "user"));
        return $response->withHeader('Content-Type', 'application/json');
    });
};

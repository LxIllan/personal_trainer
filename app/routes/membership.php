<?php

declare(strict_types=1);

use App\Application\Controllers\MembershipController;
use App\Application\Helpers\Util;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\App;

return function (App $app) {
    /**
     * @api /memberships
     * @method POST
     * @description Create a new membership
     */
    $app->post('/memberships', function (Request $request, Response $response) {
        $membershipController = new MembershipController();
        $body = $request->getParsedBody();
        $membership = $membershipController->create($body);
        if ($membership) {
            $response->getBody()->write(Util::encodeData($membership, "membership", 201));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }        
    });

    /**
     * @api /memberships
     * @method GET
     * @description Get all memberships
     */
    $app->get('/memberships', function (Request $request, Response $response) {
        $membershipController = new MembershipController();        
        $memberships = $membershipController->getMemberships();
        $response->getBody()->write(Util::encodeData($memberships, "memberships"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /memberships/{id}
     * @method GET
     * @description Get membership by id
     */
    $app->get('/memberships/{id}', function (Request $request, Response $response, $args) {
        $membershipController = new MembershipController();        
        $membership = $membershipController->getById(intval($args['id']));
        if ($membership) {
            $response->getBody()->write(Util::encodeData($membership, "membership"));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /memberships/{id}
     * @method PUT
     * @description edit membership by id
     */
    $app->put('/memberships/{id}', function (Request $request, Response $response, $args) {
        $membershipController = new MembershipController();        
        $body = $request->getParsedBody();
        $membership = $membershipController->edit(intval($args['id']), $body);
        if ($membership) {
            $response->getBody()->write(Util::encodeData($membership, "membership"));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /memberships/{id}
     * @method DELETE
     * @description Delete membership by id
     */
    $app->delete('/memberships/{id}', function (Request $request, Response $response, $args) {
        $membershipController = new MembershipController();
        $wasDeleted = $membershipController->delete(intval($args['id']));
        $response->getBody()->write(Util::encodeData($wasDeleted, "deleted"));
        return $response->withHeader('Content-Type', 'application/json');
    });
};

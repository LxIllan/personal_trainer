<?php

declare(strict_types=1);

use App\Application\Controllers\MemberController;
use App\Application\Helpers\Util;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use App\Application\Controllers\ReceiptController;
use Slim\App;

return function (App $app) {
    /**
     * @api /members
     * @method POST
     * @description Create member
     */
    $app->post('/members', function (Request $request, Response $response) {
        $memberController = new MemberController();
        $jwt = $request->getAttribute("token");
        $body = $request->getParsedBody();
        $body["user_id"] = $jwt["user_id"];
        $member = $memberController->create($body);
        $response->getBody()->write(Util::encodeData($member, "member", 201));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /members
     * @method GET
     * @description Get all members
     */
    $app->get('/members', function (Request $request, Response $response) {
        $memberController = new MemberController();
        $members = $memberController->getMembers();
        $response->getBody()->write(Util::encodeData($members, "member"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /profile
     * @method GET
     * @description Get member by id
     */
    $app->get('/profile', function (Request $request, Response $response) {
        $memberController = new MemberController();
        $token = $request->getAttribute("token");
        $member = $memberController->getMemberById($token['user_id']);
        $response->getBody()->write(Util::encodeData($member, "user"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /members
     * @method POST
     * @description Create user
     */
    $app->post('/members/exist-email', function (Request $request, Response $response) {
        $memberController = new MemberController();
        $body = $request->getParsedBody();
        $existEmail = $memberController->existEmail($body['email']);
        $response->getBody()->write(Util::encodeData($existEmail, "exist"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /cashiers/reset-password
     * @method POST
     * @description Reset cashier password
     */
    $app->post('/members/reset-password', function (Request $request, Response $response) {
        $memberController = new MemberController();
        $body = $request->getParsedBody();
        $wasUpdated = $memberController->resetPassword(intval($body['user_id']));
        $response->getBody()->write(Util::encodeData($wasUpdated, "response"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /members/{id}
     * @method GET
     * @description Get user by id
     */
    $app->get('/members/{id}', function (Request $request, Response $response, $args) {
        $memberController = new MemberController();
        $member = $memberController->getMemberById(intval($args['id']));        
        if ($member) {
            $response->getBody()->write(Util::encodeData($member, "member"));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /members/{id}
     * @method PUT
     * @description Edit user by id
     */
    $app->put('/members/{id}', function (Request $request, Response $response, $args) {
        $body = $request->getParsedBody();
        $memberController = new MemberController();
        $member = $memberController->edit(intval($args['id']), $body);
        $response->getBody()->write(Util::encodeData($member, "user"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /members/{id}
     * @method DELETE
     * @description Delete user by id
     */
    $app->delete('/members/{id}', function (Request $request, Response $response, $args) {
        $memberController = new MemberController();
        $member = $memberController->delete(intval($args['id']));
        $response->getBody()->write(Util::encodeData($member, "user"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /members/{id}/receipts
     * @method GET
     * @description Get receipts by member
     */
    $app->get('/members/{id}/receipts', function (Request $request, Response $response, $args) {        
        $receiptController = new ReceiptController();
        $jwt = $request->getAttribute("token");
        $receipts = $receiptController->getByMember(intval($args['id']));
        $response->getBody()->write(Util::encodeData($receipts, "receipts"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /members/{id}/pay-membership
     * @method PUT
     * @description Pay membership
     */
    $app->put('/members/{id}/pay-membership', function (Request $request, Response $response, $args) {        
        $memberController = new MemberController();
        $jwt = $request->getAttribute("token");
        $body = $request->getParsedBody();
        $body["user_id"] = $jwt["user_id"];
        $member = $memberController->payMembership(intval($args['id']), intval($body['membership_id']), intval($body['user_id']));
        $response->getBody()->write(Util::encodeData($member, "member"));
        return $response->withHeader('Content-Type', 'application/json');
    });
};

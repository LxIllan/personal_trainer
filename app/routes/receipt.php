<?php

declare(strict_types=1);

use App\Application\Controllers\ReceiptController;
use App\Application\Helpers\Util;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    /**
     * @api /receipts/{id}
     * @method GET
     * @description Get dish by id
     */
    $app->get('/receipts/{id}', function (Request $request, Response $response, $args) {
        $receiptController = new ReceiptController();
        $product = $receiptController->getById(intval($args['id']));
        $response->getBody()->write(Util::encodeData($product, "product"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /receipts/{id}
     * @method PUT
     * @description Edit a dish
     */
    $app->put('/receipts/{id}', function (Request $request, Response $response, $args) {
        $receiptController = new ReceiptController();
        $body = $request->getParsedBody();
        $product = $receiptController->edit(intval($args['id']), $body);
        $response->getBody()->write(Util::encodeData($product, "product"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /receipts/{id}
     * @method DELETE
     * @description Delete a dish
     */
    $app->delete('/receipts/{id}', function (Request $request, Response $response, $args) {
        $receiptController = new ReceiptController();
        $wasDeleted = $receiptController->delete(intval($args['id']));
        $response->getBody()->write(Util::encodeData($wasDeleted, "response"));
        return $response->withHeader('Content-Type', 'application/json');
    });
};

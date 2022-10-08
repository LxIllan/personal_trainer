<?php

declare(strict_types=1);

use App\Application\Controllers\ReceiptController;
use App\Application\Helpers\Util;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\App;

return function (App $app) {
    /**
     * @api /receipts/{id}
     * @method GET
     * @description Get receipt by id
     */
    $app->get('/receipts/{id}', function (Request $request, Response $response, $args) {
        $receiptController = new ReceiptController();
        $receipt = $receiptController->getById(intval($args['id']));        
        if ($receipt) {
            $response->getBody()->write(Util::encodeData($receipt, "receipt"));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /receipts/{id}
     * @method PUT
     * @description Edit receipt by id
     */
    $app->put('/receipts/{id}', function (Request $request, Response $response, $args) {
        $receiptController = new ReceiptController();
        $body = $request->getParsedBody();
        $receipt = $receiptController->edit(intval($args['id']), $body);
        if ($receipt) {
            $response->getBody()->write(Util::encodeData($receipt, "receipt"));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /receipts/{id}
     * @method DELETE
     * @description Delete receipt by id
     */
    $app->delete('/receipts/{id}', function (Request $request, Response $response, $args) {
        $receiptController = new ReceiptController();
        $wasDeleted = $receiptController->delete(intval($args['id']));
        $response->getBody()->write(Util::encodeData($wasDeleted, "response"));
        return $response->withHeader('Content-Type', 'application/json');
    });
};

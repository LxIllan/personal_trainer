<?php

declare(strict_types=1);

use App\Application\Controllers\ExerciseController;
use App\Application\Helpers\Util;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\App;


return function (App $app) {
    /**
     * @api /exercises
     * @method POST
     * @description Create a new expense
     */
    $app->post('/exercises', function (Request $request, Response $response) {
        $exerciseController = new ExerciseController();
        $jwt = $request->getAttribute("token");
        $body = $request->getParsedBody();
        $body['branch_id'] = $jwt['branch_id'];
        $body['user_id'] = $jwt['user_id'];
        $expense = $exerciseController->create($body);
        $response->getBody()->write(Util::encodeData($expense, "expense", 201));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /exercises
     * @method GET
     * @description Get expense by id
     */
    $app->get('/exercises', function (Request $request, Response $response) {
        $exerciseController = new ExerciseController();
        $exercises = $exerciseController->getExercises();
        $response->getBody()->write(Util::encodeData($exercises, "exercises"));
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /exercises/{id}
     * @method GET
     * @description Get expense by id
     */
    $app->get('/exercises/{id}', function (Request $request, Response $response, $args) {
        $exerciseController = new ExerciseController();
        $expense = $exerciseController->getById(intval($args['id']));
        if ($expense) {
            $response->getBody()->write(Util::encodeData($expense, "expense"));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /exercises/{id}
     * @method PUT
     * @description Edit expense by id
     */
    $app->put('/exercises/{id}', function (Request $request, Response $response, $args) {
        $exerciseController = new ExerciseController();
        $body = $request->getParsedBody();
        $expense = $exerciseController->edit(intval($args['id']), $body);
        if ($expense) {
            $response->getBody()->write(Util::encodeData($expense, "expense"));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /exercises/{id}
     * @method DELETE
     * @description Delete expense by id
     */
    $app->delete('/exercises/{id}', function (Request $request, Response $response, $args) {
        $exerciseController = new ExerciseController();
        $wasDeleted = $exerciseController->delete(intval($args['id']));
        $response->getBody()->write(Util::encodeData($wasDeleted, "result"));
        return $response->withHeader('Content-Type', 'application/json');
    });
};

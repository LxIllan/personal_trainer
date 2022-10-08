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
     * @description Create a new exercise
     */
    $app->post('/exercises', function (Request $request, Response $response) {
        $exerciseController = new ExerciseController();
        $jwt = $request->getAttribute("token");
        $body = $request->getParsedBody();
        $body['user_id'] = $jwt['user_id'];
        $exercise = $exerciseController->create($body);        
        if ($exercise) {
            $response->getBody()->write(Util::encodeData($exercise, "exercise", 201));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
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
     * @description Get exercise by id
     */
    $app->get('/exercises/{id}', function (Request $request, Response $response, $args) {
        $exerciseController = new ExerciseController();
        $exercise = $exerciseController->getById(intval($args['id']));
        if ($exercise) {
            $response->getBody()->write(Util::encodeData($exercise, "exercise"));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /exercises/{id}
     * @method PUT
     * @description Edit exercise by id
     */
    $app->put('/exercises/{id}', function (Request $request, Response $response, $args) {
        $exerciseController = new ExerciseController();
        $body = $request->getParsedBody();
        $exercise = $exerciseController->edit(intval($args['id']), $body);
        if ($exercise) {
            $response->getBody()->write(Util::encodeData($exercise, "exercise"));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /exercises/{id}
     * @method DELETE
     * @description Delete exercise by id
     */
    $app->delete('/exercises/{id}', function (Request $request, Response $response, $args) {
        $exerciseController = new ExerciseController();
        $wasDeleted = $exerciseController->delete(intval($args['id']));
        $response->getBody()->write(Util::encodeData($wasDeleted, "result"));
        return $response->withHeader('Content-Type', 'application/json');
    });
};

<?php

declare(strict_types=1);

use App\Application\Controllers\FileController;
use App\Application\Helpers\Util;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\UploadedFile;
use Slim\App;

return function (App $app) {    
    /**
     * Moves the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param string $directory directory to which the file is moved
     * @param UploadedFile $uploadedFile file uploaded file to move
     * @return string filename of moved file
     */
    function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        file_put_contents(__DIR__ . "/../../logs/system.log", date("[D, d M Y H:i:s]") . " " .
            '$extension-> ' . json_encode($extension) . " " .
            "file:" . __DIR__ . '/' . basename(__FILE__) . "\r\n", FILE_APPEND);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        file_put_contents(__DIR__ . "/../../logs/system.log", date("[D, d M Y H:i:s]") . " " .
            '$filename-> ' . json_encode($filename) . " " .
            "file:" . __DIR__ . '/' . basename(__FILE__) . "\r\n", FILE_APPEND);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return "$directory/$filename";
    }

    $app->post('/files', function(Request $request, Response $response) {        
        // TODO: create folder if not exists
        $directory = __DIR__ . '/../../public/uploads';
        file_put_contents(__DIR__ . "/../../logs/system.log", date("[D, d M Y H:i:s]") . " " .
            '$directory-> ' . json_encode($directory) . " " .
            "file:" . __DIR__ . '/' . basename(__FILE__) . "\r\n", FILE_APPEND);
        $body = $request->getParsedBody();
        file_put_contents(__DIR__ . "/../../logs/system.log", date("[D, d M Y H:i:s]") . " " .
            '$body-> ' . json_encode($body) . " " .
            "file:" . __DIR__ . '/' . basename(__FILE__) . "\r\n", FILE_APPEND);
        $uploadedFiles = $request->getUploadedFiles();
    
        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['file'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = moveUploadedFile($directory, $uploadedFile);
            $response->getBody()->write('uploaded ' . $filename . '<br/>');
        }

        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * @api /files
     * @method POST
     * @description Create a file
     */
    $app->post('/filess', function (Request $request, Response $response) {        
        $fileController = new FileController();
        $body = $request->getParsedBody();
        $file = $fileController->create($body);
        if ($file) {
            $response->getBody()->write(Util::encodeData($file, "file", 201));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /files/{id}
     * @method GET
     * @description Get file by id
     */
    $app->get('/files/{id}', function (Request $request, Response $response, $args) {
        $fileController = new FileController();
        $file = $fileController->getById(intval($args['id']));        
        if ($file) {
            $response->getBody()->write(Util::encodeData($file, "file"));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /files/{id}
     * @method PUT
     * @description Edit a file
     */
    $app->put('/files/{id}', function (Request $request, Response $response, $args) {
        $fileController = new FileController();
        $body = $request->getParsedBody();
        $file = $fileController->edit(intval($args['id']), $body);
        if ($file) {
            $response->getBody()->write(Util::encodeData($file, "file"));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($request);
        }
    });

    /**
     * @api /files/{id}
     * @method DELETE
     * @description Delete a dish
     */
    $app->delete('/files/{id}', function (Request $request, Response $response, $args) {
        $fileController = new FileController();
        $wasDeleted = $fileController->delete(intval($args['id']));
        $response->getBody()->write(Util::encodeData($wasDeleted, "response"));
        return $response->withHeader('Content-Type', 'application/json');
    });
};

<?php

use Books\Middleware\JsonBodyParserMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response as Psr7Response;
use Books\Model\BookModel;

require __DIR__ . '/../vendor/autoload.php';


$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);
$app->add(new JsonBodyParserMiddleware());

$app->post('/users', function (Request $request, Response $response, $args): Response {
    \Books\Model\User::createTable();
    $data = $request->getParsedBody();
    if (!isset($data['username']) || !isset($data['password'])) {
        return $response->withStatus(400);
    }
    $user = new \Books\Model\User($data['username'], $data['password']);
    $user->save();
    $json = json_encode($user->toArray());
    $response->getBody()->write($json);
    return $response;
});

$securityMiddleware = function (Request $request, RequestHandler $handler): Response {

    if (!$request->hasHeader('Authorization')) {
        $response = new Psr7Response();
        return $response->withStatus(401);
    }

    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];

    $customer = \Books\Model\User::findBy('username', $username);
    if ($customer === null) {
        $response = new Psr7Response();
        return $response->withStatus(401);
    }

    if ($customer->getPassword() !== $password) {
        $response = new Psr7Response();
        return $response->withStatus(401);
    }

    $request = $request->withAttribute('customer', $customer);
    return $handler->handle($request);
};



$app->get('/books', function (Request $request, Response $response, $args) {
    $books = BookModel::getBooks();
    $payload = json_encode(array_map(fn(BookModel $book): array => $book->toArrayGet(), $books));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/books/{bookId}', function (Request $request, Response $response, $args) {
    $book = BookModel::find((int)$args['bookId']);
    if ($book === null) {
        return $response->withStatus(404);
    }
    $payload = json_encode($book->toArray());
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/books', function (Request $request, Response $response, $args): Response {
    BookModel::createTable();
    $data = $request->getParsedBody();
    if (!isset($data['name']) || !isset($data['author']) || !isset($data['publisher'])
        || !isset($data['isbn']) || !isset($data['pages'])) {
        return $response->withStatus(400);
    }
    $book = new BookModel($data['name'], $data['author'], $data['publisher'], $data['isbn'], $data['pages']);
    $id = $book->save();
    $json = json_encode($book->toArray());
    $response->getBody()->write($json);
    return $response->withHeader('Location', '/books/'.$id);
})->add($securityMiddleware);

$app->put('/books/{bookId}', function (Request $request, Response $response, $args): Response {
    $book = BookModel::find((int)$args['bookId']);
    if ($book === null) {
        return $response->withStatus(404);
    }
    $data = $request->getParsedBody();
    if (!isset($data['name']) || !isset($data['author']) || !isset($data['publisher'])
        || !isset($data['isbn']) || !isset($data['pages'])) {
        return $response->withStatus(400);
    }
    $book->setName($data['name'])->setAuthor($data['author'])->setISBN($data['isbn'])->setPublisher($data['publisher'])->setPages($data['pages']);
    $book->save();
    $json = json_encode($book->toArray());
    $response->getBody()->write($json);
    return $response->withStatus(204);
})->add($securityMiddleware);

$app->delete('/books/{bookId}', function (Request $request, Response $response, $args): Response {
    $book = BookModel::find((int)$args['bookId']);
    if ($book === null) {
        return $response->withStatus(404);
    }

    $book->delete();
    $json = json_encode($book->toArray());
    $response->getBody()->write($json);
    return $response->withStatus(204);
})->add($securityMiddleware);


$app->run();

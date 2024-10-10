<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Selective\BasePath\BasePathMiddleware;
use App\Models\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

//GET request
$app->get('/customers-data/all', function (Request $request, Response $response) {
    $sql = "SELECT * FROM customers";
   
    try {
      $db = new DB();
      $conn = $db->connect();
      $stmt = $conn->query($sql);
      $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;
     
      $response->getBody()->write(json_encode($customers));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    } catch (PDOException $e) {
      $error = array(
        "message" => $e->getMessage()
      );
   
      $response->getBody()->write(json_encode($error));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
   });


//POST request
   $app->post('/customers-data/add', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $name = $data["name"];
    $email = $data["email"];
    $phone = $data["phone"];
   
    $sql = "INSERT INTO customers (name, email, phone) VALUES (:name, :email, :phone)";
   
    try {
      $db = new DB();
      $conn = $db->connect();
     
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':phone', $phone);
   
      $result = $stmt->execute();
   
      $db = null;
      $response->getBody()->write(json_encode($result));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    } catch (PDOException $e) {
      $error = array(
        "message" => $e->getMessage()
      );
   
      $response->getBody()->write(json_encode($error));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
   });


   // PUT Request
   $app->put(
    '/customers-data/update/{id}',
    function (Request $request, Response $response, array $args) 
{
 $id = $request->getAttribute('id');
 $data = $request->getParsedBody();
 $name = $data["name"];
 $email = $data["email"];
 $phone = $data["phone"];

 $sql = "UPDATE customers SET
           name = :name,
           email = :email,
           phone = :phone
 WHERE id = $id";

 try {
   $db = new DB();
   $conn = $db->connect();
  
   $stmt = $conn->prepare($sql);
   $stmt->bindParam(':name', $name);
   $stmt->bindParam(':email', $email);
   $stmt->bindParam(':phone', $phone);

   $result = $stmt->execute();

   $db = null;
   echo "Update successful! ";
   $response->getBody()->write(json_encode($result));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
 } catch (PDOException $e) {
   $error = array(
     "message" => $e->getMessage()
   );

   $response->getBody()->write(json_encode($error));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(500);
 }
});


//DELETE Request 
$app->delete('/customers-data/delete/{id}', function (Request $request, Response $response, array $args) {
    $id = $args["id"];
   
    $sql = "DELETE FROM customers WHERE id = $id";
   
    try {
      $db = new DB();
      $conn = $db->connect();
     
      $stmt = $conn->prepare($sql);
      $result = $stmt->execute();
   
      $db = null;
      $response->getBody()->write(json_encode($result));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    } catch (PDOException $e) {
      $error = array(
        "message" => $e->getMessage()
      );
   
      $response->getBody()->write(json_encode($error));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
   });


$app->run();
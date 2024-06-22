<?php

namespace Router;

require_once "../data/datasource/remote_data_source.php";
require_once "../data/model/AppResponse.php";
require_once "../data/model/InsertParams.php";
require_once "../data/model/RegisterParams.php";

// Imports
use Data\Datasource\LocalDataSource;
use Data\Model\InsertParams;
use Data\Model\RegisterParams;
use Data\Model\ResponseError;
use Data\Model\ResponseSuccess;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use RuntimeException;


class MealDetailMiddleware implements Middleware {
    private $lds;

    // Implementasi Middleware.
    public function process(
        Request $request,         
        RequestHandler $requestHandler): Response {
        $params = $request->getQueryParams();

        $recipeId = $params["id"]?? 0;
        $title = $params["title"]?? "";

        if($title ==="")  {
            $response = $requestHandler->handle($request);
            return $response;
        };

        $params = new InsertParams(            
            $title,
            $recipeId
        );

        $localDataSource = $this->lds->insertMealHistory($params);
        if($localDataSource instanceof ResponseError) {
            $message = $localDataSource->getMessage();
            throw new RuntimeException("Failed to insert meal history :" . $message);
        } else if ($localDataSource instanceof ResponseSuccess) {
            $response = $requestHandler->handle($request);
            return $response;
        }        
        throw new RuntimeException("Unexpected response from insertMealHistory");
    }

    public function __construct(){        
        $this->lds = new LocalDataSource();
    }
}

class RegisterMiddleware implements Middleware {
    private $lds;

    // Implementasi Middleware.
    public function process(
        Request $request,         
        RequestHandler $requestHandler): Response {
        $params = $request->getQueryParams();

        $username = $params["username"]?? "";
        $password = $params["password"]?? "";

        if($username ==="")  {
            $response = $requestHandler->handle($request);
            return $response;
        };

        $params = new RegisterParams(            
            $username,
            $password
        );

        $params = new RegisterParams($username, password_hash($password, PASSWORD_BCRYPT));

        $localDataSource = $this->lds->registerUser($params);
        if($localDataSource instanceof ResponseError) {
            $message = $localDataSource->getMessage();
            throw new RuntimeException("Failed to insert users :" . $message);
        } else if ($localDataSource instanceof ResponseSuccess) {
            $response = $requestHandler->handle($request);
            return $response;
        }        
        throw new RuntimeException("Unexpected response from insertMealHistory");
    }

    public function __construct(){        
        $this->lds = new LocalDataSource();
    }
}
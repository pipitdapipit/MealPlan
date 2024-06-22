<?php

namespace Router;

require_once "middleware.php";

// Imports
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Router\MealDetailMiddleware;
use Router\RegisterMiddleware;


class AppRoutes {
    private $mealDetailMiddleware;
    private $registerMiddleware;
    const basePath = __DIR__ . "/../src/pages/";
    public function defineRoutes($app) {
        
        // Main Screen Route
        $app->get("/", function(Request $request, Response $response,) {            
            include(self::basePath . "main.html");
            return $response;
        });

        // Recipe Screen Route
        $app->get("/recipe-history", function(Request $request, Response $response,) {            
            include(self::basePath . "recipe_history.html");
            return $response;
        });

        // Recipe History  Screen Route
        $app->get("/recipe", function(Request $request, Response $response,) {            
            include(self::basePath . "recipe.html");
            return $response;
        });

        // Recipe Detail Screen Route
        $app->get("/recipe-detail", function(Request $request, Response $response,) {            
            include(self::basePath . "recipe_detail.html");
            return $response;
        })->add($this->mealDetailMiddleware);    
        
        //Workout
        $app->get("/workout", function(Request $request, Response $response,) {            
            include(self::basePath . "Workout.html");
            return $response;
        });

        //Recipes food
        $app->get("/recipes-food", function(Request $request, Response $response,) {            
            include(self::basePath . "recipesfood.html");
            return $response;
        });

        //Login
        $app->get("/login", function(Request $request, Response $response,) {            
            include(self::basePath . "login.html");
            return $response;
        });

        //Register
        $app->get("/register", function(Request $request, Response $response,) {            
            include(self::basePath . "register.html");
            return $response;
        })->add($this->registerMiddleware);
    }

    public function __construct(){
        $this->mealDetailMiddleware = new MealDetailMiddleware();
        $this->registerMiddleware = new RegisterMiddleware();
    }
}
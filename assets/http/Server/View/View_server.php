<?php

namespace App\Suiteziel\Http\Server\View;

use App\Suiteziel\Framework\View;
use App\Suiteziel\Http\Server\Controller\Controller_tasks;

# GraphQL
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;


class View_server extends View
{

  public function __construct($aUri_ = false)
  {
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);
    $this->aUri = $aUri_;
  }

  public function index()
  {
    var_dump('hello world');
    var_dump($this->aUri);
    #var_dump(file_get_contents("php://input"));
try
{
  header('Content-Type: application/json');
    $queryType = new ObjectType([
      'name' => 'Query',
      'fields' => [
          'echo' => [
              'type' => Type::string(),
              'args' => [
                  'message' => Type::nonNull(Type::string()),
              ],
              'resolve' => function ($rootValue, $args) {
                  return $rootValue['prefix'] . $args['message'];
              }
          ],
        ],
      ]);

// '{"queryx": "query { echo(message: \"Hello World\") }" }'

  $schema = new Schema([
    'query' => $queryType
  ]);

  $rawInput = file_get_contents('php://input');
  $input = json_decode($rawInput, true);
  $query = $input['query'];
  $variableValues = isset($input['variables']) ? $input['variables'] : null;


    $rootValue = ['prefix' => 'You said: '];
    $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
    $output = $result->toArray();
    
    


  } catch (\Exception $e) {
    $output = [
        'errors' => [
            [
                'message' => $e->getMessage()
            ]
        ]
    ];
  }

  return print json_encode($output);
 

  }
}


?>
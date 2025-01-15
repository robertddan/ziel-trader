<?php

namespace App\Suiteziel\Providers\Graphql\Controller;
use App\Suiteziel\Framework\Controller;

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

use Amp\ByteStream\ResourceOutputStream;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Socket;
use Monolog\Logger;
use Amp\Loop;

use Amp\Http\Server\Router;
use Amp\Http\Server\Server;
use Amp\Http\Server\StaticContent\DocumentRoot;

use Amp\Http\Server\FormParser;
use Amp\Http\Server\Request;




class Controller_tasks extends Controller
{
  public function forRun() {
/*
    $cert = new Socket\Certificate(__DIR__ . '/../test/server.pem');

    $context = (new Socket\BindContext)
        ->withTlsContext((new Socket\ServerTlsContext)->withDefaultCertificate($cert));
*/
    $servers = [
        Socket\Server::listen("127.0.0.1:1337"),
        Socket\Server::listen("[::]:1337"),
        //Socket\Server::listen("0.0.0.0:1338", $context),
        //Socket\Server::listen("[::]:1338", $context),
    ];

    $logHandler = new StreamHandler(new ResourceOutputStream(STDOUT));
    $logHandler->setFormatter(new ConsoleFormatter);
    $logger = new Logger('server');
    $logger->pushHandler($logHandler);


$temp = new CallableRequestHandler(array($this, 'reqHandler'));
# or
$router = new Router;

$router->addRoute('GET', '/', new CallableRequestHandler(function (Request $request) {
    
    var_dump(($request->uri->query)); 

  return new Response(Status::OK, ['content-type' => 'text/plain'], 'Hello, world!');
}));
/*
$router->addRoute('GET', '/', new CallableRequestHandler(function (Request $request) {
    # @var FormParser\Form $form ##
    $form = yield FormParser\parseForm($request);
    $rawInput = file_get_contents('php://input');

var_dump(array(
    #$request->uri, //private
    $request,
    $form,
    $form->getValue('query'),
    $_SERVER
));

    return new Response(Status::OK, [
        "content-type" => "text/plain; charset=utf-8"
    ], $form->getValue("text") ?? "Hello, World!");
}));
*/
    $server = new HttpServer($servers, $router, $logger);

    return $server->start();
/*
    // Stop the server when SIGINT is received (this is technically optional, but it is best to call Server::stop()).
    Loop::onSignal(SIGINT, static function (string $watcherId) use ($server) {
        Loop::cancel($watcherId);
        yield $server->stop();
    });
*/
}

  public function reqHandler() {
    $this->runOutput();
    if (!isset($this->output)) $this->output = json_encode(array('No response'));
    return new Response(Status::OK, [
        "content-type" => "text/plain; charset=utf-8"
        #"content-type" => "text/json; charset=utf-8"
    ], $this->output);
  }

  public function runOutput ()
  {

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

  $schema = new Schema([
    'query' => $queryType
  ]);

  $rawInput = file_get_contents('php://input');
  
  var_dump(array(
    $rawInput,
    $_SERVER,
    $_POST,

  ));
  #exit();

  $input = json_decode($rawInput, true);
  $query = $input['query'];
  $variableValues = isset($input['variables']) ? $input['variables'] : null;
  
  try {
    
      $rootValue = ['prefix' => 'You said: '];
      $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
      $this->output = $result->toArray();

  } catch (\Exception $e) {
      $this->output = [
          'errors xxx' => [
              [
                  'message' => $e->getMessage()
              ]
          ]
      ];
  }
  #header('Content-Type: application/json');
  #return json_encode($this->output);
  $this->output = json_encode($this->output);
  
  }

  public function withRouter()
  {


#$documentRoot = new DocumentRoot(__DIR__ . '/public');

var_dump('###');
var_dump('###');

#return true;
#return print 4;


$router = new Router;
$router->addRoute('GET', '/', new CallableRequestHandler(function () {
  return new Response(Status::OK, ['content-type' => 'text/plain'], 'Hello, world!');
}));

#$router->setFallback($documentRoot);

$servers = [
  Socket\Server::listen("0.0.0.0:1337"),
  Socket\Server::listen("[::]:1337"),
  #Socket\Server::listen("0.0.0.0:1338", $context),
  #Socket\Server::listen("[::]:1338", $context),
];

$logHandler = new StreamHandler(new ResourceOutputStream(STDOUT));
$logHandler->setFormatter(new ConsoleFormatter);
$logger = new Logger('server');
$logger->pushHandler($logHandler);

$server = new Server($servers, $router, $logger);

yield $server->start();

  }

  public function run ()
  {
    
    // server
    Loop::run(array($this, 'forRun'));
    #$this->withRouter();



/*
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
*/

  }
}

?>
<?php

namespace Toy;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Toy\Base\Application;
use Toy\Exceptions\Exception;
use Toy\Http\Response;
use Toy\Http\ServerRequest;
use Toy\Interfaces\CollectorInterface;
use Toy\Router\Collector;

class WebApplication extends Application
{

    protected $collector;

    public function __construct(array $defaults = [],
                                bool $frozenValues = true,
                                CollectorInterface $collector = null)
    {
        parent::__construct($defaults, $frozenValues);
        $this->collector = $collector ?: new Collector('');
    }

    /**
     * Регистрация обработчика GET запроса
     * @param string $pattern
     * @param callable $function
     * @return CollectorInterface
     * @throws Exception
     */
    public function get(string $pattern, callable $function): CollectorInterface
    {
        return $this->collector->addRoute('get', $pattern, $function);
    }

    /**
     * Регистрация обработчика POST запроса
     * @param string $pattern
     * @param callable $function
     * @return CollectorInterface
     * @throws Exception
     */
    public function post(string $pattern, callable $function): CollectorInterface
    {
        return $this->collector->addRoute('post', $pattern, $function);
    }

    /**
     * Регистрация обработчика PUT запроса
     * @param string $pattern
     * @param callable $function
     * @return CollectorInterface
     * @throws Exception
     */
    public function put(string $pattern, callable $function): CollectorInterface
    {
        return $this->collector->addRoute('put', $pattern, $function);
    }

    /**
     * Регистрация обработчика PATCH запроса
     * @param string $pattern
     * @param callable $function
     * @return CollectorInterface
     * @throws Exception
     */
    public function patch(string $pattern, callable $function): CollectorInterface
    {
        return $this->collector->addRoute('patch', $pattern, $function);
    }

    /**
     * Регистрация обработчика DELETE запроса
     * @param string $pattern
     * @param callable $function
     * @return CollectorInterface
     * @throws Exception
     */
    public function delete(string $pattern, callable $function): CollectorInterface
    {
        return $this->collector->addRoute('delete', $pattern, $function);
    }

    /**
     * Регистрация обработчика HEAD запроса
     * @param string $pattern
     * @param callable $function
     * @return CollectorInterface
     * @throws Exception
     */
    public function head(string $pattern, callable $function): CollectorInterface
    {
        return $this->collector->addRoute('head', $pattern, $function);
    }

    /**
     * Регистрация обработчика CONNECT запроса
     * @param string $pattern
     * @param callable $function
     * @return CollectorInterface
     * @throws Exception
     */
    public function connect(string $pattern, callable $function): CollectorInterface
    {
        return $this->collector->addRoute('connect', $pattern, $function);
    }

    /**
     * Регистрация обработчика OPTIONS запроса
     * @param string $pattern
     * @param callable $function
     * @return CollectorInterface
     * @throws Exception
     */
    public function options(string $pattern, callable $function): CollectorInterface
    {
        return $this->collector->addRoute('options', $pattern, $function);
    }

    /**
     * Регистрация обработчика TRACE запроса
     * @param string $pattern
     * @param callable $function
     * @return CollectorInterface
     * @throws Exception
     */
    public function trace(string $pattern, callable $function): CollectorInterface
    {
        return $this->collector->addRoute('trace', $pattern, $function);
    }

    /**
     * Получить объект запроса
     * @return ServerRequestInterface
     */
    protected function getRequest(): ServerRequestInterface
    {
        return ServerRequest::forGlobal();
    }

    /**
     * Получить объект ответа
     * @return ResponseInterface
     * @throws Exception
     */
    protected function getResponse(): ResponseInterface
    {
        return Response::forGlobal();
    }

    /**
     * Отправка ответа
     * @param ResponseInterface $response
     */
    public function respond(ResponseInterface $response)
    {
        if (!headers_sent()) {
            header(sprintf(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));

            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }
        }
        $content = $response->getBody()->getContents();
        file_put_contents('php://output', $content);
    }

    /**
     * Запустить приложение
     * @param bool $silentRespond
     * @return ResponseInterface|static
     * @throws Exception
     */
    public function run($silentRespond = true)
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $route = $this->collector->findRoute($request->getMethod(), $request->getUri()->getPath());
        if (!empty($route)) {
            $matches = $route->getMatches();
            foreach ($matches as $name => $value) {
                $request = $request->withAttribute($name, $value);
            }

            $handler = $route->getHandler();
            $response = $handler($request, $response, $this);
        } else {
            $response = $response->withStatus(404);
        }
        if ($silentRespond) {
            $this->respond($response);
        }
        return $response;
    }

}
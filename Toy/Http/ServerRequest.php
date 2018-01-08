<?php

namespace Toy\Http;

use Psr\Http\Message\ServerRequestInterface;

class ServerRequest extends Request implements ServerRequestInterface
{

    protected $serverParams = [];
    protected $cookieParams = [];
    protected $queryParams = [];
    protected $uploadedFiles = [];
    protected $parsedBody = [];
    protected $attributes = [];

    function __construct(array $get,
                         array $post,
                         array $files,
                         array $cookie,
                         array $server)
    {
        $this->queryParams = $get;
        $this->parsedBody = $post;
        $this->uploadedFiles = $files;
        $this->cookieParams = $cookie;
        $this->serverParams = $server;
    }

    private static function _getHeaders()
    {
        if (!function_exists('apache_request_headers')) {
            $arh = array();
            $rx_http = '/\AHTTP_/';
            foreach ($_SERVER as $key => $val) {
                if (preg_match($rx_http, $key)) {
                    $arh_key = preg_replace($rx_http, '', $key);
                    $rx_matches = explode('_', $arh_key);
                    if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
                        foreach ($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
                        $arh_key = implode('-', $rx_matches);
                    }
                    $arh[$arh_key] = $val;
                }
            }
            return ($arh);
        }
        return apache_request_headers();
    }

    /**
     * Получить экземпляр объекта для глобального запроса
     * @return ServerRequestInterface
     */
    public static function forGlobal()
    {
        /** @var ServerRequestInterface $request */
        $request = new ServerRequest($_GET, $_POST, $_FILES, $_COOKIE, $_SERVER);
        $headers = self::_getHeaders();
        if (!empty($headers)) {
            foreach ($headers as $name => $value) {
                $request = $request->withHeader($name, $value);
            }
        }
        $protocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL');
        $protocolArray = explode('/', $protocol);
        $request = $request
            ->withProtocolVersion($protocolArray[1])
            ->withMethod(filter_input(INPUT_SERVER, 'REQUEST_METHOD'))
            ->withUri(Uri::forGlobal())
            ->withBody(new Stream(fopen('php://input', 'r')));
        return $request;
    }

    /**
     * @inheritdoc
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * @inheritdoc
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * @inheritdoc
     */
    public function withCookieParams(array $cookies)
    {
        if ($this->cookieParams === $cookies) {
            return $this;
        }
        $instance = clone $this;
        $instance->cookieParams = $cookies;
        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @inheritdoc
     */
    public function withQueryParams(array $query)
    {
        if ($this->queryParams === $query) {
            return $this;
        }
        $instance = clone $this;
        $instance->queryParams = $query;
        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * @inheritdoc
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        if ($this->uploadedFiles === $uploadedFiles) {
            return $this;
        }
        $instance = clone $this;
        $instance->uploadedFiles = $uploadedFiles;
        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {
        if ($this->parsedBody === $data) {
            return $this;
        }
        $instance = clone $this;
        $instance->parsedBody = $data;
        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function getAttribute($name, $default = null)
    {
        if (false === array_key_exists($name, $this->attributes)) {
            return $default;
        }
        return $this->attributes[$name];
    }

    /**
     * @inheritdoc
     */
    public function withAttribute($name, $value)
    {
        $instance = clone $this;
        $instance->attributes[$name] = $value;
        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function withoutAttribute($name)
    {
        if (false === isset($this->attributes[$name])) {
            return clone $this;
        }
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }
}

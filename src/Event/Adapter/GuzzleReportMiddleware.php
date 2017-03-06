<?php

namespace Athena\Event\Adapter;

use Athena\Event\HttpTransactionCompleted;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GuzzleReportMiddleware
{
    public static function eventCapture(EventDispatcher $emitter)
    {
        return function (callable $handler) use ($emitter) {
            return function (RequestInterface $request, array $options) use ($handler, $emitter) {
                $promise = $handler($request, $options);
                return $promise->then(
                    function (ResponseInterface $response) use ($handler, $request, $emitter) {
                        $emitter->dispatch(HttpTransactionCompleted::AFTER,
                            new HttpTransactionCompleted(
                                (string) static::formatRequest($request),
                                (string) static::formatResponse($response),
                                (string) $request->getUri(),
                                $request->getMethod()
                            )
                        );

                        return $response;
                    }
                );
            };
        };
    }

    private function formatRequest(RequestInterface $message)
    {
        $method  = $message->getMethod();
        $uri     = $message->getUri();
        $headers = static::formatHeaders($message->getHeaders());
        $body    = strval($message->getBody());
        
        $message->getBody()->rewind();

        return <<<EOF
$method $uri
$headers

$body
EOF;
    }

    private static function formatHeaders(array $headers)
    {
        $string = "";
        foreach ($headers as $key => $value) {
            $string .= sprintf("%s: %s", $key, implode("\n", $value))."\n";
        }
        return $string;
    }

    private function formatResponse(ResponseInterface $response)
    {
        $reason   = $response->getReasonPhrase();
        $version  = $response->getProtocolVersion();
        $status   = $response->getStatusCode();
        $headers  = static::formatHeaders($response->getHeaders());
        $body     = $response->getBody()->getContents();
        $response->getBody()->rewind();

        return <<<EOF
HTTP/$version $status $reason
$headers

$body
EOF;
    }
}
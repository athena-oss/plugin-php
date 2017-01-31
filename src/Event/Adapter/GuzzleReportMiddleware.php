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
                        $requestBody  = $request->getBody()->getContents();
                        $responseBody = $response->getBody()->getContents();

                        $request->getBody()->rewind();
                        $response->getBody()->rewind();

                        $emitter->dispatch(HttpTransactionCompleted::AFTER,
                            new HttpTransactionCompleted(
                                (string) $requestBody,
                                (string) $responseBody,
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
}
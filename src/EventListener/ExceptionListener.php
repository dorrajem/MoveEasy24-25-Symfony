<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\Request;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Psr\Log\LoggerInterface;

class ExceptionListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Determine the status code
        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        // Prepare error data
        $errorData = [
            'status'  => 'error',
            'code'    => $statusCode,
            'message' => $exception->getMessage(),
        ];


        // Log the exception (optional but recommended)
        $this->logger->error($exception->getMessage(), [
            'exception' => $exception,
        ]);

        // Create the JSON response
        $response = new JsonResponse($errorData, $statusCode);

        // Set the response
        $event->setResponse($response);
    }
}

<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ApiResponseFactory
{
    public function createBadRequestResponse(string $path): JsonResponse
    {
        return new JsonResponse(
            $this->getErrorResponse([
                $path => 'bad request'
            ]),
            400
        );
    }

    public function createValidationErrorResponse(array $errors): JsonResponse
    {
        return new JsonResponse(
            $this->getErrorResponse($errors),
            400
        );
    }

    public function formatConstraintValidationErrors(ConstraintViolationListInterface $constraintViolationList): array
    {
        $errors = [];

        foreach ($constraintViolationList as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }

    private function getErrorResponse(array $errors): array
    {
        $result = ['errors' => []];

        foreach ($errors as $path => $message) {
            $result['errors'][] = [
                'path' => $path,
                'message' => $message,
            ];
        }

        return $result;
    }
}
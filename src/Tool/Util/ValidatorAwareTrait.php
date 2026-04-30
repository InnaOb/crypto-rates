<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Util;

use CryptoRate\FrameworkLayer\Exception\ValidationCustomException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait ValidatorAwareTrait
{
    protected readonly ValidatorInterface $validator;

    #[Required]
    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    protected function validate(mixed $value, array|Constraint|null $constraints = null): ConstraintViolationListInterface
    {
        return $this->validator->validate($value, $constraints);
    }

    protected function validateAndThrow(mixed $value, array|Constraint|null $constraints = null): void
    {
        $violations = $this->validate($value, $constraints);

        if ($violations->count() > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $messages[] = sprintf('%s: %s', $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ValidationCustomException(
                message: implode('; ', $messages),
            );
        }
    }
}

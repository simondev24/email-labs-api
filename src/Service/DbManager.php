<?php

namespace App\Service;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Result;
use Doctrine\ORM\EntityManagerInterface;

class DbManager
{
    private const PARAMETER_TYPES = [
        'string' => ParameterType::STRING,
        'integer' => ParameterType::INTEGER,
        'boolean' => ParameterType::BOOLEAN,
        'array' => ParameterType::STRING,
    ];
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    /**
     * @throws Exception
     */
    public function getMaximumIdForContacts(): int
    {
        $sql = "SELECT MAX(id) FROM contacts";
        $statement = $this->entityManager->getConnection()->prepare($sql);
        $maxContactId = $statement->executeQuery()->fetchOne();
        return $maxContactId ?? 0;
    }

    /**
     * @throws Exception
     */
    public function executeQuery(string $sqlQuery, array $queryParameters): Result {
        $statement = $this->entityManager->getConnection()->prepare($sqlQuery);
        foreach ($queryParameters as $parameter => $value) {
            $statement->bindValue($parameter, $value, self::PARAMETER_TYPES[gettype($value)]);
        }
        return $statement->executeQuery();
    }

    public function executeWhereInQuery(string $sql, array $queryParameters, array $parameterType): Result {
        return $this->entityManager->getConnection()->executeQuery($sql,
            $queryParameters,
            $parameterType
        );
    }

}
<?php

declare(strict_types=1);

namespace App\Application\DAO;

use App\Application\Helpers\Connection;
use App\Application\Model\Exercise;
use App\Application\Helpers\Util;

class ExerciseDAO
{
    private const TABLE_NAME = 'exercise';

    /**
     * @var Connection $connection
     */
    private Connection $connection;

    public function __construct()
    {
        $this->connection = new Connection();
    }

    /**
     * @param array $data
     * @return Exercise|null
     */
    public function create(array $data): Exercise|null
    {
        $query = Util::prepareInsertQuery($data, self::TABLE_NAME);
        return ($this->connection->insert($query)) ? $this->getById($this->connection->getLastId()) : null;
    }

    /**
     * @param int $id
     * @return Exercise|null
     */
    public function getById(int $id): Exercise|null
    {
        return $this->connection
            ->select("SELECT * FROM exercise WHERE id = $id")
            ->fetch_object('App\Application\Model\Exercise');
    }

    /**
     * @return Exercise[]
     */
    public function getExercises(): array
    {
        $exercises = [];
        $result = $this->connection->select("SELECT id FROM exercise ORDER BY name");
        while ($row = $result->fetch_array()) {
            $exercises[] = $this->getById(intval($row['id']));
        }

        return $exercises;
    }

    /**
     * @param string $name
     * @return Exercise[]
     */
    public function getExercisesByName(string $name): array
    {
        $exercises = [];
        $result = $this->connection->select("SELECT id FROM exercise WHERE name LIKE '%$name%' ORDER BY name");
        while ($row = $result->fetch_assoc()) {
            $exercises[] = $this->getById(intval($row['id']));
        }
        return $exercises;
    }

    /**
     * @param int $muscleId
     * @return Exercise[]
     */
    public function getExercisesByMuscle(int $muscleId): array
    {
        $exercises = [];
        $result = $this->connection
            ->select("SELECT id FROM exercise WHERE muscle_id = $muscleId ORDER BY name");
        while ($row = $result->fetch_assoc()) {
            $exercises[] = $this->getById(intval($row['id']));
        }
        return $exercises;
    }

    /**
     * @param int $id
     * @param array $data
     * @return Exercise|null
     */
    public function edit(int $id, array $data): Exercise|null
    {
        $query = Util::prepareUpdateQuery($id, $data, self::TABLE_NAME);
        return ($this->connection->update($query)) ? $this->getById($id) : null;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = Util::prepareDeleteQuery($id, self::TABLE_NAME);
        return $this->connection->delete($query);
    }
}

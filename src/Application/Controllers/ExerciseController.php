<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\DAO\ExerciseDAO;
use App\Application\Model\Exercise;

class ExerciseController
{
    /**
     * @var ExerciseDAO $expenseDAO
     */
    private ExerciseDAO $expenseDAO;

    public function __construct()
    {
        $this->expenseDAO = new ExerciseDAO();
    }

    /**
     * @param array $data
     * @return Exercise|null
     */
    public function create(array $data): Exercise|null
    {
        return $this->expenseDAO->create($data);
    }

    /**
     * @param int $id
     * @return Exercise|null
     */
    public function getById(int $id): Exercise|null
    {
        return $this->expenseDAO->getById($id);
    }

    /**
     * @return Exercise[]
     */
    public function getExercises(): array
    {
        return $this->expenseDAO->getExercises();
    }

    /**
     * @param string $name
     * @return Exercise[]
     */
    public function getExercisesByName(string $name): array
    {
        return $this->expenseDAO->getExercisesByName($name);
    }

    /**
     * @param int $muscleId
     * @return Exercise[]
     */
    public function getExercisesByMuscle(int $muscleId): array
    {
        return $this->expenseDAO->getExercisesByMuscle($muscleId);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Exercise|null
     */
    public function edit(int $id, array $data): Exercise|null
    {
        return $this->expenseDAO->edit($id, $data);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->expenseDAO->delete($id);
    }
}

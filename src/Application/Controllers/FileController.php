<?php

declare(strict_types=1);

namespace App\Application\Controllers;
use App\Application\Model\File;
use App\Application\DAO\FileDAO;

class FileController
{
    /**
     * @var FileDAO
     */
    private FileDAO $fileDAO;

    public function __construct()
    {
        $this->fileDAO = new FileDAO();
    }

    /**
     * @param array $data
     * @return File|null
     */
    public function create(array $data): File|null
    {
        return $this->fileDAO->create($data);
    }

    /**
     * @param int $id
     * @return File|null
     */
    public function getById(int $id): File|null
    {
        return $this->fileDAO->getById($id);
    }

    /**
     * @param int $memberId
     * @return array
     */
    public function getByMember(int $memberId)
    {
        return $this->fileDAO->getByMember($memberId);
    }

    /**
     * @param int $id
     * @param array $data
     * @return File|null
     */
    public function edit(int $id, array $data): File|null
    {
        return $this->fileDAO->edit($id, $data);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->fileDAO->delete($id);
    }
}
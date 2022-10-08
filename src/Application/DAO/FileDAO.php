<?php

declare(strict_types=1);

namespace App\Application\DAO;

use App\Application\Helpers\Connection;
use App\Application\Helpers\Util;
use App\Application\Model\File;

class FileDAO
{
    private const TABLE_NAME = 'file';

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
     * @return File|null
     */
    public function create(array $data): File|null
    {
        $query = Util::prepareInsertQuery($data, self::TABLE_NAME);
        return ($this->connection->update($query)) ? $this->getById($this->connection->getLastId()) : null;
    }

    /**
     * @param int $id
     * @return File
     */
    public function getById(int $id): File
    {
        return $this->connection
            ->select("SELECT * FROM file WHERE id = $id")
            ->fetch_object('App\Application\Model\File');
    }

    /**
     * @param int $memberId
     * @return array
     */
    public function getByMember(int $memberId): array
    {
        $receipts = [];
        $result = $this->connection->select("SELECT id FROM file WHERE member_id = $memberId ORDER BY date");
        while ($row = $result->fetch_assoc()) {
            $receipts[] = $this->getById(intval($row['id']));
        }
        return $receipts;
    }

    /**
     * @param int $id
     * @param array $data
     * @return File|null
     */
    public function edit(int $id, array $data): File|null
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

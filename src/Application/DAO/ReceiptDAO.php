<?php

declare(strict_types=1);

namespace App\Application\DAO;

use App\Application\Helpers\Connection;
use App\Application\Helpers\Util;
use App\Application\Helpers\EmailTemplate;
use App\Application\Model\Receipt;

class ReceiptDAO
{
    private const TABLE_NAME = 'receipt';

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
     * @return Receipt|null
     */
    public function create(array $data): Receipt|null
    {
        $query = Util::prepareInsertQuery($data, self::TABLE_NAME);
        return ($this->connection->update($query)) ? $this->getById($this->connection->getLastId()) : null;
    }

    /**
     * @param int $id
     * @return Receipt
     */
    public function getById(int $id): Receipt
    {
        return $this->connection
            ->select("SELECT * FROM receipt WHERE id = $id")
            ->fetch_object('App\Application\Model\Receipt');
    }

    /**
     * @param int $memberId
     * @return array
     */
    public function getByMember(int $memberId): array
    {
        $receipts = [];
        $result = $this->connection->select("SELECT id FROM receipt WHERE member_id = $memberId ORDER BY date");
        while ($row = $result->fetch_assoc()) {
            $receipts[] = $this->getById(intval($row['id']));
        }
        return $receipts;
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getByUser(int $userId): array
    {
        $receipts = [];
        $result = $this->connection->select("SELECT id FROM receipt WHERE user_id = $userId ORDER BY date");
        while ($row = $result->fetch_assoc()) {
            $receipts[] = $this->getById(intval($row['id']));
        }
        return $receipts;
    }

    /**
     * @param int $id
     * @param array $data
     * @return Receipt|null
     */
    public function edit(int $id, array $data): Receipt|null
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

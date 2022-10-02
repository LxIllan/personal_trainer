<?php

declare(strict_types=1);

namespace App\Application\DAO;

use App\Application\Helpers\Connection;
use App\Application\Model\Membership;
use App\Application\Helpers\Util;

class MembershipDAO
{
    private const TABLE_NAME = 'membership';

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
     * @return Membership|null
     */
    public function create(array $data): Membership|null
    {
        $query = Util::prepareInsertQuery($data, self::TABLE_NAME);
        return ($this->connection->insert($query)) ? $this->getById($this->connection->getLastId()) : null;
    }

    /**
     * @param int $id
     * @return Membership|null
     */
    public function getById(int $id): Membership|null
    {
        return $this->connection
            ->select("SELECT * FROM membership WHERE id = $id")
            ->fetch_object('App\Application\Model\Membership');
    }

    /**
     * @param int $id
     * @param array $data
     * @return Membership|null
     */
    public function edit(int $id, array $data): Membership|null
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

    /**
     * @return Membership[]
     */
    public function getMemberships(): array
    {
        $categories = [];
        $result = $this->connection->select("SELECT id FROM membership");
        while ($row = $result->fetch_assoc()) {
            $categories[] = $this->getById(intval($row['id']));
        }
        return $categories;
    }    
}

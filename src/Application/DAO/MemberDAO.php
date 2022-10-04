<?php

declare(strict_types=1);

namespace App\Application\DAO;

use App\Application\Helpers\Connection;
use App\Application\Helpers\Util;
use App\Application\Model\Member;

class MemberDAO
{
    private const TABLE_NAME = 'member';

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
     * @return Member|null
     */
    public function create(array $data): Member|null
    {
        $data["hash"] = password_hash($data["password"], PASSWORD_DEFAULT);
        unset($data["password"]);
        $query = Util::prepareInsertQuery($data, self::TABLE_NAME);
        return ($this->connection->insert($query)) ? $this->getMemberById($this->connection->getLastId()) : null;
    }

    /**
     * @param int $id
     * @return Member|null
     */
    public function getMemberById(int $id): Member|null
    {
        $member = $this->connection
            ->select("SELECT * FROM member WHERE id = $id")
            ->fetch_object('App\Application\Model\Member');
        unset($member->hash);
        return $member;
    }

    public function getSiguienteId(): int
    {
        $row = $this->connection->select("SELECT AUTO_INCREMENT FROM "
            . "INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'member'")->fetch_array();
        return $row[0];
    }

    /**
     * @param int $id
     * @param array $data
     * @return Member|null
     */
    public function edit(int $id, array $data): Member|null
    {
        $query = Util::prepareUpdateQuery($id, $data, self::TABLE_NAME);
        return ($this->connection->update($query)) ? $this->getMemberById($id) : null;
    }

    /**
     * @param int $id
     * @return Member|null
     */
    public function delete(int $id): Member|null
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            "deleted" => 1,
            "deleted_at" => $now
        ];
        $query = Util::prepareUpdateQuery($id, $data, self::TABLE_NAME);
        return ($this->connection->update($query)) ? $this->getMemberById($id) : null;
    }

    /**
     * @return Member[]
     */
    public function getMembers(): array
    {
        $customers = [];
        $query = <<<EOF
            SELECT id
            FROM member
            WHERE deleted = 0
        EOF;

        $result = $this->connection->select($query);
        while ($row = $result->fetch_array()) {
            $customers[] = $this->getMemberById(intval($row['id']));
        }

        return $customers;
    }

    /**
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public function validateSession(string $email, string $password): array|null
    {
        $query = <<<EOF
            SELECT id, branch_id, hash, root
            FROM user
            WHERE email LIKE '$email' 
                AND email = '$email' 
                AND enabled = 1
        EOF;

        $result = $this->connection->select($query);

        if ($result->num_rows == 1) {
            $data = $result->fetch_assoc();
            if (password_verify($password, $data['hash'])) {
                unset($data['hash']);
                return $data;
            }
            return null;
        }
        return null;
    }

    /**
     * @param int $customerId
     * @param string $password
     * @return Member|null
     */
    public function resetPassword(int $customerId, string $password): Member|null
    {
        $data = [];
        $data["hash"] = password_hash($password, PASSWORD_DEFAULT);
        return $this->edit($customerId, $data);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function existEmail(string $email): bool
    {
        $row = $this->connection->select("SELECT email FROM user WHERE email = '$email'")->fetch_assoc();
        return (isset($row) && Util::validateEmail($row['email']));
    }
}

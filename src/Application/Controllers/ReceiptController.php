<?php

declare(strict_types=1);

namespace App\Application\Controllers;
use App\Application\Model\Receipt;
use App\Application\DAO\ReceiptDAO;

class ReceiptController
{
    /**
     * @var ReceiptDAO
     */
    private ReceiptDAO $receiptDAO;

    public function __construct()
    {
        $this->receiptDAO = new ReceiptDAO();
    }

    /**
     * @param array $data
     * @return Receipt|null
     */
    public function create(array $data): Receipt|null
    {
        return $this->receiptDAO->create($data);
    }

    /**
     * @param int $id
     * @return Receipt|null
     */
    public function getById(int $id): Receipt|null
    {
        return $this->receiptDAO->getById($id);
    }

    /**
     * @param int $memberId
     * @return array
     */
    public function getByMember(int $memberId)
    {
        return $this->receiptDAO->getByMember($memberId);
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getByUser(int $userId)
    {
        return $this->receiptDAO->getByUser($userId);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Receipt|null
     */
    public function edit(int $id, array $data): Receipt|null
    {
        return $this->receiptDAO->edit($id, $data);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->receiptDAO->delete($id);
    }
}
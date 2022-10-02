<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Model\Membership;
use App\Application\DAO\MembershipDAO;

class MembershipController
{
    /**
     * @var MembershipDAO $membershipDAO
     */
    private MembershipDAO $membershipDAO;

    public function __construct()
    {
        $this->membershipDAO = new MembershipDAO();
    }

    /**
     * @param array $data
     * @return Membership|null
     */
    public function create(array $data): Membership|null
    {
        return $this->membershipDAO->create($data);
    }

    /**
     * @param int $id
     * @return Membership|null
     */
    public function getById(int $id): Membership|null
    {
        return $this->membershipDAO->getById($id);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Membership|null
     */
    public function edit(int $id, array $data): Membership|null
    {
        return $this->membershipDAO->edit($id, $data);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->membershipDAO->delete($id);
    }

    /**
     * @return Membership[]
     */
    public function getMemberships(): array
    {
        return $this->membershipDAO->getMemberships();
    }
}

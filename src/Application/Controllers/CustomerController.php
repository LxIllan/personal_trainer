<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Helpers\EmailTemplate;
use App\Application\DAO\CustomerDAO;
use App\Application\Model\Customer;
use App\Application\Helpers\Util;
use Exception;

class CustomerController
{
    /**
     * @var CustomerDAO $userDAO
     */
    private CustomerDAO $userDAO;

    public function __construct()
    {
        $this->userDAO = new CustomerDAO();
    }

    /**
     * @param array $data
     * @return Customer|null
     * @throws Exception
     */
    public function create(array $data): Customer|null
    {
        $password = Util::generatePassword();
        $data['password'] = $password;
        $user = $this->userDAO->create($data);
        if ($user) {
            $branchController = new BranchController();
            $branch = $branchController->getById(intval($data['branch_id']));
            $dataToSendEmail = [
                'subject' => "Bienvenido a $branch->name",
                'email' => $user->email,
                'branch_name' => $branch->name,
                'branch_location' => $branch->location,
                'password' => $password,
                'username' => "$user->name $user->last_name"
            ];
            if (!Util::sendMail($dataToSendEmail, EmailTemplate::PASSWORD_TO_NEW_USER)) {
                throw new Exception('Error to send password to new user.');
            }
        }
        return $user;
    }

    /**
     * @param int $id
     * @return Customer
     */
    public function getCustomerById(int $id): Customer
    {
        return $this->userDAO->getCustomerById($id);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Customer|null
     */
    public function edit(int $id, array $data): Customer|null
    {
        return $this->userDAO->edit($id, $data);
    }

    /**
     * @param int $id
     * @return Customer|null
     */
    public function delete(int $id): Customer|null
    {
        return $this->userDAO->delete($id);
    }

    /**
     * @return Customer[]
     */
    public function getCustomers(): array
    {
        return $this->userDAO->getCustomers();
    }

    /**
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public function validateSession(string $email, string $password): array|null
    {
        if (!Util::validateEmail($email)) {
            throw new Exception('Invalid email');
        }
        return $this->userDAO->validateSession($email, $password);
    }

    /**
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public function resetPassword(int $userId): bool
    {
        $password = Util::generatePassword();
        $user = $this->userDAO->resetPassword($userId, $password);
        if ($user) {
            $branchController = new BranchController();
            $branch = $branchController->getById(intval($user->branch_id));
            $dataToSendEmail = [
                'subject' => "Restablecer contraseña - $branch->name",
                'email' => $user->email,
                'branch_name' => $branch->name,
                'password' => $password,
                'user_name' => "$user->name"
            ];
            if (!Util::sendMail($dataToSendEmail, EmailTemplate::RESET_PASSWORD)) {
                throw new Exception('Error to send password to new user.');
            }
            return true;
        }
        return false;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function existEmail(string $email): bool
    {
        return $this->userDAO->existEmail($email);
    }

    public function getSiguienteId() {
        return $this->userDAO->getSiguienteId();
    }
}
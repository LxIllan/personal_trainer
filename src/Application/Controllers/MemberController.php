<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Helpers\EmailTemplate;
use App\Application\Controllers\ReceiptController;
use App\Application\Controllers\MembershipController;
use App\Application\DAO\MemberDAO;
use App\Application\Model\Member;
use App\Application\Helpers\Util;
use Exception;

class MemberController
{
    /**
     * @var MemberDAO $memberDAO
     */
    private MemberDAO $memberDAO;

    public function __construct()
    {
        $this->memberDAO = new MemberDAO();
    }

    /**
     * @param array $data
     * @return Member|null
     * @throws Exception
     */
    public function create(array $data): Member|null
    {
        $membershipController = new MembershipController();
        $membership = $membershipController->getById(intval($data['membership_id']));
        $today = date('Y-m-d H:i:s');
        $endMembership = Util::addTimeToDate($today, intval($membership->months), intval($membership->weeks));
        $password = Util::generatePassword();
        $data['password'] = $password;
        $data['end_membership_date'] = $endMembership;
        $userId = $data['user_id'];
        unset($data['user_id']);
        $member = $this->memberDAO->create($data);
        if ($member) {
            $dataToSendEmail = [
                'subject' => "Bienvenido a GYM",
                'email' => $member->email,
                'password' => $password,
                'username' => "$member->name $member->last_name"
            ];
            if (!Util::sendMail($dataToSendEmail, EmailTemplate::PASSWORD_TO_NEW_USER)) {
                throw new Exception('Error to send password to new user.');
            }
            $receiptController = new ReceiptController();
            $receipt = $receiptController->create([
                'user_id' => $userId,
                'member_id' => $member->id,
                'membership_id' => $data['membership_id'],
                'price' => $membership->price
            ]);            
            if ($receipt) {
                $dataToSendEmail = [
                    'username' => "$member->name $member->last_name",
                    'date' => $today,
                    'membership' => $membership->membership,
                    'price' => $membership->price,
                    'endMembershipDate' => $endMembership
                ];
                if (!Util::sendMail($dataToSendEmail, EmailTemplate::RECEIPT)) {
                    throw new Exception('Error to send receipt to user.');
                }       
            }
        }
        return $member;
    }

    /**
     * @param int $id
     * @return Member|null
     */
    public function getMemberById(int $id): Member|null
    {
        return $this->memberDAO->getMemberById($id);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Member|null
     */
    public function edit(int $id, array $data): Member|null
    {
        return $this->memberDAO->edit($id, $data);
    }

    /**
     * @param int $id
     * @return Member|null
     */
    public function delete(int $id): Member|null
    {
        return $this->memberDAO->delete($id);
    }

    /**
     * @return Member[]
     */
    public function getMembers(): array
    {
        return $this->memberDAO->getMembers();
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
        return $this->memberDAO->validateSession($email, $password);
    }

    /**
     * @param int $memberId
     * @return bool
     * @throws Exception
     */
    public function resetPassword(int $memberId): bool
    {
        $password = Util::generatePassword();
        $member = $this->memberDAO->resetPassword($memberId, $password);
        if ($member) {
            $dataToSendEmail = [
                'subject' => "Restablecer contraseña - GYM",
                'email' => $member->email,
                'password' => $password,
                'user_name' => "$member->name"
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
        return $this->memberDAO->existEmail($email);
    }

    public function getSiguienteId() {
        return $this->memberDAO->getSiguienteId();
    }
}
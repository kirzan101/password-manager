<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\UserDTO;

interface UserInterface
{
    /**
     * Store a new user in the database.
     *
     * @param UserDTO $userDTO
     * @return ModelResponse
     */
    public function storeUser(UserDTO $userDTO): ModelResponse;

    /**
     * update an existing user in the database.
     *
     * @param UserDTO $userDTO
     * @param integer $userId
     * @return ModelResponse
     */
    public function updateUser(UserDTO $userDTO, int $userId): ModelResponse;

    /**
     * delete a user from the database.
     *
     * @param integer $userId
     * @return ModelResponse
     */
    public function deleteUser(int $userId): ModelResponse;
}

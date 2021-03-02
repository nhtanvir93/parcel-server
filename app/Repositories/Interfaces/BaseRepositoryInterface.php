<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);
}
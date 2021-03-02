<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 8/3/2020
 * Time: 11:26 PM
 */

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);
}
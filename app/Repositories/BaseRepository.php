<?php

namespace App\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $data) {
        return $this->model->create($data)->id;
    }

    public function update(array $data, $id) {
        $updated = $this->model->find($id)->update($data);
        return $updated ? $id : false;
    }

    public function delete($id) {
        return $this->model->destroy($id);
    }
}
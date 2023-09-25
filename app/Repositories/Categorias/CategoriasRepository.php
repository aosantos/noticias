<?php

namespace App\Repositories\Categorias;

use App\Models\Categoria;
use App\Repositories\BaseRepository;

class CategoriasRepository extends BaseRepository
{

    /**
     * @var array
     */
    protected $fieldSearchable = [
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Categoria::class;
    }

    /**
     * Find model record for given id
     *
     * @param  int  $id
     * @param  array  $columns
     */
    public function find($id, $columns = ['*'])
    {
        $query = $this
            ->model
            ->newQuery();

        return $query->find($id, $columns);
    }


}

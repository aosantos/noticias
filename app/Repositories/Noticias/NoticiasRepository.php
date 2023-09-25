<?php

namespace App\Repositories\Noticias;

use App\Models\Noticia;
use App\Repositories\BaseRepository;

class NoticiasRepository extends BaseRepository
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
        return Noticia::class;
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

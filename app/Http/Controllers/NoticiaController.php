<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Repositories\Noticias\NoticiasRepository;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class NoticiaController extends Controller
{
    public function __construct(private readonly NoticiasRepository $noticiasRepository)
    {

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->bearerToken()) {
            $search = $request->get('search');

            return Noticia::with('categoria')
                ->when($search, function ($query, $search) {
                    return $query->where('titulo', 'like', "%{$search}%");
                })
                ->paginate(15);
        }
        if ($request->ajax()) {
            $data = Noticia::with('categoria')->select(['id', 'titulo', 'categoria_id','data as data_publicacao']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }

        return view('noticias/index');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'data' => 'required|date',
            'conteudo' =>'required|string',
            'categoria_id' =>'required|string'
        ]);
        $input = $request->all();
        $this->noticiasRepository->create($input);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->noticiasRepository->find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->noticiasRepository->find($id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     * @throws \Exception
     */
    public function destroy(string $id)
    {
        $this->noticiasRepository->delete($id);
    }
}

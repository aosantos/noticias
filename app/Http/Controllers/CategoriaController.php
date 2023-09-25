<?php

namespace App\Http\Controllers;
use App\Models\Categoria;
use App\Repositories\Categorias\CategoriasRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class CategoriaController extends Controller
{
    public function __construct(private readonly CategoriasRepository $categoriasRepository)
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->bearerToken()) {
            return  $this->categoriasRepository->all();
        }

        if ($request->ajax()) {
            $data =  $this->categoriasRepository->all();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }

        return view('categorias/index');
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
  {
    $request->validate([
        'nome' => [
            'required',
            'string',
            'max:255',
            Rule::unique('categorias', 'nome'), // Verifica se o nome é único na tabela 'categorias'
        ],
    ]);
    $input = $request->all();
    $this->categoriasRepository->create($input);
 }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->categoriasRepository->find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->categoriasRepository->find($id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        if (!auth()->user()->can('delete categorias')) {
            abort(403, 'Você não tem permissão para excluir categorias.');
        }

       return $categoria->delete();

    }
}

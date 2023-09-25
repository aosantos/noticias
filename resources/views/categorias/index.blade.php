<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Categorias</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

</head>
<body>
<div class="container mt-5">
    <h1>Lista de Categorias</h1>
    <table id="categorias-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
        </thead>
    </table>
</div>

<!-- Botão para abrir a modal de adição -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createCategoriaModal">Adicionar Categoria</button>

<!-- Modal de criação de categoria -->
<div class="modal fade" id="createCategoriaModal" tabindex="-1" role="dialog" aria-labelledby="createCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoriaModalLabel">Adicionar Categoria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="info" style="display: none"></div>
            <div class="modal-body">
                <!-- Formulário para adicionar categoria -->
                <form id="create-categoria-form">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" class="form-control" id="nome" name="nome">
                    </div>
                    <!-- Outros campos do formulário -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="save-create-categoria-button">Salvar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal de confirmação de exclusão de categoria -->
<div class="modal fade" id="deleteCategoriaModal" tabindex="-1" role="dialog" aria-labelledby="deleteCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoriaModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Tem certeza de que deseja excluir esta categoria?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-categoria-button">Excluir</button>
            </div>
        </div>
    </div>
</div>


<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Bootstrap 4 JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    $(document).ready(function() {
        var table = $('#categorias-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/categorias',
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nome', name: 'nome' },
                {
                    data: null,
                    className: "center",
                    render: function(data, type, row) {
                        return '<a href="#" class="delete-categoria" data-id="' + row.id + '">Excluir</a>';
                    }
                }
            ],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
            }
        });

        // Quando o botão de adição na tabela é clicado:
        $('#createCategoriaModal').on('show.bs.modal', function (e) {
            $('#create-categoria-form')[0].reset();
        });

        $('#save-create-categoria-button').click(function() {
            var formData = $('#create-categoria-form').serialize();
            $.ajax({
                url: '/categorias',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#createCategoriaModal').modal('hide');
                    $('#categorias-table').DataTable().ajax.reload();
                },
                error: function(error) {
                    if (error.status === 422) {
                        let erros = error.responseJSON.errors, validacoes = [];

                        for (erro = 0; erro < Object.keys(erros).length; erro++) {
                            let campo = Object.keys(erros)[erro];
                            for (i = 0; i < erros[campo].length; i++) {
                                validacoes.push(erros[campo][i]);
                            }
                        }
                        $("#info").html("");
                        $("#info").append('<div class="alert alert-danger">' + validacoes.join('<br>') + '</div>');
                        $("#info").show();
                    }
                }
            });
        });


        // Quando o botão de exclusão na tabela é clicado:
        $('#categorias-table').on('click', '.delete-categoria', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('#confirm-delete-categoria-button').data('id', id);
            $('#deleteCategoriaModal').modal('show');
        });

        // Quando o botão de confirmação de exclusão na modal é clicado:
        $('#confirm-delete-categoria-button').click(function () {
            var id = $('#confirm-delete-categoria-button').data('id');
            var token = "{{ csrf_token() }}"
            $.ajax({
                url: '/categorias/' + id,
                type: 'DELETE',
                data: {
                    "_token": token
                },
                success: function (data) {
                    $('#categorias-table').DataTable().ajax.reload();
                    $('#deleteCategoriaModal').modal('hide');
                },
                error: function (xhr, status, error) {
                    var message = (xhr.status === 403)
                        ? 'Você não tem permissão para excluir categorias.'
                        : 'Ocorreu um erro ao excluir a categoria.';

                    Swal.fire('Erro', message, 'error');
                }
            });
        });

    });
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Notícias</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

</head>
<body>

<div class="container mt-5">
    <h1>Lista de Notícias</h1>
    <table id="noticias-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Categoria</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>
        </thead>
    </table>

    <!-- Botão para abrir a modal de adição -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createNoticiaModal">Adicionar Notícia</button>


    <!-- Modal de criação -->
    <div class="modal fade" id="createNoticiaModal" tabindex="-1" role="dialog" aria-labelledby="createNoticiaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createNoticiaModalLabel">Adicionar Notícia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="info" style="display: none"></div>
                <div class="modal-body">
                    <!-- Formulário para adicionar notícia -->
                    <form id="create-noticia-form">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label for="titulo">Título:</label>
                            <input type="text" class="form-control" id="titulo" name="titulo">
                        </div>
                        <div class="form-group">
                            <label for="data">Data:</label>
                            <input type="date" class="form-control" id="data" name="data">
                        </div>
                        <div class="form-group">
                            <label for="conteudo">Conteúdo:</label>
                            <textarea class="form-control" id="conteudo" name="conteudo"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="categoria_id">Categoria ID:</label>
                            <input type="text" class="form-control" id="categoria_id" name="categoria_id">
                        </div>
                        <!-- Outros campos do formulário -->
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="save-create-noticia-button">Salvar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para confirmar a exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tem certeza de que deseja excluir esta notícia?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-button">Excluir</button>
                </div>
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

<!-- ... (código anterior) -->

<script>
    $(document).ready(function() {
        var table = $('#noticias-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/noticias',
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'titulo', name: 'titulo' },
                {
                    data: 'categoria',
                    name: 'categoria.nome',
                    render: function(data, type, row) {
                        return data ? data.nome : '';
                    },
                    defaultContent: ''
                },
                { data: 'data_publicacao', name: 'data' },
                {
                    data: null,
                    className: "center",

                    render: function(data, type, row) {
                        return '<a href="#" class="delete" data-id="' + row.id + '">Excluir</a>';
                    }
                }

            ],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                // Traduzindo os elementos de DataTables para Português
                url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
            }
        });

        $(document).ready(function() {
            $('#save-create-noticia-button').click(function() {
                // Obtenha os dados do formulário de criação
                var formData = $('#create-noticia-form').serialize();

                // Faça uma requisição AJAX para a rota de criação (store) com os dados do formulário
                $.ajax({
                    url: '/noticias',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // A notícia foi criada com sucesso, você pode fazer o que for necessário aqui
                        $('#createNoticiaModal').modal('hide'); // Esconde a modal de criação após a criação
                        $('#noticias-table').DataTable().ajax.reload(); // Recarrega a tabela de notícias
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
        });

        // Quando o botão de exclusão na tabela é clicado:
        $('#noticias-table').on('click', '.delete', function (e) {
            e.preventDefault(); // Impede o evento padrão (navegar para outra página)

            var id = $(this).data('id'); // Obtém o ID da notícia

            // Define a URL de exclusão com base no ID da notícia
            var deleteUrl = '/noticias/' + id;

            // Exibe a modal de confirmação
            $('#deleteModal').data('id', id).data('delete-url', deleteUrl).modal('show');
        });

        // Quando o botão de exclusão na modal é clicado:
        $('#confirm-delete-button').click(function () {
            var id = $('#deleteModal').data('id');
            var token = "{{ csrf_token() }}"
            var deleteUrl = $('#deleteModal').data('delete-url');

            // Faça uma requisição AJAX para excluir a notícia
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                data: {
                    "_token": token
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    // A notícia foi excluída com sucesso, você pode atualizar a tabela de notícias aqui
                    $('#noticias-table').DataTable().ajax.reload();
                    $('#deleteModal').modal('hide'); // Esconde a modal após a exclusão
                },
                error: function (xhr, status, error) {
                    // Lidar com erros de exclusão, se necessário
                }
            });
        });
    });
</script>

</body>
</html>

@extends('layouts.main_layout')
@section('titulo', 'Listar')
@section('pagina', 'Listagem')
@section('conteudo')
    <div class="container">
        <hr>
        <div class="container">
            @if ($registro > 0)
                <div class="alert alert-primary">
                    <p>Numero de registros: {{ $registro }}</p>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Data de criação</th>
                            <th scope="col">Data de modificação</th>
                            <th scope="col">Download</th>
                            <th scope="col">Alterar dados</th>
                            <th scope="col">Excluir dados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $documento)
                            <tr>
                                <td>{{ $documento->nome }}</td>
                                <td>{{ $documento->created_at }}</td>
                                <td>{{ $documento->updated_at }}</td>

                                <td>
                                    <form action="{{ Route('documento_download', [$documento->id]) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $documento->id }}">
                                        <input class="btn btn-primary" type="submit" value="Download">
                                    </form>
                                </td>

                                <td>
                                    <form action="{{ Route('documento_update', [$documento->id]) }}" method="get">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $documento->id }}">
                                        <input class="btn btn-primary" type="submit" value="Alterar">
                                    </form>
                                </td>

                                <td>
                                    <form action="{{ Route('documento_delete', [$documento->id]) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $documento->id }}">
                                        <input class="btn btn-primary" type="submit" value="Excluir">
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-danger">
                    <p>Nenhum registro encontrado.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

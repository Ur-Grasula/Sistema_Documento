@extends('layouts.main_layout')
@section('titulo', 'Update')
@section('pagina', 'Update')
@section('conteudo')
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form action="{{ Route('documento_update_validate', [$data['id']]) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="text_nome" class="form-label">Nome: </label>
            <input type="text" name="text_nome" id="text_nome" class="form-control"
                value="{{ old('text_nome', $data['nome']) }}" placeholder="Nome">
        </div>

        <div class="mb-3">
            <label for="text_email" class="form-label">Arquivo: </label>
            <input type="file" class="form-control" name="documento">
            <small>(Caso nenhum arquivo seja selecionado o arquivo antigo será mantido.)</small>
        </div>

        <input type="submit" value="Salvar" class="btn btn-primary">
        <a href="{{ Route('documento_read') }}" class="btn btn-primary">Cancelar</a>
    </form>
    </div>
@endsection

@extends('layouts.main_layout')
@section('titulo', 'Erro')
@section('pagina', 'Erro')
@section('conteudo')
    <div class="alert alert-danger">
        <h2 class="text-center">{{ $exception->getMessage() }}</h2>
    </div>
@endsection

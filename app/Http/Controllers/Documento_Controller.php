<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Documento_Controller extends Controller
{

    // FUNÇÃO UTILIZADA PARA TESTE RAPIDO
    public function Index(Request $request)
    {

        // TESTE DE DEFINÇÃO DE HORARIO
        // date_default_timezone_set('America/Sao_Paulo');

        // echo "HORARIO NOW(): ".NOW();
        // echo "<p>";
        // echo "HORARIO CARBON NOW(): ".Carbon::NOW();
        // echo "<p>";
        // echo "HORARIO DATE: ".DATE('Y-m-d H:i:s');

        return redirect()->route('listar');
    }

    // FUNÇÃO RESPONSAVEL POR LISTAR DOCUMENTOS
    public function Listar()
    {
        $data[] = [];

        try {

            if (Documento::count() >= 1) {
                $registro = Documento::count();
                $data = Documento::orderBy('created_at', 'DESC')->get();
            } else {
                $registro = Documento::count();
            }

            return view('listar', ["data" => $data, "registro" => $registro]);
        } catch (\Exception $exception) {
            // return $exception->getMessage();
            abort(404);
        }

        return view('listar', ["data" => $data, "registro" => $registro]);
    }

    // FUNÇÃO RESPONSAVEL PELO DOWNLOAD DO DOCUMENTO
    public function Download(Request $request, int $id)
    {
        try {
            if ($request->isMethod('post')) {
                if (Documento::Where('id', '=', $id)->count() == 1) {
                    $data = Documento::Where('id', '=', $id)->get();
                    $nome = $data[0]['nome'];
                    $extensao = $data[0]['extensao'];
                    $documento = $data[0]['documento'];
                    return Storage::disk('documento')->Download($documento, $nome . '.' . $extensao);
                }
            } else {
                return redirect()->route('listar');
            }
        } catch (\Exception $exception) {
            abort(404);
        }
    }

    // FUNÇÃO RESPONSAVEL POR EXIBIR FORMULARIO DE UPLOAD DE ARQUIVOS
    public function Upload()
    {
        return view('formulario_upload');
    }

    // FUNÇÃO RESPONSAVEL POR VALIDAR DADOS DE UPLOAD
    public function Upload_Submit_Validate(Request $request)
    {
        try {

            if ($request->isMethod('post')) {

                $request->validate([
                    'text_nome' => ['required', 'min:3', 'max:50'],
                    'documento' => ['required', 'mimes:ods,txt'],
                ], [
                    'text_nome.required' => "Campo nome Obrigatorio.",
                    'text_nome.min' => "Campo nome precisa ter pelo menos 3 catacteres.",
                    'text_nome.max' => "Campo nome pode ter no maximo 40 catacteres.",

                    'documento.required' => "Nenhum arquivo selecionado.",
                    'documento.mimes' => "O arquivo precisa ser do tipo (ods,txt).",
                ]);

                $nome = $request->input('text_nome');
                $file = $request->file('documento');
                $extensao = $request->file('documento')->getClientOriginalExtension();
                $nome_documento = Carbon::NOW()->format('Y-m-d-H-i-s-a') . rand() . "." . $extensao;

                return $this->Upload_Submit_Data($nome, $file, $extensao, $nome_documento);
            } else {
                return redirect()->route('upload');
            }
        } catch (\Exception $exception) {
            abort(404);
        }
    }

    // FUNÇÃO RESPONSAVEL PELO UPLOAD DE DADOS
    public function Upload_Submit_Data(string $nome, $file, string $extensao, string $nome_documento)
    {
        try {
            // GRAVAR DADOS NO BANCO DE DADOS
            $documento = new Documento;
            $documento->nome = $nome;
            $documento->documento = $nome_documento;
            $documento->extensao = $extensao;
            $documento->created_at = Carbon::NOW();
            $documento->updated_at = Carbon::NOW();

            if ($documento->save()) {
                // GRAVAR ARQUIVO NA PASTA
                if ($file->storeAs('', $nome_documento, ['disk' => 'documento'])) {
                    return redirect()->route('listar');
                }
            }
        } catch (\Exception $exception) {
            abort(404);
        }
    }

    // FUNÇÃO RESPONSAVAVEL POR DELETAR DADOS E ARQUIVO
    public function Delete(Request $request, int $id)
    {
        try {

            if ($request->isMethod('post')) {

                if (Documento::where('id', '=', $id)->count() == 1) {
                    $data = Documento::where('id', '=', $id)->get();
                    foreach ($data as $documento) {

                        // DELETAR ARQUIVO
                        Storage::disk('documento')->delete($documento->documento);

                        // DELETAR DADOS DO BANCO DE DADOS
                        $data = Documento::find($id);
                        $data->delete();

                        return redirect()->route('listar');
                    }
                }
            } else {
                return redirect()->route('listar');
            }
        } catch (\Exception $exception) {
            abort(404);
        }
    }

    //FUNÇÃO RESPONSAVEL PELO FORMULARIO DE UPDATE
    public function Update(Request $request, int $id)
    {
        try {

            if ($request->isMethod('get')) {

                if (Documento::Where('id', '=', $id)->count() == 1) {
                    $data = Documento::Where('id', '=', $id)->get();
                    return view('formulario_update', ["data" => $data]);
                } else {
                    return redirect()->route('listar');
                }
            } else {
                return redirect()->route('listar');
            }
        } catch (\Exception $exception) {
            abort(404);
        }
    }

    // FUNÇÃO RESPONSAVEL POR VALIDAR DADOS DE UPDATE
    public function Update_Submit_Validate(Request $request, int $id)
    {
        try {
            if ($request->isMethod('post')) {

                $request->validate([
                    'text_nome' => ['required', 'min:3', 'max:50'],
                    'documento' => ['mimes:ods,txt'],
                ], [
                    'text_nome.required' => "Campo nome Obrigatorio.",
                    'text_nome.min' => "Campo nome precisa ter pelo menos 3 catacteres.",
                    'text_nome.max' => "Campo nome pode ter no maximo 40 catacteres.",

                    'documento.mimes' => "O arquivo precisa ser do tipo (ods,txt).",
                ]);

                $nome = $request->input('text_nome');
                $file = $request->file('documento');

                return $this->Update_Submit_Data($id, $nome, $file);
            } else {
                return  redirect()->route('listar');
            }
        } catch (\Exception $exception) {
            abort(404);
        }
    }

    // FUNÇÃO RESPONSAVEL PELO UPDATE DE DATA
    public function Update_Submit_Data(int $id, string $nome, $file)
    {
        try {
            // SALVAR NO BANCO DE DADOS
            $documento = Documento::find($id);
            $documento->nome = $nome;

            if (isset($file)) {

                // GRAVA O NOME DO DOCUMENTO ANTIGO
                $documento_antigo = $documento->documento;

                // GERAR NOME PARA O DOCUMENTO
                $extensao = $file->getClientOriginalExtension();
                $nome_documento = Carbon::NOW()->format('Y-m-d-H-i-s-a') . rand() . "." . $extensao;

                $documento->documento = $nome_documento;
                $documento->extensao = $extensao;
            }

            $documento->updated_at = Carbon::NOW();

            if ($documento->save() && isset($file)) {

                // EXCLUI DOCUMENTO ANTIGO
                Storage::disk('documento')->delete($documento_antigo);

                // GRAVAR NOVO DOCUMENTO NA PASTA
                if ($file->storeAs('', $nome_documento, ['disk' => 'documento'])) {
                    return redirect()->route('listar');
                }
            }
            return redirect()->route('listar');
        } catch (\Exception $exception) {
            abort(404);
        }
    }

    // FUNÇÃO RESPONSAVEL POR PESQUISAR DADOS
    public function Search(Request $request)
    {
        try {
            if ($request->isMethod('get')) {
                $nome = $request->input('nome');

                $data[] = [];

                if (Documento::Where('nome', 'like', $nome . '%')->count() >= 1) {
                    $registro = Documento::Where('nome', 'like', $nome . '%')->count();
                    $data = Documento::orderBy('nome', 'ASC')->where('nome', 'like', $nome . '%')->get();
                } else {
                    $registro = Documento::Where('nome', 'like', $nome . '%')->count();
                }

                return view('search', ["data" => $data, "registro" => $registro]);
            } else {
                return redirect()->route('listar');
            }
        } catch (\Exception $exception) {
            abort(404);
        }
    }
}

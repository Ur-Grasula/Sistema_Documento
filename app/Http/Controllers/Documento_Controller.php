<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\Node\Block\Document;

class Documento_Controller extends Controller
{
    // CRUDL ===================================================================================

    // FUNÇÃO RESPONSAVEL POR SALVAR DADOS NO BANCO DE DADOS
    private function Create_Documento(string $nome, string $nome_documento, string $extensao)
    {
        try {
            $documento = new Documento;
            $documento->nome = $nome;
            $documento->documento = $nome_documento;
            $documento->extensao = $extensao;
            $documento->created_at = Carbon::NOW();
            $documento->updated_at = Carbon::NOW();
            $documento->save();
        } catch (\Exception $exception) {
            $erro_code = $exception->getCode();
            abort(404, $erro_code . " | Erro ao tentar salvar dados no banco de dados");
        }
    }

    // FUNÇÃO RESPONSAVEL POR BUSCAR DADOS NO BANCO DE DADOS
    private function Read_Documento($id)
    {
        try {
            $data['registro'] = Documento::count();
            $data['documento'] = Documento::find($id);
            return $data;
        } catch (\Exception $exception) {
            $erro_code = $exception->getCode();
            abort(404, $erro_code . " | Erro ao tentar recuperar dados ");
        }
    }

    // FUNÇÃO RESPONSAVEL POR ALTERAR DADOS NO BANCO DE DADOS
    private function Update_Documento($data_update)
    {
        try {
            $documento = Documento::find($data_update['id']);
            $documento->nome = $data_update['nome'];

            if (isset($data_update['documento']) && isset($data_update['extensao'])) {
                $documento->documento = $data_update['documento'];
                $documento->extensao = $data_update['extensao'];
            }

            $documento->Updated_at = Carbon::NOW();
            $documento->save();
        } catch (\Exception $exception) {
            $erro_code = $exception->getCode();
            abort(404, $erro_code . " | Erro ao tentar recuperar dados  ");
        }
    }

    // FUNÇÃO RESPONSAVEL POR DELETAR DADOS NO BANCO DE DADOS
    private function Delete_Documento(int $id)
    {
        try {
            $data = Documento::find($id);
            $data->delete();
        } catch (\Exception $exception) {
            $erro_code = $exception->getCode();
            abort(404, $erro_code . " | Erro ao tentar deletar dados ");
        }
    }

    private function List_Documento()
    {
        try {
            $data['registro'] = Documento::count();
            $data['documento'] = Documento::orderBy('created_at', 'DESC')->get();
            return $data;
        } catch (\Exception $exception) {
            $erro_code = $exception->getCode();
            abort(404, $erro_code . " | Erro ao tentar recuperar dados ");
        }
    }

    // CONTROLE DE DOCUMENTO ===================================================================

    // FUNÇÃO RESPONSAVEL POR GERAR NOME PARA O DOCUMENTO
    public function Gerar_Nome_Documento($file)
    {
        $extensao = $file->getClientOriginalExtension();

        try {
            do {
                $nome_documento = Carbon::NOW()->format('Y-m-d-H-i-s-a') . rand() . "." . $extensao;
            } while (Documento::where('documento', '=', $nome_documento)->count() >= 1);

            $nome_documento = [
                'nome_documento' => $nome_documento,
                'extensao' => $extensao
            ];

            return $nome_documento;
        } catch (\Exception $exception) {
            $erro_code = $exception->getCode();
            abort(404, $erro_code . " | Erro ao tentar gerar nome para documento documento ");
        }
    }

    // FUNÇÃO RESPONSAVEL POR SALVAR DOCUMENTO NA PASTA
    public function Salvar_Documento(string $nome_documento, $file)
    {
        try {
            $file->storeAs('', $nome_documento, ['disk' => 'documento']);
        } catch (\Exception $exception) {
            $erro_code = $exception->getCode();
            abort(404, $erro_code . " | Erro ao tentar armazenar documento ");
        }
    }

    // FUNÇÃO RESPONSAVEL POR DELETAR DOCUMENTO DA PASTA
    // NOTA - MENSAGEM DE ERRO NÃO SENDO RETORNADA
    public function Excluir_Documento(int $id)
    {
        try {
            if (Documento::where('id', '=', $id)->count() == 1) {

                $documento = Documento::where('id', '=', $id)->value('documento');
                Storage::disk('documento')->delete($documento);
            }
        } catch (\Exception $exception) {
            $erro_code = $exception->getCode();
            abort(404, $erro_code . " | Erro ao tentar excluir documento ");
        }
    }

    // FUNÇÃO RESPONSAVEL POR FAZER DOWNLOAD DO DOCUMENTO
    public function Download_Documento(int $id)
    {
        try {
            if (Documento::Where('id', '=', $id)->count() == 1) {
                $data = Documento::Where('id', '=', $id)->get();
                $nome = $data[0]['nome'];
                $extensao = $data[0]['extensao'];
                $documento = $data[0]['documento'];;
                return Storage::disk('documento')->Download($documento, $nome . '.' . $extensao);
            }
        } catch (\Exception $exception) {
            $erro_code = $exception->getCode();
            abort(404, $erro_code . " | Erro ao tentar fazer download de documento ");
        }
    }

    // VIEWS ===================================================================================

    // FUNÇÃO UTILIZADA PARA TESTE RAPIDO
    public function Index()
    {
        return redirect()->route('documento_read');
    }

    // FUNÇÃO RESPONSAVEL POR RETORNAR DOCUMENTOS PARA VIEW
    public function View_Documento_Read()
    {
        $data =  $this->List_Documento();
        return view('documento_read', ['data' => $data['documento'], 'registro' => $data['registro']]);
    }

    // FUNÇÃO RESPONSAVEL POR INCIAR DOWNLOAD
    public function View_Documento_Download(Request $request, int $id)
    {
        if ($request->isMethod('post')) {
            return $this->Download_Documento($id);
        } else {
            return redirect()->route('documento_read');
        }
    }

    // FUNÇÃO RESPONSAVEL POR EXIBIR FORMULARIO DE UPLOAD DE DOCUMENTO
    public function View_Documento_Upload()
    {
        return view('documento_formulario_upload');
    }

    // FUNÇÃO RESPONSAVEL POR VALIDAR DADOS DE UPLOAD DE DOCUMENTO
    public function View_Documento_Upload_Validate(Request $request)
    {
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

            $documento = $this->Gerar_Nome_Documento($file);

            $nome_documento = $documento['nome_documento'];
            $extensao = $documento['extensao'];

            $this->Create_Documento($nome, $nome_documento, $extensao);
            $this->Salvar_Documento($nome_documento, $file);

            return redirect()->route('documento_read');
        } else {
            return redirect()->route('documento_read');
        }
    }

    // FUNÇÃO RESPONSAVAVEL POR DELETAR DADOS E DOCUMENTO
    public function View_Documento_Delete(Request $request, int $id)
    {
        if ($request->isMethod('post')) {

            // DELETAR DOCUMENTO
            $this->Excluir_Documento($id);

            // DELETAR DADOS DO BANCO DE DADOS
            $this->Delete_Documento($id);

            return redirect()->route('documento_read');
        } else {
            return redirect()->route('documento_read');
        }
    }

    //FUNÇÃO RESPONSAVEL PELO FORMULARIO DE UPDATE
    public function View_Documento_Update(Request $request, int $id)
    {
        if ($request->isMethod('get')) {

            $data = $this->Read_Documento($id);
            $data = $data['documento'];

            return view('documento_formulario_update', ["data" => $data]);
        } else {
            return redirect()->route('documento_read');
        }
    }

    // FUNÇÃO RESPONSAVEL POR VALIDAR DADOS DE UPDATE
    public function View_Documento_Update_Validate(Request $request, int $id)
    {
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
            $nome_documento = null;
            $extensao = null;

            if (isset($file)) {

                // DELETAR DOCUMENTO ANTIGO
                $this->Excluir_Documento($id);

                // GERAR NOME PARA NOVO DOCUMENTO
                $documento = $this->Gerar_Nome_Documento($file);
                $nome_documento = $documento['nome_documento'];
                $extensao = $documento['extensao'];
            }

            // PREPARA PARAMETROS EM ARRAY
            $data_update = [
                'id' => $id,
                'nome' => $nome,
                'documento' => $nome_documento,
                'extensao' => $extensao,
            ];

            // ATUALIZA DADOS NO BANCO DE DADOS
            $this->Update_Documento($data_update);

            // SALVA NOVO DOCUMENTO NA PASTA
            if (isset($file)) {
                $this->Salvar_Documento($nome_documento, $file);
            }

            return redirect()->route('documento_read');
        } else {
            return  redirect()->route('documento_read');
        }
    }

    // FUNÇÃO RESPONSAVEL POR PESQUISAR DADOS
    // NOTA - ADICONAR METODO AO CRUD DE READ PARAMETRO NOME
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
                return redirect()->route('documento_read');
            }
        } catch (\Exception $exception) {
            abort(404);
        }
    }
}

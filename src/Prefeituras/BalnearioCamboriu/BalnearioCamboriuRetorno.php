<?php namespace PhpNFe\NFSe\Prefeituras\BalnearioCamboriu;

use Illuminate\Support\Arr;
use PhpNFe\NFSe\Prefeituras\Retorno;

class BalnearioCamboriuRetorno extends Retorno
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $erroPath = 'GerarNfseResult.ListaMensagemRetorno.MensagemRetorno';

    /**
     * @var string
     */
    protected $okPath = 'GerarNfseResult.NovaNfse.IdentificacaoNfse';

    /**
     * ItajaiRetorno constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = json_decode(json_encode($data), true);
    }

    /**
     * Retorna o xml recebido do servidor.
     * @return string
     */
    public function getXml()
    {
        $xml = file_get_contents(__DIR__ . '/retorno_gerarnfseresult.xml');

        $xml = str_replace('{{cnpjPrestador}}',      Arr::get($this->data, $this->okPath . '.IdentificacaoPrestador.Cnpj', ''), $xml);
        $xml = str_replace('{{inscricaoMunicipal}}', Arr::get($this->data, $this->okPath . '.IdentificacaoPrestador.InscricaoMunicipal', ''), $xml);
        $xml = str_replace('{{numNfse}}',            Arr::get($this->data, $this->okPath . '.Numero', ''), $xml);
        $xml = str_replace('{{serie}}',              Arr::get($this->data, $this->okPath . '.Serie', ''), $xml);
        $xml = str_replace('{{numProt}}',            Arr::get($this->data, $this->okPath . '.CodigoVerificacao', ''), $xml);
        $xml = str_replace('{{dataEmit}}',           Arr::get($this->data, $this->okPath . '.DataEmissao', ''), $xml);
        $xml = str_replace('{{link}}',               Arr::get($this->data, $this->okPath . '.Link', ''), $xml);

        return $xml;
    }

    /**
     * Pegar o número da Nfse.
     * @return string
     */
    public function getNfse()
    {
        return Arr::get($this->data, $this->okPath . '.Numero', '');
    }

    /**
     * Retorna o protocolo da operação.
     * @return string
     */
    public function getProt()
    {
        return Arr::get($this->data, $this->okPath . '.CodigoVerificacao');
    }

    /**
     * Pega o codigo da mensagem retornada pela requisicao servidor.
     * @return string
     */
    public function getCodigoErro()
    {
        return Arr::get($this->data, $this->erroPath . '.Codigo', '');
    }

    /**
     * Retorna a mensagem de erro caso tiver.
     * @return string
     */
    public function getErro()
    {
        return Arr::get($this->data, $this->erroPath . '.Mensagem', '');
    }

    /**
     * Retorna se teve erro ou não.
     * @return bool
     */
    public function isError()
    {
        $err = Arr::get($this->data, $this->erroPath);

        return ! is_null($err);
    }

    /**
     * Retorna se teve erro ou não no cancelamento.
     * @return bool
     */
    public function isErrorCancela()
    {
        return $this->xml->getElementsByTagName('Confirmacao')->item(0) == null ? true : false;
    }
}
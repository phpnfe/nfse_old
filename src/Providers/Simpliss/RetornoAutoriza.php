<?php namespace PhpNFe\NFSe\Providers\Simpliss;

use Illuminate\Support\Arr;
use PhpNFe\NFSe\Providers\Retorno as BaseRetorno;

class RetornoAutoriza extends BaseRetorno
{
    /**
     * @var string
     */
    protected $pathError = 'GerarNfseResult.ListaMensagemRetorno.MensagemRetorno';

    /**
     * @var string
     */
    protected $pathOk = 'GerarNfseResult.NovaNfse.IdentificacaoNfse';

    /**
     * @param $data
     */
    public function __construct($data)
    {
        parent::__construct(json_decode(json_encode($data), true));
    }

    /**
     * Retorna se teve erro ou não.
     * @return bool
     */
    public function isError()
    {
        return Arr::has($this->data, $this->pathError);
    }

    /**
     * Retorna a lista das mensagens de erro se tiver.
     * @return string
     */
    public function getErros()
    {
        $errors = Arr::has($this->data, $this->pathError . '.Codigo') ? [Arr::get($this->data, $this->pathError, [])] : Arr::get($this->data, $this->pathError, []);

        $lines = [];
        foreach ($errors as $err) {
            $lines[] = sprintf('%s: %s', Arr::get($err, 'Codigo', ''), Arr::get($err, 'Mensagem', ''));
        }

        return implode("\r\n", $lines);
    }

    /**
     * Retorna o numero da Nfse.
     * @return string
     */
    public function getNumNfse()
    {
        return Arr::get($this->data, $this->pathOk . '.Numero', '');
    }

    /**
     * Retorna o protocolo da operação.
     * @return string
     */
    public function getNumProt()
    {
        return Arr::get($this->data, $this->pathOk . '.CodigoVerificacao', '');
    }

    /**
     * Retorna o protocolo como XML.
     * @return string
     */
    public function getXmlProt()
    {
        if ($this->isError()) {
            return '';
        }

        $info = Arr::get($this->data, $this->pathOk, []);

        $xml = file_get_contents(__DIR__ . '/template_retorno_gerarnfse.xml');

        $xml = str_replace('{{cnpjPrestador}}',      Arr::get($info, 'IdentificacaoPrestador.Cnpj', ''), $xml);
        $xml = str_replace('{{inscricaoMunicipal}}', Arr::get($info, 'IdentificacaoPrestador.InscricaoMunicipal', ''), $xml);
        $xml = str_replace('{{numNfse}}',            Arr::get($info, 'Numero', ''), $xml);
        $xml = str_replace('{{serie}}',              Arr::get($info, 'Serie', ''), $xml);
        $xml = str_replace('{{numProt}}',            Arr::get($info, 'CodigoVerificacao', ''), $xml);
        $xml = str_replace('{{dataEmit}}',           Arr::get($info, 'DataEmissao', ''), $xml);
        $xml = str_replace('{{link}}',               Arr::get($info, 'Link', ''), $xml);

        return $xml;
    }
}
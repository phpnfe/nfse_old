<?php namespace PhpNFe\NFSe\Providers\Simpliss;

use Illuminate\Support\Arr;
use PhpNFe\NFSe\Providers\Retorno as BaseRetorno;

class RetornoCancela extends BaseRetorno
{
    /**
     * @var string
     */
    protected $pathError = 'CancelarNfseResult.ListaMensagemRetorno.MensagemRetorno';

    /**
     * @var string
     */
    protected $pathOk = 'CancelarNfseResult.Cancelamento.Confirmacao';

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
        return Arr::get($this->data, $this->pathOk . '.Pedido.InfPedidoCancelamento.IdentificacaoNfse.Numero', '');
    }

    /**
     * Retorna o protocolo da operação.
     * @return string
     */
    public function getNumProt()
    {
        return '';
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

        $xml = file_get_contents(__DIR__ . '/template_retorno_cancelamento.xml');

        $xml = str_replace('{{cnpjPrestador}}',      Arr::get($info, 'Pedido.InfPedidoCancelamento.IdentificacaoNfse.Cnpj', ''), $xml);
        $xml = str_replace('{{inscricaoMunicipal}}', Arr::get($info, 'Pedido.InfPedidoCancelamento.IdentificacaoNfse.InscricaoMunicipal', ''), $xml);
        $xml = str_replace('{{numNfse}}',            Arr::get($info, 'Pedido.InfPedidoCancelamento.IdentificacaoNfse.Numero', ''), $xml);
        $xml = str_replace('{{codMun}}',             Arr::get($info, 'Pedido.InfPedidoCancelamento.IdentificacaoNfse.CodigoMunicipio', ''), $xml);
        $xml = str_replace('{{codCan}}',             Arr::get($info, 'Pedido.InfPedidoCancelamento.CodigoCancelamento', ''), $xml);
        $xml = str_replace('{{dataCan}}',            Arr::get($info, 'DataHoraCancelamento', ''), $xml);

        return $xml;
    }
}
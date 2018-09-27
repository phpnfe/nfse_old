<?php

namespace PhpNFe\NFSe\Builder\Templates;

use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpNFe\NFSe\Builder\Rps;
use PhpNFe\NFSe\Prefeituras\Config;
use PhpNFe\NFSe\Prefeituras\Roteador;

/**
 * Classe para manipular os templates.
 * Class Manipulador.
 */
class Manipulador
{
    /**
     * Rps.
     *
     * @var Rps
     */
    protected $rps;

    /**
     * Cidade.
     *
     * @var
     */
    protected $cidade;

    /**
     * Manipulador constructor.
     *
     * @param Rps $this ->rps
     * @param $codMun
     */
    public function __construct(Rps $rps, $codMun)
    {
        $this->cidade = Roteador::distribuir($codMun);
        $this->rps = $rps;
    }

    /**
     * Carregar a classe.
     *
     * @param Rps $rps
     * @param $codMun
     * @return Manipulador
     */
    public static function load(Rps $rps, $codMun)
    {
        return new self($rps, $codMun);
    }

    /**
     * Gerar XML.
     * @return mixed
     */
    public function gerar()
    {
        $metodo = $this->cidade;

        return $this->$metodo();
    }

    protected function blumenau()
    {
        $xml = file_get_contents(__DIR__ . '/rpsBlumenau.xml');

        $dtEmissao = Carbon::createFromFormat(Carbon::ATOM, $this->rps->ideRPS->dataEmissao);
        $emissao = $dtEmissao->format('Y-m-d');

        $xml = $this->CpfCnpjPrestador($xml);
        $xml = str_replace('{{inscricaoMun}}', $this->rps->prestador->identificacao->inscricaoMun, $xml);
        $xml = str_replace('{{serieRPS}}', $this->rps->ideRPS->serieRPS, $xml);
        $xml = str_replace('{{numeroRPS}}', $this->rps->ideRPS->numeroRPS, $xml);
        $xml = str_replace('{{tipoRPS}}', $this->replaceVars('tipoRPS', $this->rps->ideRPS->tipoRPS), $xml);
        $xml = str_replace('{{dataEmissao}}', $emissao, $xml);
        $xml = str_replace('{{statusRPS}}', $this->replaceVars('statusRPS', $this->rps->ideRPS->statusRPS), $xml);
        $xml = str_replace('{{tributacaoRPS}}', $this->replaceVars('natOp', $this->rps->ideRPS->naturezaOperacao), $xml);
        $xml = str_replace('{{valorServicos}}', $this->rps->servico->valores->valorServicos, $xml);
        $xml = str_replace('{{valorDeducoes}}', $this->rps->servico->valores->valorDeducoes, $xml);
        $xml = str_replace('{{valorPIS}}', $this->rps->servico->valores->valorPIS, $xml);
        $xml = str_replace('{{valorCOFINS}}', $this->rps->servico->valores->valorCOFINS, $xml);
        $xml = str_replace('{{valorINSS}}', $this->rps->servico->valores->valorINSS, $xml);
        $xml = str_replace('{{valorIR}}', $this->rps->servico->valores->valorIR, $xml);
        $xml = str_replace('{{valorCSLL}}', $this->rps->servico->valores->valorCSLL, $xml);
        $xml = str_replace('{{codigoServico}}', $this->rps->servico->codigoServico, $xml);
        $xml = str_replace('{{aliquota}}', $this->rps->servico->valores->aliquota, $xml);
        $xml = str_replace('{{ISSRetido}}', $this->replaceVars('boolean', $this->rps->servico->valores->ISSRetido), $xml);
        $xml = $this->CpfCnpjTomador($xml);
        $xml = str_replace('{{razaoSocial}}', $this->rps->tomador->identificacao->razaoSocial, $xml);
        $xml = str_replace('{{endereco}}', $this->rps->tomador->identificacao->endereco->endereco, $xml);
        $xml = str_replace('{{numero}}', $this->rps->tomador->identificacao->endereco->numero, $xml);
        $xml = str_replace('{{bairro}}', $this->rps->tomador->identificacao->endereco->bairro, $xml);
        $xml = str_replace('{{cidade}}', $this->rps->tomador->identificacao->endereco->codMun, $xml);
        $xml = str_replace('{{uf}}', $this->rps->tomador->identificacao->endereco->uf, $xml);
        $xml = str_replace('{{cep}}', $this->rps->tomador->identificacao->endereco->cep, $xml);
        $xml = str_replace('{{email}}', $this->rps->tomador->identificacao->contato->email, $xml);
        $xml = str_replace('{{discriminacao}}', $this->rps->servico->discriminacao, $xml);

        $this->limpaRPS($xml);

        return $xml;
    }

    /**
     * Montar rps de Itajai.
     * @return mixed|string
     */
    protected function itajai()
    {
        $xml = file_get_contents(__DIR__ . '/rpsItajai.xml');
        $xml = $this->rps->idInfRps == null ? str_replace('{{id}}', '', $xml)
            : str_replace('{{id}}', $this->rps->idInfRps, $xml);
        $xml = str_replace('{{numeroRPS}}', $this->rps->ideRPS->numeroRPS, $xml);
        $xml = str_replace('{{serieRPS}}', $this->rps->ideRPS->serieRPS, $xml);
        $xml = str_replace('{{tipoRPS}}', $this->replaceVars('tipoRPS', $this->rps->ideRPS->tipoRPS), $xml);
        $xml = str_replace('{{dataEmissao}}', $this->rps->ideRPS->dataEmissao, $xml);
        $xml = str_replace('{{naturezaOperacao}}', $this->replaceVars('natOp', $this->rps->ideRPS->naturezaOperacao), $xml);
        $xml = str_replace('{{simplesNacional}}', $this->replaceVars('boolean', $this->rps->prestador->simplesNacional), $xml);
        $xml = str_replace('{{incentivadorCultural}}', $this->replaceVars('boolean', $this->rps->prestador->incentivadorCultural), $xml);
        $xml = str_replace('{{statusRPS}}', $this->replaceVars('statusRPS', $this->rps->ideRPS->statusRPS), $xml);
        $xml = str_replace('{{valorServicos}}', $this->rps->servico->valores->valorServicos, $xml);
        $xml = str_replace('{{valorPIS}}', $this->rps->servico->valores->valorPIS, $xml);
        $xml = str_replace('{{valorCOFINS}}', $this->rps->servico->valores->valorCOFINS, $xml);
        $xml = str_replace('{{valorINSS}}', $this->rps->servico->valores->valorINSS, $xml);
        $xml = str_replace('{{valorIR}}', $this->rps->servico->valores->valorIR, $xml);
        $xml = str_replace('{{valorCSLL}}', $this->rps->servico->valores->valorCSLL, $xml);
        $xml = str_replace('{{ISSRetido}}', $this->replaceVars('boolean', $this->rps->servico->valores->ISSRetido), $xml);
        $xml = str_replace('{{valorISS}}', $this->rps->servico->valores->valorISS, $xml);
        $xml = str_replace('{{valorIssRetido}}', $this->rps->servico->valores->valorISSRetido, $xml);
        $xml = str_replace('{{aliquota}}', $this->rps->servico->valores->aliquota, $xml);
        $xml = str_replace('{{valorLiquidoNfse}}', $this->rps->servico->valores->valorLiquidoNfse, $xml);
        //$xml = str_replace('{{descontoCondicionado}}', $this->rps->servico->valores->descontoCondicionado, $xml);
        //$xml = str_replace('{{descontoIncondicionado}}', $this->rps->servico->valores->descontoIncondicionado, $xml);
        $xml = str_replace('{{codigoServico}}', $this->rps->servico->codigoServico, $xml);
        $xml = str_replace('{{discriminacao}}', $this->rps->servico->discriminacao, $xml);
        $xml = str_replace('{{codMunPrestador}}', $this->rps->servico->codMun, $xml);
        $xml = $this->CpfCnpjPrestador($xml);
        $xml = str_replace('{{inscricaoMunicipal}}', $this->rps->prestador->identificacao->inscricaoMun, $xml);
        $xml = $this->CpfCnpjTomador($xml);
        $xml = str_replace('{{razaoSocial}}', $this->rps->tomador->identificacao->razaoSocial, $xml);
        $xml = str_replace('{{endereco}}', $this->rps->tomador->identificacao->endereco->endereco, $xml);
        $xml = str_replace('{{numero}}', $this->rps->tomador->identificacao->endereco->numero, $xml);
        $xml = str_replace('{{complemento}}', $this->rps->tomador->identificacao->endereco->complemento, $xml);
        $xml = str_replace('{{bairro}}', $this->rps->tomador->identificacao->endereco->bairro, $xml);
        $xml = str_replace('{{codMun}}', $this->rps->tomador->identificacao->endereco->codMun, $xml);
        $xml = str_replace('{{uf}}', $this->rps->tomador->identificacao->endereco->uf, $xml);
        $xml = str_replace('{{cep}}', $this->rps->tomador->identificacao->endereco->cep, $xml);
        $xml = str_replace('{{email}}', $this->rps->tomador->identificacao->contato->email, $xml);

        $this->limpaRPS($xml);

        return $xml;
    }

    /**
     * Montar rps de Balneario Camboriu.
     * @return mixed|string
     */
    protected function balneariocamboriu()
    {
        $xml = file_get_contents(__DIR__ . '/rpsBalnearioCamboriu.xml');
        $xml = $this->rps->idInfRps == null ? str_replace('{{id}}', '', $xml) : str_replace('{{id}}', $this->rps->idInfRps, $xml);

        $xml = str_replace('{{numeroRPS}}', $this->rps->ideRPS->numeroRPS, $xml);
        $xml = str_replace('{{serieRPS}}', $this->rps->ideRPS->serieRPS, $xml);
        $xml = str_replace('{{tipoRPS}}', $this->replaceVars('tipoRPS', $this->rps->ideRPS->tipoRPS), $xml);
        $xml = str_replace('{{dataEmissao}}', $this->rps->ideRPS->dataEmissao, $xml);
        $xml = str_replace('{{naturezaOperacao}}', $this->replaceVars('natOp', $this->rps->ideRPS->naturezaOperacao), $xml);
        $xml = str_replace('{{simplesNacional}}', $this->replaceVars('boolean', $this->rps->prestador->simplesNacional), $xml);
        $xml = str_replace('{{incentivadorCultural}}', $this->replaceVars('boolean', $this->rps->prestador->incentivadorCultural), $xml);
        $xml = str_replace('{{statusRPS}}', $this->replaceVars('statusRPS', $this->rps->ideRPS->statusRPS), $xml);
        $xml = str_replace('{{valorServicos}}', $this->rps->servico->valores->valorServicos, $xml);
        $xml = str_replace('{{valorPIS}}', $this->rps->servico->valores->valorPIS, $xml);
        $xml = str_replace('{{valorCOFINS}}', $this->rps->servico->valores->valorCOFINS, $xml);
        $xml = str_replace('{{valorINSS}}', $this->rps->servico->valores->valorINSS, $xml);
        $xml = str_replace('{{valorIR}}', $this->rps->servico->valores->valorIR, $xml);
        $xml = str_replace('{{valorCSLL}}', $this->rps->servico->valores->valorCSLL, $xml);
        $xml = str_replace('{{ISSRetido}}', $this->replaceVars('boolean', $this->rps->servico->valores->ISSRetido), $xml);
        $xml = str_replace('{{valorISS}}', $this->rps->servico->valores->valorISS, $xml);
        $xml = str_replace('{{valorIssRetido}}', $this->rps->servico->valores->valorISSRetido, $xml);
        $xml = str_replace('{{aliquota}}', $this->rps->servico->valores->aliquota, $xml);
        $xml = str_replace('{{valorLiquidoNfse}}', $this->rps->servico->valores->valorLiquidoNfse, $xml);
        //$xml = str_replace('{{descontoCondicionado}}', $this->rps->servico->valores->descontoCondicionado, $xml);
        //$xml = str_replace('{{descontoIncondicionado}}', $this->rps->servico->valores->descontoIncondicionado, $xml);
        $xml = str_replace('{{codigoServico}}', $this->rps->servico->codigoServico, $xml);
        $xml = str_replace('{{discriminacao}}', $this->rps->servico->discriminacao, $xml);
        $xml = str_replace('{{codMunPrestador}}', $this->rps->servico->codMun, $xml);
        $xml = $this->CpfCnpjPrestador($xml);
        $xml = str_replace('{{inscricaoMunicipal}}', $this->rps->prestador->identificacao->inscricaoMun, $xml);
        $xml = $this->CpfCnpjTomador($xml);
        $xml = str_replace('{{razaoSocial}}', $this->rps->tomador->identificacao->razaoSocial, $xml);
        $xml = str_replace('{{endereco}}', $this->rps->tomador->identificacao->endereco->endereco, $xml);
        $xml = str_replace('{{numero}}', $this->rps->tomador->identificacao->endereco->numero, $xml);
        $xml = str_replace('{{complemento}}', $this->rps->tomador->identificacao->endereco->complemento, $xml);
        $xml = str_replace('{{bairro}}', $this->rps->tomador->identificacao->endereco->bairro, $xml);
        $xml = str_replace('{{codMun}}', $this->rps->tomador->identificacao->endereco->codMun, $xml);
        $xml = str_replace('{{uf}}', $this->rps->tomador->identificacao->endereco->uf, $xml);
        $xml = str_replace('{{cep}}', $this->rps->tomador->identificacao->endereco->cep, $xml);
        $xml = str_replace('{{email}}', $this->rps->tomador->identificacao->contato->email, $xml);

        $this->limpaRPS($xml);

        return $xml;
    }

    /**
     * Traduzir variaveis de configuracao.
     *
     * @param $var
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    protected function replaceVars($var, $value)
    {
        $value = is_bool($value) ? (($value === true) ? 'true' : 'false') : $value;

        $info = Config::getMethodInfo($this->rps->tpAmb, $this->rps->codMun, Config::mtAutoriza);

        // Verificar se variavel de cofniguracao foi definida
        if (! array_key_exists($var, $info->configs)) {
            throw new \Exception(sprintf('Variavel de configuracao %s nao definida', $var));
        }

        // Verificar se valor foi definido na variavel de cofniguracao
        if (! array_key_exists($value, $info->configs[$var])) {
            throw new \Exception(sprintf('Valor %s nao foi definido na variavel de configuracao %s', $value, $var));
        }

        return $info->configs[$var][$value];
    }

    /**
     * Tratar o CpfCnpj do Prestador.
     *
     * @param $xml
     *
     * @return mixed
     */
    protected function CpfCnpjPrestador($xml)
    {
        if ($this->rps->prestador->identificacao->cnpj != null) {
            $cnpj = Conversor::converter(Conversor::CNPJ, $this->cidade);
            $xml = str_replace('{{CPFCNPJPrestador}}', $cnpj, $xml);

            return str_replace('{{cpfcnpjPrestador}}', $this->rps->prestador->identificacao->cnpj, $xml);
        } else {
            $cnpj = Conversor::converter(Conversor::CPF, $this->cidade);
            $xml = str_replace('{{CPFCNPJPrestador}}', $cnpj, $xml);

            return str_replace('{{cpfcnpjPrestador}}', $this->rps->prestador->identificacao->cpf, $xml);
        }
    }

    /**
     * Tratar o CpfCnpj do Tomador.
     *
     * @param $xml
     *
     * @return mixed
     */
    protected function CpfCnpjTomador($xml)
    {
        if ($this->rps->tomador->identificacao->cnpj != null) {
            $cnpj = Conversor::converter(Conversor::CNPJ, $this->cidade);
            $xml = str_replace('{{CPFCNPJTomador}}', $cnpj, $xml);

            return str_replace('{{cpfcnpjTomador}}', $this->rps->tomador->identificacao->cnpj, $xml);
        } else {
            $cpf = Conversor::converter(Conversor::CPF, $this->cidade);
            $xml = str_replace('{{CPFCNPJTomador}}', $cpf, $xml);

            return str_replace('{{cpfcnpjTomador}}', $this->rps->tomador->identificacao->cpf, $xml);
        }
    }

    /**
     * Limpar o RPS de tags vazias.
     *
     * @param $xml
     */
    protected function limpaRPS(&$xml)
    {
        $xml = str_replace('&', '', $xml);
        $xml = str_replace('@', '[[arroba]]', $xml);
        $xml = utf8_encode($xml);
        $xml = str_replace("\r\n", '[[nl]]', $xml);
        $xml = Str::ascii($xml);
        $xml = str_replace('[[nl]]', "\r\n", $xml);
        $xml = str_replace('[[arroba]]', '@', $xml);

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

        $nodes = $dom->getElementsByTagName(Config::getNomeRps(Config::getCodMun($this->cidade)))->item(0)->childNodes;

        foreach ($nodes as $node) {
            $this->limpaNode($node);
        }

        $xml = $dom->saveXML();
    }

    /**
     * Limpa um node por completo.
     *
     * @param $node
     */
    protected function limpaNode($node)
    {
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $nodeChild) {
                if ($nodeChild->hasChildNodes()) {
                    $this->limpaNode($nodeChild);
                } elseif ($nodeChild->textContent == null) {
                    $nodeChild->parentNode->removeChild($nodeChild);
                }
            }
        }

        if ($node->textContent == null) {
            $node->parentNode->removeChild($node);
        }
    }
}
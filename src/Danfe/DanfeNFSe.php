<?php

namespace PhpNFe\NFSe\Danfe;

use PhpNFe\NFSe\Prefeituras\Config;
use PhpNFe\NFSe\Prefeituras\Roteador;

/**
 * Class Danfe.
 */
class DanfeNFSe
{
    /**
     * Inscrição Municipal.
     *
     * @var string
     */
    protected $inscricao;

    /**
     * CPFCNPJ.
     *
     * @var string
     */
    protected $cpfcnpj;

    /**
     * Tipo do Ambiente.
     * @var string
     */
    protected $tpAmb;

    /**
     * Inscricao: Informar caso seja para Blumenau
     * CpfCnpj: Informar caso seja para Itajai.
     *
     * Danfe constructor.
     *
     * @param $inscricao
     */
    public function __construct($inscricao = null, $cpfcnpj = null, $tpAmb = Config::ambProducao)
    {
        $this->inscricao = $inscricao;
        $this->cpfcnpj = $cpfcnpj;
        $this->tpAmb = $tpAmb;
    }

    /**
     * Funcao para montar o PDF da NF passada por parametro.
     *
     * @param $nf
     * @param $codVerifi
     *
     * @return string
     */
    public function getPDF($codMun, $nf, $codVerifi)
    {
        require_once __DIR__ . '/DomPDF/bootstrap.php';

        $urlMethod = Roteador::distribuir($codMun) . 'MontaURL';

        $url = $this->$urlMethod($nf, $codVerifi);

        $img = $this->cURL($url);

        // Verificar se jah eh um PDF
        if (substr($img, 0, 4) == '%PDF') {
            return $img;
        }

        // Gerar PDF pela imagem
        $html = $this->getImageHTML($img);

        $pdf = new \DOMPDF();
        $pdf->set_base_path(__DIR__);
        $pdf->load_html($html);
        $pdf->set_paper('A4', 'portrait');

        $pdf->render();

        return $pdf->output();
    }

    /**
     * Monta url para a busca da danfe de Blumenau.
     *
     * @param $nf
     * @param $codVerifi
     *
     * @return string
     */
    protected function blumenauMontaURL($nf, $codVerifi)
    {
        if ($this->inscricao == null) {
            return new \Exception('Para Blumenau a inscricao do Prestador precisa ser informada!');
        }

        return 'https://nfse.blumenau.sc.gov.br/contribuinte/notaprintimg.aspx?inscricao='
        . $this->inscricao .
        '&nf=' . $nf .
        '&verificacao=' . $codVerifi .
        '&TextoCarta=&imprimir=0';
    }

    /**
     * Monta url para a busca da danfe de Itajai.
     *
     * @param $nf
     * @param $codVerifi
     *
     * @return string
     */
    protected function itajaiMontaURL($nf, $codVerifi)
    {
        if ($this->cpfcnpj == null) {
            return new \Exception('Para Itajai o cpf ou cnpj precisa ser informado!');
        }

        $tipo = '1';
        $serie = 'A1';
        //$prefix_url = ($this->tpAmb == Config::ambHomologacao) ? 'http://nfse-teste.publica.inf.br/itajai_nfse/NFES' : 'http://nfse.itajai.sc.gov.br/nfse/NFES';
        //2018 $prefix_url = ($this->tpAmb == Config::ambHomologacao) ? 'http://nfse-teste.publica.inf.br/itajai_nfse/NFES' : 'http://nfse.itajai.sc.gov.br/NFES';
        $prefix_url = ($this->tpAmb == Config::ambHomologacao) ? 'http://nfse-teste.publica.inf.br/itajai_nfse/NFES' : 'https://nfse.itajai.sc.gov.br/NFES';

        return $prefix_url . '?cdt_cnpjcpf=' . $this->cpfcnpj .
        '&nfp_numero=' . $nf . '&nfp_tipo=' . $tipo . '&nfp_serie=' . $serie . '&chave_validacao=' . $codVerifi;
    }

    /**
     * Requisicao cURL para pegar a imagem da Danfe retornando já em base64.
     *
     * @param $url
     *
     * @return string
     */
    protected function cURL($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $result = @curl_exec($ch);
        $curl_err = curl_error($ch);

        return $result;
    }

    /**
     * Montar o html com a imagem em base64.
     *
     * @param $img
     *
     * @return string
     */
    protected function getImageHTML($img)
    {
        $ext = 'GIF';
        $src = 'data:image/' . $ext . ';base64,' . base64_encode($img) . '';

        return '<img src="' . $src . '">';
    }
}
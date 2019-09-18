<?php namespace PhpNFe\NFSe\Contracts;

use PhpNFe\NFSe\Builder\RCancela;
use PhpNFe\NFSe\Builder\Rps;

interface NfseProviderInterface
{
    const ambNfseProducao    = 'producao';
    const ambNfseHomologacao = 'homologacao';

    /**
     * Autorizar um RPS em NFSe.
     *
     * @param Rps $rps
     * @param $amb
     * @return NfseRetornoInterface
     */
    public function autorizar(Rps $rps, $amb);

    /**
     * Cancelar uma NFSe emitida.
     *
     * @param RCancela $can
     * @param $amb
     * @return NfseRetornoInterface
     */
    public function cancela(RCancela $can, $amb);
}
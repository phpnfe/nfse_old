<?php

use Carbon\Carbon;

$cod_municipio = '4202008';

$rps = new \PhpNFe\NFSe\Builder\Rps($cod_municipio);

// ideRPS
//--------------------------------------------------------------------
$rps->ideRPS->numeroRPS        = '00001';
$rps->ideRPS->serieRPS         = 'E';
$rps->ideRPS->tipoRPS          = '1'; // Normal
$rps->ideRPS->dataEmissao      = Carbon::now()->format(Carbon::ATOM);
$rps->ideRPS->naturezaOperacao = '101';
$rps->ideRPS->statusRPS        = 'N'; // Normal

// prestador
//--------------------------------------------------------------------
// Identificacao
$rps->prestador->identificacao->cnpj         = '01799858000120';
$rps->prestador->identificacao->razaoSocial  = 'MORRO DA CRUZ EMPREENDIMENTOS TURISTICOS LTDA - EPP';
$rps->prestador->identificacao->inscricaoMun = '926';

// Endereço
$rps->prestador->identificacao->endereco->endereco    = 'R INDONESIA';
$rps->prestador->identificacao->endereco->numero      = '800';
$rps->prestador->identificacao->endereco->complemento = '';
$rps->prestador->identificacao->endereco->bairro      = 'NACOES';
$rps->prestador->identificacao->endereco->codMun      = $cod_municipio;
$rps->prestador->identificacao->endereco->uf          = 'SC';
$rps->prestador->identificacao->endereco->cep         = '88338285';

// Contato
$rps->prestador->identificacao->contato->email        = 'cristoluz@cristoluz.com.br';
$rps->prestador->identificacao->contato->telefone     = '4733632337';

// Outros
$rps->prestador->incentivadorCultural = '2';
$rps->prestador->simplesNacional      = '1';


// tomador
//--------------------------------------------------------------------
$rps->tomador->identificacao->cpf = '00852036965';

$rps->tomador->identificacao->razaoSocial = 'BRUNO GONCALVES';
$rps->tomador->identificacao->inscricaoMun = '';

// Endereco
$rps->tomador->identificacao->endereco->endereco    = 'RUA GUILHERME POERNER';
$rps->tomador->identificacao->endereco->numero      = '1855';
$rps->tomador->identificacao->endereco->complemento = 'BLOCO 4 AP 304';
$rps->tomador->identificacao->endereco->bairro      = 'PASSO MANSO';
$rps->tomador->identificacao->endereco->codMun      = '4202404';
$rps->tomador->identificacao->endereco->uf          = 'SC';
$rps->tomador->identificacao->endereco->cep         = '89032300';

// Contato
$rps->tomador->identificacao->contato->email    = 'bugotech@gmail.com';
$rps->tomador->identificacao->contato->telefone = null;

// servico
//--------------------------------------------------------------------
$rps->servico->codigoServico = '12.17';
$rps->servico->codMun        = $cod_municipio;
$rps->servico->discriminacao = 'INGRESSOS';

// totais
//--------------------------------------------------------------------
$rps->servico->valores->ISSRetido   = '2';
$rps->servico->valores->valorPIS    = 0;
$rps->servico->valores->valorCOFINS = 0;
$rps->servico->valores->valorINSS   = 0;
$rps->servico->valores->valorIR     = 0;
$rps->servico->valores->valorCSLL   = 0;

$rps->servico->valores->aliquota = 3;

$rps->servico->valores->valorISSRetido   = 0;
$rps->servico->valores->valorServicos    = 10.00;
$rps->servico->valores->baseCalculo      = $rps->servico->valores->valorServicos;
$rps->servico->valores->valorLiquidoNfse = $rps->servico->valores->valorServicos;
$rps->servico->valores->valorISS         = round($rps->servico->valores->baseCalculo * $rps->servico->valores->aliquota / 100, 2);
$rps->servico->valores->valorDeducoes    = 0;

return $rps;
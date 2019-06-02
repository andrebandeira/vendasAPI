<?php

declare(strict_types=1);

namespace Api\Handler\Notificacao;

use Core\Handler\MainHandler;
use Core\Json\JsonException;
use Core\Json\JsonMessage;
use Core\Repository\Vendas\Venda;
use Core\Util\MailUtil;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class EmailHandler extends MainHandler
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $dataAtual = date('Y-m-d');

            $dataInicio = $dataAtual;
            $dataFim = $dataAtual;

            $vendas = Venda::buscaVenda(null, $dataInicio, $dataFim);

            $conteudo = $this->getConteudo($vendas);

            $login = 'processoseletivo20199@gmail.com';
            $pass = '123mudar!';
            $port = '465';
            $ssl = 'ssl';
            $smtp = 'smtp.gmail.com';

            $smtpOptions = MailUtil::getSMTPOptions(
                $login, $pass, $smtp, $ssl, $port
            );

            $destinatario = 'abandeira@live.com';
            $assunto = 'Relatório de Vendas: ' . $dataAtual;

            MailUtil::send(
                $destinatario, $assunto, $conteudo, $smtpOptions
            );

            return new JsonMessage(
                'OK'
            );
        } catch (\Exception $ex) {
            return new JsonException($ex);
        }
    }

    private function getConteudo($vendas) : string {
        $conteudo = '<table border="1">';
        $conteudo.= '<thead>';
        $conteudo.= '<tr>';
        $conteudo.= '<td>';
        $conteudo.= 'ID';
        $conteudo.= '</td>';
        $conteudo.= '<td>';
        $conteudo.= 'NOME';
        $conteudo.= '</td>';
        $conteudo.= '<td>';
        $conteudo.= 'EMAIL';
        $conteudo.= '</td>';
        $conteudo.= '<td>';
        $conteudo.= 'DATA';
        $conteudo.= '</td>';
        $conteudo.= '<td>';
        $conteudo.= 'VALOR';
        $conteudo.= '</td>';
        $conteudo.= '<td>';
        $conteudo.= 'COMISSAO';
        $conteudo.= '</td>';
        $conteudo.= '</tr>';
        $conteudo.= '</thead>';
        $conteudo.= '<tbody>';

        $comissaoTotal = 0;
        $valorTotal = 0;

        foreach ($vendas as $venda) {
            $dataHora = $venda->DATA_HORA;

            if ($dataHora) {
                $dataHora = new \DateTime($dataHora);
                $dataHora = $dataHora->format('d/m/Y H:i:s');
            }

            $comissaoTotal += $venda->COMISSAO;
            $valorTotal += $venda->VALOR;

            $comissao = number_format(floatval($venda->COMISSAO),2, ',', '.');
            $valor = number_format(floatval($venda->VALOR),2, ',', '.');

            $conteudo.= '<tr>';
            $conteudo.= '<td>';
            $conteudo.= $venda->ID;
            $conteudo.= '</td>';
            $conteudo.= '<td>';
            $conteudo.= $venda->NOME;
            $conteudo.= '</td>';
            $conteudo.= '<td>';
            $conteudo.= $venda->EMAIL;
            $conteudo.= '</td>';
            $conteudo.= '<td>';
            $conteudo.= $dataHora;
            $conteudo.= '</td>';
            $conteudo.= '<td>';
            $conteudo.= $valor;
            $conteudo.= '</td>';
            $conteudo.= '<td>';
            $conteudo.= $comissao;
            $conteudo.= '</td>';
            $conteudo.= '</tr>';
        }

        $conteudo.= '<tr>';
        $conteudo.= '<td>';
        $conteudo.= 'TOTAL';
        $conteudo.= '</td>';
        $conteudo.= '<td></td>';
        $conteudo.= '<td></td>';
        $conteudo.= '<td></td>';
        $conteudo.= '<td>';
        $conteudo.=  number_format($valorTotal,2, ',', '.');
        $conteudo.= '</td>';
        $conteudo.= '<td>';
        $conteudo.=  number_format($comissaoTotal,2, ',', '.');
        $conteudo.= '</td>';
        $conteudo.= '</tr>';

        $conteudo.= '</tbody>';
        $conteudo.= '</table>';

        return $conteudo;
    }
}

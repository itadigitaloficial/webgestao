<?php

require_once __DIR__ . '/../config/database.php';

class Dashboard
{
    public static function metrics()
    {
        $pdo = db();

        // Contagens básicas
        $clientesTotal = (int)($pdo->query("SELECT COUNT(*) AS t FROM clientes")->fetch()['t'] ?? 0);
        $clientesAtivos = (int)($pdo->query("SELECT COUNT(*) AS t FROM clientes WHERE ativo = 1")->fetch()['t'] ?? 0);

        $servicosTotal = (int)($pdo->query("SELECT COUNT(*) AS t FROM servicos")->fetch()['t'] ?? 0);
        $servicosAtivos = (int)($pdo->query("SELECT COUNT(*) AS t FROM servicos WHERE ativo = 1")->fetch()['t'] ?? 0);

        // OS por status
        $osAbertas = (int)($pdo->query("SELECT COUNT(*) AS t FROM ordens_servico WHERE status = 'aberta'")->fetch()['t'] ?? 0);
        $osAndamento = (int)($pdo->query("SELECT COUNT(*) AS t FROM ordens_servico WHERE status = 'em_andamento'")->fetch()['t'] ?? 0);
        $osConcluidas = (int)($pdo->query("SELECT COUNT(*) AS t FROM ordens_servico WHERE status = 'concluida'")->fetch()['t'] ?? 0);
        $osCanceladas = (int)($pdo->query("SELECT COUNT(*) AS t FROM ordens_servico WHERE status = 'cancelada'")->fetch()['t'] ?? 0);

        // Financeiro
        $receitasPagas = (float)($pdo->query("SELECT COALESCE(SUM(valor),0) AS t FROM movimentacoes WHERE tipo='receita' AND status='pago'")->fetch()['t'] ?? 0);
        $despesasPagas = (float)($pdo->query("SELECT COALESCE(SUM(valor),0) AS t FROM movimentacoes WHERE tipo='despesa' AND status='pago'")->fetch()['t'] ?? 0);

        $receitasPendentes = (float)($pdo->query("SELECT COALESCE(SUM(valor),0) AS t FROM movimentacoes WHERE tipo='receita' AND status='pendente'")->fetch()['t'] ?? 0);
        $despesasPendentes = (float)($pdo->query("SELECT COALESCE(SUM(valor),0) AS t FROM movimentacoes WHERE tipo='despesa' AND status='pendente'")->fetch()['t'] ?? 0);

        // Atrasados (regra simples: pendente e vencimento < hoje)
        $atrasados = (float)($pdo->query("SELECT COALESCE(SUM(valor),0) AS t FROM movimentacoes WHERE status='pendente' AND data_vencimento < CURDATE()")->fetch()['t'] ?? 0);

        // Saldo (pago)
        $saldoPago = $receitasPagas - $despesasPagas;

        // Últimas OS
        $ultimasOs = $pdo->query("
            SELECT os.id, os.numero, os.status, os.data_abertura, os.valor_final, c.nome AS cliente_nome
            FROM ordens_servico os
            JOIN clientes c ON c.id = os.cliente_id
            ORDER BY os.id DESC
            LIMIT 8
        ")->fetchAll();

        // Últimas movimentações
        $ultimasMovs = $pdo->query("
            SELECT m.id, m.tipo, m.status, m.descricao, m.valor, m.data_vencimento, m.data_pagamento,
                   c.nome AS cliente_nome, os.numero AS os_numero
            FROM movimentacoes m
            LEFT JOIN clientes c ON c.id = m.cliente_id
            LEFT JOIN ordens_servico os ON os.id = m.ordem_id
            ORDER BY m.id DESC
            LIMIT 8
        ")->fetchAll();

        return [
            'clientesTotal' => $clientesTotal,
            'clientesAtivos' => $clientesAtivos,
            'servicosTotal' => $servicosTotal,
            'servicosAtivos' => $servicosAtivos,

            'osAbertas' => $osAbertas,
            'osAndamento' => $osAndamento,
            'osConcluidas' => $osConcluidas,
            'osCanceladas' => $osCanceladas,

            'receitasPagas' => $receitasPagas,
            'despesasPagas' => $despesasPagas,
            'receitasPendentes' => $receitasPendentes,
            'despesasPendentes' => $despesasPendentes,
            'atrasados' => $atrasados,
            'saldoPago' => $saldoPago,

            'ultimasOs' => $ultimasOs,
            'ultimasMovs' => $ultimasMovs,
        ];
    }
}

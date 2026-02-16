<?php

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/core/Router.php';

/* ===========================
   CONTROLLERS
=========================== */

require_once __DIR__ . '/app/controllers/DashboardController.php';
require_once __DIR__ . '/app/controllers/ClientesController.php';
require_once __DIR__ . '/app/controllers/ServicosController.php';
require_once __DIR__ . '/app/controllers/OrdemServicoController.php';
require_once __DIR__ . '/app/controllers/FinanceiroController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';

/* ===========================
   ROUTER
=========================== */

$router = new Router();

/* ===========================
   AUTH
=========================== */

$router->get('', fn() => DashboardController::index());

$router->get('login', fn() => AuthController::login());
$router->post('login', fn() => AuthController::loginPost());
$router->get('logout', fn() => AuthController::logout());

/* ===========================
   DASHBOARD
=========================== */

$router->get('dashboard', fn() => DashboardController::index());

/* ===========================
   CLIENTES
=========================== */

$router->get('clientes', fn() => ClientesController::index());
$router->get('clientes_create', fn() => ClientesController::create());
$router->post('clientes_store', fn() => ClientesController::store());
$router->get('clientes_edit', fn() => ClientesController::edit());
$router->post('clientes_update', fn() => ClientesController::update());
$router->post('clientes_delete', fn() => ClientesController::delete());

$router->get('clientes_servicos', fn() => ClientesController::servicos());
$router->post('clientes_add_servico', fn() => ClientesController::addServico());
$router->get('clientes_toggle_servico', fn() => ClientesController::toggleServico());
$router->get('clientes_remove_servico', fn() => ClientesController::removeServico());

/* ===========================
   SERVIÇOS
=========================== */

$router->get('servicos', fn() => ServicosController::index());
$router->get('servicos_create', fn() => ServicosController::create());
$router->post('servicos_store', fn() => ServicosController::store());
$router->get('servicos_edit', fn() => ServicosController::edit());
$router->post('servicos_update', fn() => ServicosController::update());
$router->post('servicos_delete', fn() => ServicosController::delete());

/* ===========================
   ORDENS DE SERVIÇO
=========================== */

$router->get('os', fn() => OrdemServicoController::index());
$router->get('os_create', fn() => OrdemServicoController::create());
$router->post('os_store', fn() => OrdemServicoController::store());

$router->get('os_detalhe', fn() => OrdemServicoController::detalhe());
$router->post('os_add_item', fn() => OrdemServicoController::addItem());
$router->post('os_remove_item', fn() => OrdemServicoController::removeItem());
$router->post('os_update_status', fn() => OrdemServicoController::updateStatus());
$router->post('os_delete', fn() => OrdemServicoController::delete());

/* ===========================
   FINANCEIRO
=========================== */

$router->get('financeiro', fn() => FinanceiroController::index());
$router->post('financeiro_pagar', fn() => FinanceiroController::marcarPago());
$router->post('financeiro_delete', fn() => FinanceiroController::delete());

/* ===========================
   EXECUTAR
=========================== */

$router->run();

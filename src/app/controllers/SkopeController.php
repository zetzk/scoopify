<?php

namespace src\app\controllers;

use src\app\database\entities\Skope;
use src\support\View;

class SkopeController
{
    /**
     * Constructor do SkopeController
     * 
     * Inicializa o controller com as dependências necessárias através de injeção de dependência.
     */
    function __construct() {}


    
    /**
     * Exibe a listagem de escopos (skopes)
     * 
     * Este método retorna uma view com a lista de escopos disponíveis.
     * Caso não existam escopos, exibe uma notificação de sucesso e retorna
     * uma view vazia específica.
     * 
     * @return View Retorna a view 'skopes.index' com a lista de escopos ou 'skopes.empty' se não houver escopos
     */
    function index()
    {
        $skopes = Skope::get();
        $skopes_with_devs = array_filter($skopes, fn(Skope $skope) => !$skope->is_estimated());
        if (empty($skopes_with_devs))
            notification()->success("Não há nenhum escopo disponível para análise hoje 🎉");

        return view("skopes.index", ["skopes" => $skopes]);
    }
}

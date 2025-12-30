<?php

namespace src\app\controllers;


use src\app\database\entities\Session;
use src\app\database\entities\Skope;
use src\exceptions\app\NotFoundSessionException;
use src\exceptions\app\NotFoundWithUuidException;
use src\exceptions\app\SessionInProgressException;
use src\support\Json;
use src\support\Redirect;
use src\support\Rules;
use src\support\View;

class SessionController
{

    public function __construct()
    {
    }

    /**
     * Inicia uma sessão de escopo para um usuário.
     *
     * @param string $uuid O identificador único do escopo (Skope) a ser iniciado.
     *
     * @return Redirect - Redireciona o usuário para a rota de junção da sessão.
     *
     * @throws NotFoundWithUuidException Lançada quando o escopo com o UUID fornecido não existe.
     * @throws SessionInProgressException Lançada quando já existe uma sessão em andamento para o escopo.
     *
     */
    public function start(string $uuid)
    {
        $skp = Skope::getByUuid($uuid) ?? throw new NotFoundWithUuidException();
        $session = Session::start($skp->id) ?? throw new SessionInProgressException(["data" => $skp]);
        Session::join(
            $session->id,
            user()->matricula,
            user()->nome,
            Rules::HOST
        );
        return redirect()->route("session.join", ["uuid" => $uuid]);
    }



    /**
     * Adiciona um usuário a uma sessão pelo UUID do projeto.
     *
     * Este método recupera um projeto (Skope) pelo seu UUID, verifica se existe uma sessão
     * para esse projeto, adiciona o usuário atual como participante se ainda não estiver na sessão,
     * e retorna a view da sala de espera.
     *
     * @param string $uuid O identificador único do projeto (Skope)
     *
     * @return View A view da sala de espera com dados do projeto e da sessão
     *
     * @throws NotFoundWithUuidException Se o UUID do projeto não existir
     * @throws NotFoundSessionException Se nenhuma sessão existir para o projeto
     *
     */
    public function join(string $uuid)
    {
        $skp = Skope::getByUuid($uuid)
            ?? throw new NotFoundWithUuidException();
        $session = Session::by_project_id($skp->id)
            ?? throw new NotFoundSessionException(["data" => $skp]);
        $user = user();


        Session::join( 
            $session->id,
            $user->matricula,
            $user->nome,
            Rules::PARTICIPANT
        );
        $is_host = Session::is_host($session->id, $user->matricula);
        return view("sessions.waiting", [
            "skope" => $skp,
            "session" => $session,
            "is_host" => $is_host
        ]);
    }



    /**
     * Retorna os participantes de uma sessão específica.
     *
     * @param int $id O identificador único da sessão
     *
     * @return Json Retorna um JSON com os participantes da sessão
     *
     * @throws NotFoundSessionException Se a sessão não existir
     */
    function participants(int $id)
    {
        $session = Session::getById($id);
        if (!$session)
            return Json::return(["message" => "Session not found"], 404);
        return Json::return(["participants" => Session::participants($session->id)], 200);
    }
}

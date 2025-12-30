<?php

namespace src\app\database\entities;

use src\app\database\Querio;
use src\support\Rules;
use src\support\Status;

class Session extends Querio
{
    protected static string $table = 'bd_amsted.scoopify_session';
    protected static string $dbType = 'intranet';

    public string $status;
    public int $id;

    /**
     * Inicia uma sessão para um projeto específico.
     *
     * Este método verifica se já existe uma sessão ativa para o projeto.
     * Caso não exista, cria uma nova sessão com status de espera de desenvolvedores.
     * Se existir uma sessão em andamento, retorna nulo. Caso contrário, atualiza
     * o status da sessão existente para espera de desenvolvedores.
     *
     * @param int $project_id O identificador único do projeto
     *
     * @return mixed Retorna a sessão criada ou atualizada, ou null se a sessão
     *               está atualmente em andamento (IN_SESSION)
     *
     * @see self::by_project_id()
     * @see self::create()
     * @see self::update()
     */
    static function start(int $project_id)
    {
        $has = self::by_project_id($project_id);
        if (!$has)
            return self::create(["project_id" => $project_id, "status" => Status::WAITING_DEVS]);


        if (in_array($has->status, [Status::IN_SESSION]))
            return null;

        return self::update(["status" => Status::WAITING_DEVS])->whereEquals("id", $has->id)->finish();
    }

    /**
     * Obtém uma sessão pelo ID do projeto.
     *
     * @param int $project_id O identificador único do projeto
     *
     * @return Session|null Retorna uma instância de Session se encontrada, ou null caso contrário
     */
    static function by_project_id(int $project_id): Session|null
    {
        return self::selectOne()->whereEquals("project_id", $project_id)->finish();
    }


    /**
     * adiciona um participante a uma sessão.
     * @param int $session_id
     * @param string $user_key
     * @param string $user_name
     * @param string $rule - host ou participant
     * @return array|bool|object
     */
    static function join(int $session_id, string $user_key, string $user_name, string $rule)
    {

        $is_participant = self::is_participant_in_session($session_id, $user_key);
        if ($is_participant)
            return false;

        $data = [
            "session_id" => $session_id,
            "user_key" => $user_key,
            "user_name" => $user_name,
            "rule" => $rule
        ];
        return self::table("bd_amsted.scoopify_session_participants")->create($data);
    }


    /**
     * Obtém todos os participantes de uma sessão específica.
     *
     * @param int $session_id O identificador único da sessão
     *
     * @return array|null Retorna um array de participantes da sessão
     */
    static function participants(int $session_id): array|null
    {
        return self::table("bd_amsted.scoopify_session_participants")
            ->select()
            ->whereEquals("session_id", $session_id)
            ->finish();
    }

    /**
     * Obtém apenas as chaves dos participantes de uma sessão específica.
     *
     * @param int $session_id O identificador único da sessão
     *
     * @return array|null Retorna um array contendo apenas as chaves dos participantes
     */
    static function participants_only_keys(int $session_id): array|null
    {
        $participants = self::table("bd_amsted.scoopify_session_participants")
            ->select()
            ->whereEquals("session_id", $session_id)
            ->finish();

        return $participants ? array_map(fn($p) => $p->user_key, $participants) : [];
    }


    /**
     * Verifica se um participante específico está em uma sessão.
     *
     * @param int $session_id O identificador único da sessão
     * @param string $user_key A chave do usuário a ser verificado
     *
     * @return bool Retorna true se o participante estiver na sessão, false caso contrário
     */
    static function is_participant_in_session(int $session_id, string $user_key)
    {
        $participants = self::participants_only_keys($session_id);
        if (!$participants)
            return false;
        return in_array($user_key, $participants);
    }



    /**
     * Verifica se um usuário é o host de uma sessão específica.
     *
     * @param int $session_id O identificador único da sessão
     * @param string $user_key A chave do usuário a ser verificado
     *
     * @return bool Retorna true se o usuário for o host da sessão, false caso contrário
     */
    static function is_host(int $session_id, string $user_key): bool
    {
        $participant = self::table("bd_amsted.scoopify_session_participants")
            ->selectOne()
            ->whereEquals("session_id", $session_id)
            ->andWhere("user_key", '=', $user_key)
            ->andWhere("rule", '=', Rules::HOST)
            ->finish();
        return $participant != null;
    }
}

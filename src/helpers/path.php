<?php

function path()
{
    return new class () {
        /**
         * Obtém o endereço de imagens.
         * @param string $path
         * @return string
         */
        function images(string $path): string
        {
            if ($path[0] === '/')
                $path = mb_substr($path, 1);
            return $this->start_path() . "/public/images/{$path}";
        }

        /**
         * Obtém o endereço dos arquivos javascript.
         * @param string $path
         * @return string
         */
        function js(string $path): string
        {
            if ($path[0] !== '/')
                $path = "/{$path}";
            return $this->start_path() . "/public/js{$path}";
        }


        /**
         * Obtém o endereço dos css.
         * @param string $path
         * @return string
         */
        function css(string $path): string
        {
            if ($path[0] !== '/')
                $path = "/{$path}";
            return $this->start_path() . "/public/css{$path}";
        }


        /**
         * Obtém o endereço de storage (arquivos de uploads).
         * @param string $path
         * @return string
         */
        function storage(string $path = ""): string
        {
            $link = str_replace($_ENV["APP_DIR"], $_ENV["APP_DIR"] . "-storage", $this->start_path() . "/{$path}");
            return $link;
        }


        /**
         * Obtém o endereço absoluto do servidor.
         * @param string|null $complement
         * @return string
         */
        function storage_server(string|null $complement = null): string
        {
            return getcwd() . "-storage/" . $complement;
        }


        /**
         * Obtém o endereço de imagens.
         * @param string $complement
         * @return string
         */
        function start_path(string $complement = ''): string
        {
            $r = !is_remote() ? path()->get_host() : path()->get_host() . '/' . str_replace('/var/www/htdocs/', "", getcwd());
            if (empty($complement))
                return $r;
            return $r . $complement;
        }


        /**
         * Obtém o endereço do servidor + complemento informado via parâmetro.
         * @param string|null $complement
         * @return string
         */
        function server(string|null $complement = null): string
        {
            return getcwd() . $complement;
        }

        /**
         * Obtém o url da aplicação.
         * @return string
         */
        function get_url(): string
        {
            if (is_remote()) {
                $getcwd = array_filter(
                    explode('/', getcwd()),
                    fn($i) => !in_array($i, ['', 'var', 'www', 'html', 'htdocs'])
                );
                $getcwdCleaned = implode("/", $getcwd);
                $url = path()->get_host() . "/{$getcwdCleaned}";
                return $url;
            } else {
                $getcwd = array_values(array_filter(
                    explode('/', $_SERVER["REDIRECT_URL"]),
                    fn($i) => !in_array($i, ['', 'var', 'www', 'html', 'htdocs'])
                ));
                $getcwdCleaned = implode("/", [$getcwd[0], $getcwd[1], $getcwd[2]]);
                $url = path()->get_host() . "/{$getcwdCleaned}";
                return $url;
            }
        }

        /**
         * Obtém a empresa que está a aplicação.
         * @return string
         */
        function get_company(): string
        {
            if (is_remote()) {
                $getcwd = array_values(
                    array_filter(
                        explode('/', getcwd()),
                        fn($i) => !in_array($i, ['', 'var', 'www', 'html', 'htdocs'])
                    )
                );
                return $getcwd[0];
            } else {
                $path = array_values(array_filter(explode('/', $_SERVER["REDIRECT_URL"])));
                return $path[0];
            }
        }

        /**
         * Obtém o host da aplicação.
         * @return string
         */
        function get_host(): string
        {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                ? "https://"
                : "http://";
            return $protocol . $_SERVER['HTTP_HOST'];
        }
    };
}

<?php

namespace App\Services;


class Helper
{

    function formatarNomeProprio(string $nome): string
    {
        // remove espaços duplicados
        $nome = trim(preg_replace('/\s+/', ' ', $nome));

        // tudo para minúscula (compatível com acentos)
        $nome = mb_strtolower($nome, 'UTF-8');

        // primeira letra de cada palavra em maiúscula
        return mb_convert_case($nome, MB_CASE_TITLE, 'UTF-8');
    }

    private function gerarSiglaNome(string $nome): string
    {
        // palavras que não entram na sigla
        $ignorar = ['de', 'da', 'do', 'dos', 'das', 'e'];

        // remove acentos e converte para ASCII
        $nome = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);

        // separa palavras
        $palavras = preg_split('/\s+/', trim($nome));

        $sigla = '';

        foreach ($palavras as $palavra) {
            $palavra = strtolower($palavra);

            if (!in_array($palavra, $ignorar)) {
                $sigla .= strtoupper(substr($palavra, 0, 1));
            }
        }

        return $sigla;
    }


}

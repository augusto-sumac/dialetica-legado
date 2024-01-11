@extend('layouts.autores')

<?php
$options = [
    'title' => 'Normas de Publicação',
    'icon' => 'atlas',
];
?>
@section('content')
    @include('components.content-header', $options)

    <div class="row justify-content-center">
        <div class="col" style="max-width: 210mm;">

            <div class="card">

                <div class="card-body fake-doc">

                    <h1 class="text-center">NORMAS DE PUBLICAÇÃO</h1>

                    <p>
                        A <strong>Dialética</strong> é a principal editora de divulgação científica do Brasil e referência
                        em
                        excelência editorial. As normas que se seguem têm o objetivo de orientar os nossos autores no uso da
                        Plataforma de Submissão de Artigos.
                    </p>

                    <h2>
                        Sobre a normalização e formatação
                    </h2>

                    <p>
                        O artigo deve ter, no máximo, 50 páginas de tamanho formatado da seguinte forma:
                    </p>

                    <ul>
                        <li>
                            <strong>Corpo do texto</strong>: Times New Roman, tamanho 12, espaçamento entre linhas de
                            1,5 cm, recuo padrão de 1,25 no início dos parágrafos e alinhamento justificado.
                            Não utilizar espaçamento antes ou depois dos parágrafos.
                        </li>
                        <li>
                            <strong>Citações diretas longas</strong>: Times New Roman, tamanho 10, espaçamento entre
                            linhas simples, recuo de 4 cm e alinhamento justificado.
                        </li>
                        <li>
                            <strong>Formato</strong>: A4 (21x29,7 cm);
                        </li>
                        <li>
                            <strong>Margens</strong>: Superior e inferior iguais a 2,5 cm e esquerda e direita iguais a 3,0
                            cm;
                        </li>
                        <li>
                            <strong>Referências bibliográficas</strong>: devem ser citadas no modelo autor-data no corpo do
                            texto e devem constar também ao final do artigo, com o título das obras em
                            negrito e em ordem alfabética, de acordo com as normas da ABNT.
                        </li>
                    </ul>

                    <p>
                        As seguintes informações devem constar na primeira página do artigo: título do trabalho
                        em letras maiúsculas, nome completo dos autores seguido do link para o respectivo ORCID ou
                        Lattes. Em seguida, devem constar o resumo em um único parágrafo e as palavras-chave.
                    </p>

                    <div class="mini-doc no-indent">
                        <p class="text-center mb-5">
                            TÍTULO DO TRABALHO: SUBTÍTULO DO ARTIGO
                        </p>
                        <p>
                            Nome completo do primeiro autor <br />
                            Link para o ORCID ou Lattes do primeiro autor
                        </p>
                        <p>
                            Nome completo do segundo autor <br />
                            Link para o ORCID ou Lattes do segundo autor
                        </p>
                        <p>
                            Nome completo do terceiro autor <br />
                            Link para o ORCID ou Lattes do terceiro autor
                        </p>
                        <p>
                            <strong>Resumo</strong>: Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                            eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis
                            nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure
                            dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur
                            sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est
                            laborum. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                            incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
                            ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit
                            in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat
                            cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </p>
                        <p class="mb-0">
                            <strong>Palavras-chave</strong>: Palavra-chave 1; Palavra-chave 2; Palavra-chave 3;
                            Palavra-chave 4.
                        </p>
                    </div>

                    <h2>
                        Sobre a autoria
                    </h2>

                    <p>
                        Reconhecemos que o mundo da pesquisa, da escrita e das publicações científicas contemporâneas encara
                        rápidas mudanças. Os desafios que a coautoria coloca para as diversas áreas do conhecimento não
                        podem ser tratadas de forma irreflexiva. Para tanto, levamos em consideração as recomendações de
                        instituições como a <em>International Committee of Medical Journal Editors (ICMJE), Council of
                            Science Editors</em>, dentre outras, para gerar critérios de submissão de artigos.
                    </p>

                    <p>
                        A autoria é uma grande responsabilidade, por isso entendemos que o crédito indevido, além de
                        antiético, desestimula os autores que realmente se envolveram com o artigo e com o trabalho, e
                        reforça práticas não horizontais dentro da academia.
                    </p>

                    <p>
                        A Editora Dialética reconhece que a coautoria é autêntica quando as referências abaixo contemplam
                        todos os que assinam o artigo:
                    </p>

                    <ul>
                        <li>
                            Concepção do conflito central e argumentação, assim como condução teórica e metodológica da
                            análise.
                        </li>
                        <li>
                            Fontes analisadas e sua interpretação.
                        </li>
                        <li>
                            Envolvimento na escrita, revisão e na aprovação do texto final a ser publicado.
                        </li>
                        <li>
                            Ciente das práticas envolvidas na produção do artigo, tais como: originalidade, inexistência de
                            plágio, uso devido de imagens e ilustrações e questões éticas.
                        </li>
                    </ul>

                    <p>
                        O <strong>número máximo de autores por artigo é 15</strong>. O autor responsável pela submissão do
                        artigo na
                        plataforma assume a comunicação com a Editora durante o processo de publicação do texto. Ele ou ela
                        deve garantir que todos os requisitos sejam atendidos e todas as demandas concluídas.
                    </p>

                </div>

            </div>

        </div>
    </div>
@endsection

@section('css')
    <style>
        .mini-doc {
            padding: 1.5rem;
            margin: 2rem auto;
            border: 1px solid #ddd;
        }

        .fake-doc>* {
            text-align: justify;
        }

        .fake-doc>:last-child {
            margin-bottom: 0;
        }

        @media(min-width: 768px) {
            .fake-doc {
                padding: 3em;
            }

            .fake-doc>* {
                text-indent: 1.5cm;
                margin-bottom: .75cm;
                line-height: .75cm;
            }

            .fake-doc ul {
                padding-left: 2.5cm;
            }

            .fake-doc ul li {
                text-indent: 0;
                padding-left: .5cm;
                margin-bottom: .5cm;
            }

            .fake-doc .no-indent,
            .fake-doc>.text-center {
                text-indent: 0;
            }
        }

        @media(max-width: 768px) {
            .fake-doc ul li {
                margin-bottom: 10px;
            }

            .mini-doc {}
        }

    </style>
@endsection

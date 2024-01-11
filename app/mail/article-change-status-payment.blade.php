@layout('mail.layouts.base-layout', ['title' => $name . ', o seu artigo foi aprovado para publicação! Parabéns!'])


@section('body')
    <div class="es-wrapper-color" style="background-color:transparent">
        <!--[if gte mso 9]>
                                                          <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
                                                           <v:fill type="tile" color="transparent"></v:fill>
                                                          </v:background>
                                                         <![endif]-->
        <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:transparent">
            <tr style="border-collapse:collapse">
                <td valign="top" style="padding:0;Margin:0">
                    <table cellpadding="0" cellspacing="0" class="es-content" align="center"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                        <tr style="border-collapse:collapse">
                            <td class="es-adaptive" align="center" style="padding:0;Margin:0">
                                <table class="es-content-body" cellspacing="0" cellpadding="0" align="center"
                                    bgcolor="#ffffff"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:900px">
                                    <tr style="border-collapse:collapse">
                                        <td align="left" style="padding:0;Margin:0">
                                            <table width="100%" cellspacing="0" cellpadding="0"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr style="border-collapse:collapse">
                                                    <td valign="top" align="center"
                                                        style="padding:0;Margin:0;width:900px">
                                                        <table width="100%" cellspacing="0" cellpadding="0"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left" style="padding:0;Margin:0">
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        Olá, <b>{{ $name }}</b>!</p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        Espero que esteja tudo bem!</p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        Tenho excelentes notícias para você!&nbsp;</p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        O seu artigo intitulado
                                                                        <b>{{ $title }}</b> foi <b>APROVADO</b>
                                                                        pelo <b>Conselho Editorial</b> para publicação
                                                                        em forma de capítulo de livro.
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        O próximo passo é <b>bem simples</b>: basta você
                                                                        acessar a plataforma e realizar o pagamento das
                                                                        custas editoriais para garantir que o seu
                                                                        trabalho seja publicado.</p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        O prazo de publicação do livro é de até
                                                                        <strong>60</strong><b> dias após a confirmação
                                                                            do pagamento</b>.
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        Na Dialética, você como autor não
                                                                        é&nbsp;obrigado a adquirir nenhuma unidade do
                                                                        livro. Após ser publicado, o livro estará
                                                                        disponível para download no site da editora, e
                                                                        também será divulgado&nbsp;em dezenas de
                                                                        marketplaces no Brasil e no Mundo.</p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        Caso tenha interesse, você poderá adquirir uma
                                                                        cópia impressa do livro posteriormente com o
                                                                        <b>desconto de 50% do autor</b>.
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        Clique abaixo para ser direcionado para a
                                                                        plataforma e garantir que o seu trabalho seja
                                                                        publicado:</p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr style="border-collapse:collapse">
                                                                <td align="center" style="padding:0;Margin:0"><span
                                                                        class="es-button-border"
                                                                        style="border-style:solid;border-color:#CCAD53;background:#6aca4c;border-width:0px;display:inline-block;border-radius:6px;width:auto"><a
                                                                            href="http://plataforma.editoradialetica.com"
                                                                            class="es-button" target="_blank"
                                                                            style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;color:#FFFFFF;font-size:22px;border-style:solid;border-color:#6aca4c;border-width:10px 20px 10px 20px;display:inline-block;background:#6aca4c;border-radius:6px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;font-weight:bold;font-style:normal;line-height:26px;width:auto;text-align:center">Quero
                                                                            publicar meu artigo</a></span></td>
                                                            </tr>
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left" style="padding:0;Margin:0">
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br><br>Para você conhecer melhor a Editora
                                                                        Dialética, seguem abaixo algumas informações
                                                                        adicionais:
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <ul>
                                                                        <li
                                                                            style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;Margin-bottom:15px;color:#333333;font-size:14px">
                                                                            <p
                                                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                                Já são mais de <b>3 mil livros
                                                                                    publicados com alto padrão
                                                                                    editorial</b>.</p>
                                                                        </li>
                                                                    </ul>
                                                                    <ul>
                                                                        <li
                                                                            style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;Margin-bottom:15px;color:#333333;font-size:14px">
                                                                            <p
                                                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                                O propósito é <b>democratizar o acesso
                                                                                    ao conhecimento e valorizar a
                                                                                    ciência </b>por meio da publicação
                                                                                dos principais pesquisadores brasileiros
                                                                                em livros, e-books e audiobooks, além de
                                                                                colaborar com a consolidação da carreira
                                                                                de pesquisadores <b>relevantes como
                                                                                    você.</b></p>
                                                                        </li>
                                                                        <li
                                                                            style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;Margin-bottom:15px;color:#333333;font-size:14px">
                                                                            <p
                                                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                                A qualidade editorial é reforçada pelas
                                                                                parcerias consolidadas com as principais
                                                                                editoras acadêmicas internacionais, como
                                                                                a <b>Oxford University Press</b>, para a
                                                                                tradução e publicação inédita no Brasil
                                                                                de diversos livros relevantes.</p>
                                                                        </li>
                                                                        <li
                                                                            style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;Margin-bottom:15px;color:#333333;font-size:14px">
                                                                            <p
                                                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                                Estamos comprometidos com os mais
                                                                                elevados padrões da produção científica,
                                                                                observando rigorosamente os requisitos
                                                                                da CAPES para a melhor estratificação no
                                                                                "Qualis Livro" (todos os nossos livros
                                                                                são publicados com <b>ISBN, DOI, Ficha
                                                                                    Catalográfica</b> e validados pelo
                                                                                nosso <b>Conselho Editorial</b>).</p>
                                                                        </li>
                                                                    </ul>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        Para saber mais sobre a Dialética, você pode
                                                                        visitar o site <a
                                                                            href="http://www.editoradialetica.com/"
                                                                            style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#0319fb;font-size:14px"><u>www.editoradialetica.com</u></a>
                                                                        e o Instagram <a
                                                                            href="https://www.instagram.com/editoradialetica"
                                                                            style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#0319fb;font-size:14px"><u>@editoradialetica</u></a>.
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        Você também pode acompanhar o status do artigo
                                                                        pela plataforma.&nbsp;</p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        <br>
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px">
                                                                        Caso tenha alguma dúvida, estou à disposição por
                                                                        este e-mail e pelo telefone/WhatsApp que constam
                                                                        na minha assinatura abaixo.</p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="border-collapse:collapse">
                                        <td align="left"
                                            style="padding:0;Margin:0;padding-top:20px;padding-left:0px;padding-right:20px">
                                            <table cellpadding="0" cellspacing="0" width="100%"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr style="border-collapse:collapse">
                                                    <td align="center" valign="top"
                                                        style="padding:0;Margin:0;width:860px">
                                                        <table cellpadding="0" cellspacing="0" width="100%"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr style="border-collapse:collapse">
                                                                <td align="left"
                                                                    style="padding:0;Margin:0;font-size:0px"><img
                                                                        class="adapt-img"
                                                                        src="{{ url('public/img/ass-articles-editor.jpeg') }}"
                                                                        alt
                                                                        style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"
                                                                        width="567"></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table class="es-content" cellspacing="0" cellpadding="0" align="center"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                        <tr style="border-collapse:collapse">
                            <td align="center" style="padding:0;Margin:0">
                                <table class="es-content-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff"
                                    align="center"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:900px">
                                    <tr style="border-collapse:collapse">
                                        <td align="left"
                                            style="padding:0;Margin:0;padding-top:20px;padding-left:0px;padding-right:20px">
                                            <table cellpadding="0" cellspacing="0" width="100%"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr style="border-collapse:collapse">
                                                    <td align="center" valign="top"
                                                        style="padding:0;Margin:0;width:860px">
                                                        <table cellpadding="0" cellspacing="0" width="100%"
                                                            role="presentation"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                            <tr class="es-mobile-hidden" style="border-collapse:collapse">
                                                                <td align="left" style="padding:0;Margin:0">
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:15px;color:#666666;font-size:10px">
                                                                        Esta mensagem pode conter informação
                                                                        confidencial ou privilegiada, sendo seu sigilo
                                                                        protegido por lei. Se você não for o
                                                                        destinatário ou a pessoa autorizada a receber
                                                                        esta mensagem, não pode usar, copiar ou divulgar
                                                                        as informações nela contidas ou tomar qualquer
                                                                        ação baseada nessas informações. Se você recebeu
                                                                        esta mensagem por engano, por favor, avise
                                                                        imediatamente ao remetente, respondendo o e-mail
                                                                        e em seguida apague-a.&nbsp;Agradecemos sua
                                                                        cooperação.</p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:15px;color:#666666;font-size:10px">
                                                                        <br>This message may contain confidential or
                                                                        privileged information and its confidentiality
                                                                        is protected by law. If you are not the
                                                                        addressed or authorized person to receive this
                                                                        message, you must not use, copy, disclose or
                                                                        take any action based on it or any information
                                                                        herein. If you have received this message by
                                                                        mistake, please advise the sender immediately by
                                                                        replying the e-mail and then deleting
                                                                        it.&nbsp;Thank you for your cooperation.
                                                                    </p>
                                                                    <p
                                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:21px;color:#666666;font-size:14px;display:none">
                                                                        <br>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
@endsection

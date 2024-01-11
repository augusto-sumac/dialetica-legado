@layout('mail.layouts.base-layout', ['title' => $name . ', um novo artigo foi submetido para a sua coletãnea!'])

@section('body')
    <?php
    $mail_p_style = 'style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:georgia, times, \'times new roman\', serif;line-height:17px;color:#333333;font-size:14px"';
    $mail_p_break_line = '<p ' . $mail_p_style . '><br></p>';
    ?>

    <p {{ $mail_p_style }}>
        Olá, <strong>{{ $name }}</strong>!
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Como vai?
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        O artigo intitulado <strong>{{ $article_title }}</strong> acaba de ser submetido para a sua coletânea
        <strong>{{ $title }}</strong> e encontra-se
        disponível para a sua avaliação
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Aproveito para lembrar que o artigo já foi avaliado e aprovado pela equipe da editora. Todavia, você também tem a
        liberdade de analisá-lo, aprovando-o ou não.
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Lembrando que o prazo para a sua análise é de {{ $days_approve_article }} dias corridos. Após esse tempo,
        consideraremos que você o aprovou.
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Caso acredito que eu possa ajudar em algo, estou à disposição.
    </p>
@endsection

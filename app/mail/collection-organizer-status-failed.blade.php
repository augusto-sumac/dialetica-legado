@layout('mail.layouts.base-layout', ['title' => $name . ', faltaram artigos para a sua coletânea!'])

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
        Espero que esteja bem!
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        A sua proposta de coletânea intitulada <strong>{{ $title }}</strong> não atingiu o número mínimo de
        {{ $minimum_articles_in_collection }} artigos
        para ser publicada!
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Não se preocupe! Para que os autores de capítulo não ficassem prejudicados, os artigos foram encaminhados para
        publicação nas coletâneas organizadas pela própria editora em fluxo contínuo.
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Todavia, isso não impede que você submeta novamente esta proposta de coletânea ou outras para publicação. Todavia,
        não se esqueça de divulgá-lo para os seus colegas pesquisadores, discentes ou docentes, para que tenhamos ao menos
        {{ $minimum_articles_in_collection }}
        artigos!
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Fico, como sempre, à disposição!
    </p>
@endsection

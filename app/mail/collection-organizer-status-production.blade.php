@layout('mail.layouts.base-layout', ['title' => $name . ', a editoração de sua coletânea se iniciou!'])

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
        Trago boas novas! A sua coletânea intitulada <strong>{{ $title }}</strong> atingiu o número de artigos
        suficientes e o processo editorial para a sua produção acaba de começar! Parabéns!
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Muito em breve a sua coletânea será publicada, mas fique atento: é possível que a nossa produtora editorial entre em
        contato com você para alinhar detalhes.
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Fico à disposição!
    </p>
@endsection

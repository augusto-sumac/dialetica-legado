@layout('mail.layouts.base-layout', ['title' => $name . ', a sua proposta de coletânea previsa de uma revisão!'])

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
        Você submeteu a proposta de coletânea intitulada <strong>{{ $title }}</strong>, e eu agradeço muito por isso.
        No entanto, os nossos analistas consideraram que, antes de ser aprovada, ela precisa passar por uma revisão.
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Não se preocupe! Basta verificar se todas as informações necessárias foram preenchidas por você no momento da
        submissão. Se sim, vale a pena reescrever a proposta e submeter novamente.
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Em caso de qualquer dúvida, fico à disposição.
    </p>
@endsection

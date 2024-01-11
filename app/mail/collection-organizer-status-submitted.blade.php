@layout('mail.layouts.base-layout', ['title' => $name . ', a sua proposta de coletânea foi submetida com sucesso!'])

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
        Como você está? Espero que bem!
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        A sua proposta de coletânea intitulada
        <strong>{{ $title }}</strong> foi submetida
        com sucesso para
        análise.
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Em até <strong>{{ $days_approve_collection }} dias</strong> você receberá um novo
        e-mail comunicando sobre a aprovação ou não de sua
        coletânea. Você pode também acompanhar o status da
        sua proposta na Plataforma.
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Fico à disposição.
    </p>
@endsection

@layout('mail.layouts.base-layout', ['title' => $name . ', a sua proposta de coletânea foi aprovada!'])

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
        É com muita satisfação que tenho o prazer de te comunicar que, após análise do nosso Conselho Editorial, a sua
        proposta de coletânea intitulada <strong>{{ $title }}</strong> foi APROVADA para publicação pela maior
        editora acadêmica do Brasil!
    </p>

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        A partir de agora, você já pode convidar o seu público para publicar na sua coletânea!
    </p>

    @if ($coupon)
        {{ $mail_p_break_line }}

        <p {{ $mail_p_style }}>
            Além da chance de organizar a coletânea, você também agora possui um cupom exclusivo de afiliado, que é o
            seguinte:
            <br>
            <strong>{{ $coupon }}</strong>.
        </p>

        {{ $mail_p_break_line }}

        <p {{ $mail_p_style }}>
            Vale a pena convidar os seus colegas, alunos e colaboradores para enviar os seus textos para a sua coletânea ou
            para
            qualquer outra usando o código acima.
        </p>

        {{ $mail_p_break_line }}

        <p {{ $mail_p_style }}>
            Ao usar o seu código, o seu indicado ganhará {{ $coupon_discount_percent }}% de desconto e você receberá um
            cashback de {{ $coupon_affiliate_percent }}% do valor.
        </p>

        {{ $mail_p_break_line }}

        <p {{ $mail_p_style }}>
            Trata-se de um mecanismo que encontramos para valorizar o pesquisador em suas iniciativas acadêmicas de
            divulgação
            do conhecimento.
        </p>
    @endif

    {{ $mail_p_break_line }}

    <p {{ $mail_p_style }}>
        Em caso de qualquer dúvida, fico à disposição.
    </p>
@endsection

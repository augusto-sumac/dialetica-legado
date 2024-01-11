<div id="article-confirm">

    <input type="hidden" name="id" value="{{ $id }}" />

    <p>
        <span class="fas fa-check fa-fw status-icon bg-success text-white me-1"></span>
        Sua <strong>solicitação de revisão</strong> foi cadastrada com sucesso!
    </p>

    <p>
        Após o pagamento, em até <strong>30 dias</strong> o arquivo revisado e a prova de revisão estarão disponíveis no
        sistema para a
        sua validação.
    </p>

    <p>
        Você receberá uma notificação quando os arquivos estiverem disponíveis. Após a conclusão da revisão, você terá
        até 7 dias para validá-la.
    </p>

    <p>
        Você pode acompanhar o status da sua revisão pela plataforma na área "Minhas Revisões".
    </p>

    <div class="border shadow p-4 rounded mt-5 mb-5">
        <p>
            <span class="fas fa-credit-card fa-fw status-icon bg-info text-white me-1"></span>
            Efetue o <strong>pagamento</strong> para que o processo de revisão seja iniciado!
        </p>

        <p class="m-0">
            <button class="btn btn-lg btn-success goto-payment px-5">
                Pagar Agora
            </button>
        </p>
    </div>

    <a href="{{ url(AUTHOR_REVIEWS_BASE_URL) }}" class="btn btn-sm btn-secondary">
        Minhas revisões
    </a>

</div>

# Propósito e Diagnóstico — Feira Digital

> Esta é a primeira etapa da Pirâmide de Projeto: **Levantamento do Propósito**. Ela
> antecede e fundamenta a [Especificação de Requisitos](./requisitos.md). O diagnóstico
> aqui foi construído por **pesquisa sobre o tema** do desenvolvimento regional e da
> agricultura familiar — não houve vínculo formal com uma entidade específica, modalidade
> prevista pelo guia de extensão ("diagnóstico inicial com a entidade real **e/ou**
> pesquisa sobre o tema").

---

## 1. Enquadramento na Extensão

O projeto se inscreve no **Programa de Desenvolvimento Regional** do guia de extensão da
disciplina, cuja diretriz é *"potencializar a economia local ao conectar pequenos
produtores e consumidores, mitigando a dependência de grandes atravessadores"*, tendo como
funcionalidade principal um **marketplace de produtores locais com catálogo de produtos**.

A escolha responde ao princípio da **interação dialógica**: o conhecimento técnico
(Programação Web II) é colocado a serviço de uma demanda concreta da comunidade do
Alto Vale do Itajaí, região de forte presença da agricultura familiar.

---

## 2. O Problema

O pequeno produtor rural e o artesão local enfrentam uma assimetria estrutural na hora de
escoar a produção:

- **Dependência de atravessadores.** Boa parte da renda do produtor é capturada por
  intermediários entre a propriedade e o consumidor final, comprimindo a margem de quem
  produz.
- **Baixa visibilidade digital.** O pequeno produtor raramente tem presença on-line
  própria; quando tem, está pulverizado em redes sociais sem vitrine organizada por
  produto, preço e disponibilidade.
- **Distância do consumidor local.** O consumidor que *gostaria* de comprar direto da
  origem — por preço, frescor ou consciência de consumo — não dispõe de um lugar único
  para descobrir quem produz o quê na sua própria região.
- **Barreira tecnológica.** Soluções de marketplace genéricas são caras, cobram comissão
  e pressupõem logística e meios de pagamento que o pequeno produtor não tem.

O efeito combinado é econômico (renda menor para quem produz) e social (enfraquecimento da
economia e da identidade regional).

---

## 3. Quem é afetado (públicos)

| Público                     | Dor principal                                              |
|-----------------------------|------------------------------------------------------------|
| Pequeno produtor / agricultor familiar | Escoar a produção sem perder margem para atravessadores e sem barreira técnica |
| Produtor artesanal          | Dar visibilidade a um produto autoral para um público que o valorize |
| Consumidor local            | Encontrar e comprar direto da origem, com confiança na reputação de quem vende |

---

## 4. Objetivos

### 4.1. Objetivo social
Aproximar produtor e consumidor da mesma região, encurtando a cadeia entre quem produz e
quem consome, fortalecendo a economia e a identidade locais.

### 4.2. Objetivos de negócio (do produto)
1. Dar a cada produtor uma **vitrine digital gratuita**, organizada por produto, preço,
   unidade e disponibilidade.
2. Permitir ao consumidor **descobrir** produtores por categoria, cidade, busca e
   ordenação — **sem exigir cadastro** para navegar.
3. Viabilizar o **contato direto** produtor↔consumidor (WhatsApp), sem intermediação nem
   comissão da plataforma.
4. Construir **confiança** por meio de avaliações de reputação do produtor.

---

## 5. Da dor ao requisito (rastreabilidade)

A tabela liga cada dor diagnosticada à decisão de produto correspondente na
[Especificação de Requisitos](./requisitos.md), evidenciando que a codificação partiu da
análise — e não o contrário.

| Dor diagnosticada                         | Resposta no produto                                  | Requisitos       |
|-------------------------------------------|------------------------------------------------------|------------------|
| Dependência de atravessadores             | Contato direto via WhatsApp, sem comissão            | RF-09            |
| Baixa visibilidade digital do produtor    | Vitrine pública: perfil + catálogo por produtor      | RF-07, RF-08, RF-16 |
| Consumidor não acha quem produz na região | Catálogo público com filtro por categoria/cidade, busca e ordenação | RF-01 a RF-06 |
| Barreira tecnológica / custo              | Cadastro gratuito; sem checkout/pagamento (fora de escopo) | RF-10, RF-14 |
| Falta de confiança em quem vende          | Avaliações de reputação do produtor                  | RF-22, RF-23, RF-08 |
| Consumidor quer acompanhar produtores     | Favoritos                                            | RF-20, RF-21     |

---

## 6. Delimitação

A plataforma é uma **vitrine e ponto de descoberta e contato**, não um sistema de comércio
eletrônico transacional. Pagamento, carrinho, logística de entrega e intermediação
financeira estão deliberadamente fora do escopo — tanto pela complexidade jurídica e
operacional quanto por fidelidade ao propósito: a Feira Digital aproxima as pontas e sai
do caminho. A negociação acontece diretamente entre produtor e consumidor. As demais
exclusões de escopo estão registradas na [Especificação de Requisitos](./requisitos.md#5-exclusões-de-escopo).

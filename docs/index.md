# Feira Digital — Documentação do Projeto

Marketplace de produtores locais desenvolvido como Projeto de Extensão da disciplina
**Programação Web II** — Bacharelado em Sistemas de Informação, Unidavi.

**Professor:** M.e Sandro Alencar Fernandes

---

## Índice

**Concepção e especificação** (a base da Pirâmide de Projeto, anterior à implementação):

- [Propósito e Diagnóstico](./proposito.md)
- [Requisitos do Sistema](./requisitos.md)
- [Arquitetura e Decisões Técnicas](./arquitetura.md)
- [Rotas da Aplicação](./rotas.md)
- [Fluxos de Requisição](./fluxos-de-requisicao.md)

**Histórico de execução** (sprints entregues):

- [Sprint 1 — Infraestrutura e Fundação](./sprints/sprint-01.md)
- [Sprint 2 — Autenticação do Produtor](./sprints/sprint-02.md)
- [Sprint 3 — Gestão de Produtos](./sprints/sprint-03.md)
- [Sprint 4 — Catálogo Público](./sprints/sprint-04.md)
- [Sprint 5 — Interface, Validações e Documentação](./sprints/sprint-05.md)
- [Sprint 6 — Overhaul de Interface e Design System](./sprints/sprint-06.md)
- [Sprint 7 — Catálogo e Descoberta](./sprints/sprint-07.md)
- [Sprint 8 — Conta de Comprador e Engajamento Social](./sprints/sprint-08.md)
- [Sprint 10 — Avaliações: Visibilidade e Curadoria](./sprints/sprint-10.md)

> A [Sprint 9 — Dashboard Avançado](./sprints/sprint-09.md) foi planejada como evolução
> **possível** (métricas, estoque, preço promocional, contato por e-mail, painel admin),
> mas **não** foi implementada no escopo entregue. Os itens estão registrados como backlog
> futuro e nas [exclusões de escopo](./requisitos.md#5-exclusões-de-escopo).

---

## Visão Geral

**Feira Digital** conecta pequenos produtores rurais e artesanais a consumidores locais,
oferecendo uma vitrine digital sem intermediários. O sistema permite que produtores cadastrem
e gerenciem suas ofertas, e que o público as encontre sem a necessidade de criar uma conta.

O projeto se enquadra no **Programa de Desenvolvimento Regional** proposto pelo guia de extensão,
com foco em potencializar a economia local ao eliminar a dependência de atravessadores.
O diagnóstico do problema e os objetivos sociais estão detalhados em
[Propósito e Diagnóstico](./proposito.md).

---

## Stack Tecnológica

| Camada           | Tecnologia                  |
|------------------|-----------------------------|
| Backend          | PHP 8.5 + Laravel 11        |
| Frontend         | Blade (SSR) + SCSS via Vite |
| ORM              | Eloquent                    |
| Banco de dados   | MySQL 8                     |
| Infraestrutura   | Docker via Laravel Sail     |
| Autenticação     | Laravel Breeze (Blade)      |
| Build de assets  | Vite                        |

---

## Fluxo Resumido de Usuários

```
Visitante
  └── Navega catálogo, filtra por categoria/cidade/ordenação, busca produto e produtor
        └── [Nenhum cadastro necessário]
        └── Contato direto com o produtor via link de WhatsApp

Comprador
  └── /register (papel "comprador") → verifica e-mail → acessa a home
        └── Favorita produtos e avalia produtores (1–5 estrelas + comentário)

Produtor
  └── /register (papel "produtor") → preenche dados → verifica e-mail
        └── Completa perfil do negócio (setup único)
              └── Dashboard → gerencia produtos (CRUD + disponibilidade + destaque)
                            → cura as avaliações recebidas (ocultar/exibir)
```

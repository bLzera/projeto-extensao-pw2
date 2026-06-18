# Feira Digital — Documentação do Projeto

Marketplace de produtores locais desenvolvido como Projeto de Extensão da disciplina
**Programação Web II** — Bacharelado em Sistemas de Informação, Unidavi.

**Professor:** M.e Sandro Alencar Fernandes

---

## Índice

- [Requisitos do Sistema](./requisitos.md)
- [Arquitetura e Decisões Técnicas](./arquitetura.md)
- [Rotas da Aplicação](./rotas.md)
- [Fluxos de Requisição](./fluxos-de-requisicao.md)
- [Sprint 1 — Infraestrutura e Fundação](./sprints/sprint-01.md)
- [Sprint 2 — Autenticação do Produtor](./sprints/sprint-02.md)
- [Sprint 3 — Gestão de Produtos](./sprints/sprint-03.md)
- [Sprint 4 — Catálogo Público](./sprints/sprint-04.md)
- [Sprint 5 — Interface, Validações e Documentação](./sprints/sprint-05.md)
- [Sprint 6 — Overhaul de Interface e Design System](./sprints/sprint-06.md)
- [Sprint 7 — Catálogo e Descoberta](./sprints/sprint-07.md)
- [Sprint 8 — Conta de Comprador e Engajamento Social](./sprints/sprint-08.md)
- [Sprint 9 — Dashboard Avançado (possível)](./sprints/sprint-09.md)

---

## Visão Geral

**Feira Digital** conecta pequenos produtores rurais e artesanais a consumidores locais,
oferecendo uma vitrine digital sem intermediários. O sistema permite que produtores cadastrem
e gerenciem suas ofertas, e que o público as encontre sem a necessidade de criar uma conta.

O projeto se enquadra no **Programa de Desenvolvimento Regional** proposto pelo guia de extensão,
com foco em potencializar a economia local ao eliminar a dependência de atravessadores.

---

## Stack Tecnológica

| Camada           | Tecnologia                  |
|------------------|-----------------------------|
| Backend          | PHP 8.3 + Laravel 11        |
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
        └── Pode enviar mensagem de contato ao produtor (Sprint 9)

Comprador (Sprint 8+)
  └── Acessa /register/comprador → verifica e-mail → acessa conta
        └── Salva favoritos, deixa avaliações de produtos

Produtor
  └── Acessa /register → preenche dados → verifica e-mail
        └── Completa perfil do negócio (setup único)
              └── Acessa dashboard → gerencia produtos (CRUD + destaques)
```

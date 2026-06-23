// Filtros do catálogo (home) com AJAX: troca apenas o conteúdo do catálogo
// sem recarregar a página inteira e mantém a URL sincronizada.
export function catalogFilters() {
  return {
    openMenu: null,
    loading: false,

    init() {
      // Voltar/avançar do navegador: re-renderiza o estado da URL atual
      // sem empilhar novo histórico.
      window.addEventListener('popstate', () => this.load(location.href, false));
    },

    // Acionado pelas opções de filtro/ordenação e pela paginação.
    apply(url) {
      this.openMenu = null;
      this.load(url, true);
    },

    // Captura cliques na paginação (re-renderizada a cada troca de conteúdo).
    onContentClick(e) {
      const link = e.target.closest('.pagination a');
      if (link) {
        e.preventDefault();
        this.apply(link.href);
      }
    },

    async load(url, push) {
      this.loading = true;
      try {
        const res = await fetch(url, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        this.$refs.content.innerHTML = await res.text();
        window.Alpine.initTree(this.$refs.content);

        if (push) history.pushState({}, '', url);

        this.$refs.content.scrollIntoView({ behavior: 'smooth', block: 'start' });
      } catch (e) {
        console.error('Erro ao filtrar o catálogo', e);
        window.location.href = url; // fallback: refresh tradicional
      } finally {
        this.loading = false;
      }
    },
  };
}

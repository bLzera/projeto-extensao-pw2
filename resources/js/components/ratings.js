import { patchJson } from "../https";

export function ratingRow(initialHidden, toggleUrl) {
  return {
    hidden: initialHidden,
    loading: false,

    async toggle() {
      this.loading = true;
      try {
        const data = await patchJson(toggleUrl)
        this.hidden = data.hidden;
        this.$dispatch('ratings-visibility-count-change', {count: data.count});        
      } catch(e) {
        console.error('Erro ao alternar visibilidade', e);
      } finally {
        this.loading = false;
      }
    },

    onBulkChange(hidden){
      this.hidden = hidden;
    }    
  }
}

export function ratingBulk(toggleUrl) {
  return {
    loading: false,

    async toggleAll(hidden) {
      this.loading = true;

      try {
        const data = await patchJson(toggleUrl, { hidden });
        this.$dispatch('ratings-visibility-change', {hidden: data.hidden});
        this.$dispatch('ratings-visibility-count-change', {count: data.count});
      } catch(e) {
        console.error('Erro ao alternar a visibilidade de todas as avaliações', e);
      } finally {
        this.loading = false;
      }
    }
  } 
}

export function ratingVisibilityCount(initialCount){
  return {
    count: initialCount,

    onVisibilityChange(count) {
      this.count = count;
    }
  }
}
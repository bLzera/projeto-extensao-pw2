import { patchJson } from "../https";

export function productCard(config) {
  return {
    featuredLoading: false,
    availableLoading: false,

    featured: config.is_featured,
    available: config.is_available,

    async toggleFeatured() {
      this.featuredLoading = true;
      try{
        const data = await patchJson(config.featuredUrl);
        this.featured = data.is_featured;
      } catch(e) {
        console.error(`Erro ao alternar destaque`, e);
      } finally {
        this.featuredLoading = false;
      }
    },

    async toggleAvailable() {
      this.availableLoading = true;
      try{
        const data = await patchJson(config.availableUrl);
        this.available = data.is_available;
        this.$dispatch('products-available-count-change', { count: data.available_count });
      } catch(e) {
        console.error('Erro ao alternar disponibilidade do produto', e);
      } finally {
        this.availableLoading = false;
      }
    }
  }
}

export function productAvailableCount(initialCount) {
  return {
    count: initialCount,

    onAvailableChange(count){
      this.count = count;
    }
  }
}
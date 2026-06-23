import Alpine from 'alpinejs';
import { ratingBulk, ratingRow, ratingVisibilityCount } from './components/ratings';
import { productAvailableCount, productCard } from './components/products';
import { catalogFilters } from './components/catalog';
window.Alpine = Alpine;

const csrf = document.querySelector('meta[name="csrf-token"]').content;

Alpine.data('ratingRow', ratingRow);
Alpine.data('ratingBulk', ratingBulk);
Alpine.data('productCard', productCard);
Alpine.data('ratingVisibilityCount', ratingVisibilityCount);
Alpine.data('productAvailableCount', productAvailableCount);
Alpine.data('catalogFilters', catalogFilters);

Alpine.start();
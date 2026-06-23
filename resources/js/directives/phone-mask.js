// Diretiva Alpine `x-phone-mask`: formata telefones brasileiros enquanto o
// usuário digita. Usa (00) 0000-0000 para fixo (até 10 dígitos) e expande para
// (00) 00000-0000 quando há o 9 inicial de celular (11 dígitos).
function format(value) {
  const digits = value.replace(/\D/g, '').slice(0, 11);

  if (digits.length === 0) return '';

  const ddd = digits.slice(0, 2);
  const rest = digits.slice(2);

  if (digits.length <= 2) return `(${ddd}`;

  // Ponto de quebra do hífen: 4 dígitos no fixo, 5 no celular (11 dígitos).
  const split = digits.length > 10 ? 5 : 4;
  const prefix = rest.slice(0, split);
  const suffix = rest.slice(split);

  return suffix ? `(${ddd}) ${prefix}-${suffix}` : `(${ddd}) ${prefix}`;
}

export function phoneMask(el) {
  // Formata o valor inicial vindo do banco (já mascarado ou não).
  el.value = format(el.value);

  el.addEventListener('input', () => {
    el.value = format(el.value);
  });
}

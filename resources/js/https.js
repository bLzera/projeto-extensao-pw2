const csrf = document.querySelector('meta[name="csrf-token"]').content;

export async function patchJson(url, body = null) {
  const res = await fetch(url, {
    method: 'PATCH',
    headers: {
      'X-CSRF-TOKEN': csrf,
      'Accept': 'application/json',
      'Content-Type': body ? 'application/json' : undefined,
    },
    body: body ? JSON.stringify(body) : undefined,
  });

  if(!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}
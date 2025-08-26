import './bootstrap'

/* ==== helpers ==== */
const $  = (s) => document.querySelector(s)
const $$ = (s) => document.querySelectorAll(s)
const esc = (x) => String(x ?? '')
  .replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;')
  .replaceAll('"','&quot;').replaceAll("'",'&#039;')
const debounce = (fn, ms=250) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms) }}

/* ==== state ==== */
const state = {
  q: '',
  category: 'all',
  sub: {},            // meta filters: {key: value}
  page: 1,
  perPage: 12,
  total: 0,
  lastPage: 1,
}
const LS_KEY = 'iso.db.filters.v1'

function persist(){ try{ localStorage.setItem(LS_KEY, JSON.stringify(state)) }catch{} }
function hydrate(){
  try {
    const raw = localStorage.getItem(LS_KEY); if(!raw) return
    const saved = JSON.parse(raw)
    state.q = saved.q ?? ''
    state.category = saved.category ?? 'all'
    state.sub = saved.sub ?? {}
    state.page = saved.page ?? 1
    state.perPage = saved.perPage ?? 12
    const s = $('#searchInput'); if (s) s.value = state.q
  }catch{}
}

/* ==== API ==== */
async function fetchFacets(){
    const params = new URLSearchParams()
    if (state.q) params.set('q', state.q)
    if (state.category && state.category !== 'all') params.set('category', state.category)
    for (const [k,v] of Object.entries(state.sub)) if (v && v !== 'all') params.set(k, v)

    const r = await fetch('/api/services/facets?'+params.toString(), { headers: { 'Accept':'application/json' } })
    if(!r.ok) throw new Error('Gagal memuat facets')
    return await r.json()
}

async function fetchList(){
  const params = new URLSearchParams()
  if (state.q) params.set('q', state.q)
  if (state.category && state.category !== 'all') params.set('category', state.category)
  params.set('page', String(state.page))
  params.set('per_page', String(state.perPage))
  for (const [k,v] of Object.entries(state.sub)) if (v && v !== 'all') params.set(k, v)

  const r = await fetch('/api/services?'+params.toString(), { headers: { 'Accept':'application/json' } })
  if(!r.ok) throw new Error('Gagal memuat data')
  const json = await r.json()
  state.total = json.meta.total
  state.lastPage = json.meta.last_page
  return json.data // array of services
}

/* ==== UI builders ==== */
function buildCategories(categories=[]) {
  const el = $('#catWrap'); if (!el) return
  const cats = ['Semua', ...categories]
  el.innerHTML = `
    <div class="flex flex-wrap items-center gap-3">
      <div class="flex items-center gap-2">
        <label for="catSelect" class="text-xs text-slate-500">Kategori</label>
        <select id="catSelect" class="rounded-md border border-slate-300 px-3 py-2">
          ${cats.map(c => {
            const val = (c === 'Semua') ? 'all' : c
            return `<option value="${esc(val)}"${state.category===val?' selected':''}>${esc(c)}</option>`
          }).join('')}
        </select>
      </div>

      <div class="flex items-center gap-2">
        <label for="perPageSelect" class="text-xs text-slate-500">Per halaman</label>
        <select id="perPageSelect" class="rounded-md border border-slate-300 px-2 py-2">
          ${[6,9,12,18,24].map(n => `<option value="${n}"${state.perPage===n?' selected':''}>${n}</option>`).join('')}
        </select>
      </div>
    </div>
  `
}

function buildSubFilters(facets) {
  const wrap = $('#subWrap'); if (!wrap) return
  const groups = facets?.metadata_facets || {}

  // Urutkan key: prioritas dulu, lalu alfabet (backup kalau backend tidak urut)
  const keys = Object.keys(groups).sort((a,b)=>{
    const pri = {'nama-akreditasi':-2, 'jenis-iso':-1}
    return (pri[a]??0)-(pri[b]??0) || a.localeCompare(b)
  })

  if (!keys.length) { wrap.innerHTML = ''; return }

  const PRIMARY_N = 3
  const primary = keys.slice(0, PRIMARY_N)
  const more    = keys.slice(PRIMARY_N)

  const makeSelect = (key) => {
    const label = key.replace(/[-_]/g,' ').replace(/\b\w/g, m=>m.toUpperCase())
    const current = state.sub[key] ?? 'all'
    const pairs = groups[key] || [] // array of [val, count]
    const opts = [`<option value="all"${current==='all'?' selected':''}>Semua</option>`]
      .concat(pairs.map(([v,c]) => `<option value="${esc(v)}"${current===v?' selected':''}>${esc(v)} (${c})</option>`))
      .join('')
    return `
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-slate-600" for="sel-${esc(key)}">${esc(label)}</label>
        <select id="sel-${esc(key)}" class="rounded-md border border-slate-300 px-3 py-2" data-sub="${esc(key)}">
          ${opts}
        </select>
      </div>
    `
  }

  wrap.innerHTML = `
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
      ${primary.map(makeSelect).join('')}
      ${more.length ? `<button id="toggleMoreFilters" class="rounded-md border border-slate-300 px-3 py-2 text-left sm:col-span-2 lg:col-span-3">
        + Filter lainnya (${more.length})
      </button>` : ''}
    </div>
    ${more.length ? `
      <div id="moreFilters" class="mt-3 hidden">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
          ${more.map(makeSelect).join('')}
        </div>
      </div>
    ` : ''}
  `
}


function renderResultInfo(countOnPage){
  const el = $('#resultCount'); if(!el) return
  el.textContent = `Menampilkan ${countOnPage} dari ${state.total} hasil`
}

function renderGrid(items){
  const grid = $('#certificationGrid'); if(!grid) return
  if (!items.length) {
    grid.innerHTML = `<div class="col-span-full text-center text-slate-500">Tidak ada hasil. Ubah filter.</div>`
    return
  }
  grid.innerHTML = items.map(c => `
    <article class="group bg-white rounded-xl border border-slate-200 p-5">
      <div class="text-[11px] font-semibold uppercase tracking-wider text-blue-700">
        ${esc((c.metadata?.['jenis-iso']) ?? 'ISO')}
      </div>
      <h3 class="mt-1 text-lg md:text-xl font-bold text-slate-900 leading-snug">
        <a href="/sertifikasi/${esc(c.slug)}" class="hover:underline">${esc(c.title)}</a>
      </h3>
      <p class="mt-2 text-sm text-slate-600 line-clamp-3">
        ${esc(c.short_description ?? (c.description ?? '').slice(0,140))}
      </p>
      <div class="mt-4 flex flex-wrap gap-2 text-xs">
        ${c.category ? `<span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700">#${esc(c.category)}</span>` : ''}
        ${c.metadata?.['nama-akreditasi'] ? `<span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700">${esc(c.metadata['nama-akreditasi'])}</span>` : ''}
      </div>
      <div class="mt-5 flex items-center justify-end">
        <a href="/sertifikasi/${esc(c.slug)}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Detail</a>
      </div>
    </article>
  `).join('')
}

function renderPagination(){
  const el = $('#pagination'); if(!el) return
  const cur = state.page, total = state.lastPage
  const btn = (label, page, disabled=false, active=false) => `
    <button class="min-w-9 h-9 px-3 rounded-md border ${active?'bg-blue-600 text-white border-blue-600':'border-slate-300 hover:border-slate-400'} ${disabled?'opacity-50 cursor-not-allowed':''}"
      data-page="${disabled?'':page}">${esc(label)}</button>`
  const parts = []
  parts.push(btn('‹', cur-1, cur===1))
  const maxBtns = 7
  let pages = []
  if (total <= maxBtns) pages = Array.from({length: total}, (_,i)=>i+1)
  else {
    const start = Math.max(1, cur-2)
    const end = Math.min(total, start+4)
    pages.push(1); if(start>2) pages.push('…')
    for(let p=start;p<=end;p++) pages.push(p)
    if(end<total-1) pages.push('…'); pages.push(total)
  }
  for (const p of pages) parts.push(p==='…' ? `<span class="px-2 text-slate-400">…</span>` : btn(String(p), p, false, p===cur))
  parts.push(btn('›', cur+1, cur===total))
  el.innerHTML = parts.join('')
}

/* ==== Active filter chips ==== */
function renderActive(){
  const box = $('#activeFilters'); if (!box) return
  const chips = []
  if (state.q) chips.push(`<button class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs" data-clear="q">Cari: ${esc(state.q)} ✕</button>`)
  if (state.category !== 'all') chips.push(`<button class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs" data-clear="category">Kategori: ${esc(state.category)} ✕</button>`)
  for (const [k,v] of Object.entries(state.sub)) if (v && v !== 'all')
    chips.push(`<button class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs" data-clear="sub:${esc(k)}">${esc(k)}: ${esc(v)} ✕</button>`)
  box.innerHTML = chips.length ? chips.join(' ') + ` <button id="clearAllFilters" class="px-2 py-1 rounded-md border text-xs border-slate-300 hover:border-slate-400">Bersihkan Semua</button>`
                              : `<span class="text-slate-400 text-sm">Tidak ada filter.</span>`
}

/* ==== Orkestrasi ==== */
async function refresh(){
  const items = await fetchList()
  renderGrid(items)
  renderPagination()
  renderResultInfo(items.length)
  renderActive()
  persist()
}

async function refreshFacets(){
  const facets = await fetchFacets()
  buildSubFilters(facets)
}

function attachEvents(facets){
    $('#searchInput')?.addEventListener('input', debounce(async (e)=>{
    state.q = e.target.value.trim()
    state.page = 1
    await refreshFacets()
    await refresh()
    }, 250))
    $('#catWrap')?.addEventListener('change', async (e)=>{
    if (e.target.id === 'catSelect') {
        state.category = e.target.value || 'all'
        state.page = 1
        // reset sub-filter saat ganti kategori biar fokus
        state.sub = {}
        await refreshFacets()
        await refresh()
        return
    }
    if (e.target.id === 'perPageSelect') {
        const n = parseInt(e.target.value,10); if(!isNaN(n)) state.perPage = n
        state.page = 1
        await refresh()
        return
    }
    })
  $('#resetFiltersBtn')?.addEventListener('click', ()=>{ state.q=''; state.category='all'; state.sub={}; state.page=1; const s=$('#searchInput'); if(s) s.value=''; refresh() })
    $('#subWrap')?.addEventListener('change', async (e)=>{
    const sel = e.target.closest('select[data-sub]'); if(!sel) return
    const key = sel.getAttribute('data-sub'); state.sub[key] = sel.value || 'all'
    state.page = 1
    await refreshFacets()
    await refresh()
    })
    $('#subWrap')?.addEventListener('click', (e)=>{
    const btn = e.target.closest('#toggleMoreFilters'); if (!btn) return
    const panel = document.getElementById('moreFilters')
    if (panel) panel.classList.toggle('hidden')
    })

    $('#activeFilters')?.addEventListener('click', async (e)=>{
    const btn=e.target.closest('button[data-clear],#clearAllFilters'); if(!btn) return
    if (btn.id==='clearAllFilters'){
        state.q=''; state.category='all'; state.sub={}; state.page=1
        const s=$('#searchInput'); if(s) s.value=''
        await refreshFacets()
        await refresh()
        return
    }
    const key=btn.getAttribute('data-clear')
    if(key==='q'){ state.q=''; const s=$('#searchInput'); if(s) s.value='' }
    else if(key==='category'){ state.category='all'; state.sub={} }
    else if(key.startsWith('sub:')){ const k=key.split(':')[1]; state.sub[k]='all' }
    state.page=1
    await refreshFacets()
    await refresh()
    })
  $('#pagination')?.addEventListener('click',(e)=>{
    const b=e.target.closest('button[data-page]'); if(!b) return
    const p=parseInt(b.getAttribute('data-page')||'',10)
    if(!isNaN(p)){ state.page=p; refresh(); document.querySelector('#certificationGrid')?.scrollIntoView({behavior:'smooth',block:'start'}) }
  })

  // Inisialisasi kategori dari SSR kalau ada
  const cats = (window.__CATS__ || facets.categories || [])
  buildCategories(cats)
  buildSubFilters(facets)

  // Smooth scroll untuk tombol CTA yang punya data-scroll
    document.addEventListener('click', (e)=>{
    const btn = e.target.closest('[data-scroll]')
    if(!btn) return
    const sel = btn.getAttribute('data-scroll')
    const target = document.querySelector(sel)
    if (target) { e.preventDefault(); target.scrollIntoView({behavior:'smooth', block:'start'}) }
    })

    // Toggle mobile menu (sesuai id di Blade-mu)
    const mbtn = document.getElementById('mobile-menu-btn')
    const mnav = document.getElementById('mobile-menu')
    if (mbtn && mnav) {
    mbtn.addEventListener('click', ()=> mnav.classList.toggle('hidden'))
    // klik link di mobile menu → auto-hide
    mnav.addEventListener('click', (e)=>{
        if (e.target.closest('a')) mnav.classList.add('hidden')
    })
    }

}

/* ==== Boot ==== */
document.addEventListener('DOMContentLoaded', async ()=>{
  try{
    hydrate()
    const facets = await fetchFacets()
    attachEvents(facets)
    await refresh()
  }catch(err){
    console.error(err)
    const grid = $('#certificationGrid'); if(grid) grid.innerHTML = `<div class="col-span-full text-center text-red-600">${esc(err.message||'Gagal memuat')}</div>`
  }
})

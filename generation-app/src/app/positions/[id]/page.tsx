'use client';

import { useEffect, useState } from 'react';

interface Position {
  id: string;
  title: string;
  seoTitle: string;
  primaryKeyword: string;
  secondaryKeywords: string[];
  category: string;
  materials: string;
  fit: string;
  printMethod: string;
  sizes: string;
  colors: string;
  price: string;
  extraNotes: string;
  slug: string;
  description: string;
  shortDescription: string;
  metaTitle: string;
  seoDescription: string;
  tags: string[];
  imagesAlt: string[];
  imageFilenames: string[];
  keywordsUsed: { primary?: string; secondary?: string[] } | null;
  warnings: string[];
  status: 'DRAFT' | 'GENERATED' | 'APPROVED';
  imageMime: string | null;
}

interface Violation {
  rule: string;
  detail: string;
}

interface Translation {
  description: string;
  shortDescription: string;
  metaTitle: string;
  metaDescription: string;
  tags: string[];
  imagesAlt: string[];
}

const META_TITLE_MAX = 70;
const META_DESC_MAX = 165;

function Counter({ value, max }: { value: string; max: number }) {
  return (
    <span className={`counter ${value.length > max ? 'over' : ''}`}>
      {value.length} / {max} characters
    </span>
  );
}

export default function PositionCard({ params }: { params: { id: string } }) {
  const [p, setP] = useState<Position | null>(null);
  const [busy, setBusy] = useState(false);
  const [gen, setGen] = useState(false);
  const [error, setError] = useState('');
  const [violations, setViolations] = useState<Violation[]>([]);
  const [ok, setOk] = useState('');
  const [metaTitle, setMetaTitle] = useState('');
  const [metaDesc, setMetaDesc] = useState('');
  // Russian preview: a reading aid only. Never saved, never exported — the storefront
  // is English, so the English copy stays the single source of truth.
  const [ru, setRu] = useState<Translation | null>(null);
  const [translating, setTranslating] = useState(false);

  async function load() {
    const res = await fetch(`/api/positions/${params.id}`);
    if (res.ok) {
      const data: Position = await res.json();
      setP(data);
      setMetaTitle(data.metaTitle);
      setMetaDesc(data.seoDescription);
    }
  }
  useEffect(() => {
    load();
  }, []);

  async function save(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setBusy(true);
    setError('');
    setOk('');
    const form = new FormData(e.currentTarget);
    // Multi-value fields arrive as one textarea each (newline / comma separated).
    for (const [raw, field] of [
      ['secondaryRaw', 'secondaryKeywords'],
      ['tagsRaw', 'tags'],
      ['altRaw', 'imagesAlt'],
      ['filesRaw', 'imageFilenames'],
    ] as const) {
      const value = String(form.get(raw) || '');
      form.delete(raw);
      value
        .split(/[\n,]/)
        .map((s) => s.trim())
        .filter(Boolean)
        .forEach((k) => form.append(field, k));
    }

    const res = await fetch(`/api/positions/${params.id}`, { method: 'PATCH', body: form });
    setBusy(false);
    if (res.ok) {
      setOk('Saved');
      load();
    } else {
      const { error } = await res.json().catch(() => ({ error: 'Failed' }));
      setError(error);
    }
  }

  async function generate() {
    setGen(true);
    setError('');
    setOk('');
    setViolations([]);
    const res = await fetch(`/api/positions/${params.id}/generate`, { method: 'POST' });
    setGen(false);
    const data = await res.json().catch(() => null);
    if (!res.ok || !data) {
      setError(data?.error || 'Generation failed');
      return;
    }
    setViolations(data.violations || []);
    if (data.ok) setOk(`Generated cleanly in ${data.attempts} attempt(s) — every rule passed.`);
    load();
  }

  async function exportWc() {
    const res = await fetch(`/api/positions/${params.id}/woocommerce`);
    const data = await res.json();
    if (!res.ok) {
      setError(data.error);
      return;
    }
    await navigator.clipboard.writeText(JSON.stringify(data.payload, null, 2));
    setOk('WooCommerce payload copied to clipboard.');
  }

  async function translate() {
    if (ru) {
      setRu(null); // toggle the panel off
      return;
    }
    setTranslating(true);
    setError('');
    const res = await fetch(`/api/positions/${params.id}/translate`, { method: 'POST' });
    setTranslating(false);
    const data = await res.json().catch(() => null);
    if (!res.ok || !data) {
      setError(data?.error || 'Translation failed');
      return;
    }
    setRu(data);
  }

  async function remove() {
    if (!confirm('Delete this position?')) return;
    await fetch(`/api/positions/${params.id}`, { method: 'DELETE' });
    window.location.href = '/';
  }

  if (!p) return <div className="empty">Loading…</div>;

  return (
    <>
      <a href="/" className="hint">
        ← All positions
      </a>
      <h1 style={{ marginTop: 10 }}>
        {p.title} <span className={`badge ${p.status}`}>{p.status}</span>
      </h1>

      {error && <div className="alert err">{error}</div>}
      {ok && <div className="alert ok">{ok}</div>}

      {violations.length > 0 && (
        <div className="alert err">
          <strong>Rejected — the copy still breaks hard rules after 3 attempts.</strong>
          <ul>
            {violations.map((v, i) => (
              <li key={i}>
                <code>{v.rule}</code> — {v.detail}
              </li>
            ))}
          </ul>
          Kept as a DRAFT, not published. Adjust the inputs and generate again.
        </div>
      )}

      {p.warnings.length > 0 && (
        <div className="alert warn">
          <strong>Notes from the generator</strong>
          <ul>
            {p.warnings.map((w, i) => (
              <li key={i}>{w}</li>
            ))}
          </ul>
        </div>
      )}

      <form onSubmit={save}>
        {/* ---------- inputs ---------- */}
        <div className="card">
          <h2 style={{ marginTop: 0 }}>Inputs</h2>
          <div className="grid">
            <div>
              <label>Print image</label>
              {p.imageMime ? (
                <img className="preview" src={`/api/positions/${p.id}/image`} alt="" />
              ) : (
                <div className="alert warn">No image — the copy is written from it.</div>
              )}
              <label htmlFor="image">Replace image</label>
              <input id="image" type="file" name="image" accept="image/*" />
            </div>

            <div>
              <label htmlFor="title">Title — main print name</label>
              <input id="title" type="text" name="title" defaultValue={p.title} />

              <label htmlFor="seoTitle">SEO title (desired)</label>
              <input id="seoTitle" type="text" name="seoTitle" defaultValue={p.seoTitle} />

              <label htmlFor="primaryKeyword">Primary keyword</label>
              <input id="primaryKeyword" type="text" name="primaryKeyword" defaultValue={p.primaryKeyword} />
              <p className="hint">
                Used exactly once in each of: product name, first paragraph, meta title, meta
                description. Unique across all positions — no cannibalization.
              </p>

              <label htmlFor="secondaryRaw">Secondary keywords</label>
              <textarea
                id="secondaryRaw"
                name="secondaryRaw"
                defaultValue={p.secondaryKeywords.join('\n')}
                style={{ minHeight: 80 }}
              />
              <p className="hint">Each at most once, description only. Any that do not fit are dropped, not forced.</p>
            </div>
          </div>
        </div>

        {/* ---------- product facts ---------- */}
        <div className="card">
          <h2 style={{ marginTop: 0 }}>Product facts</h2>
          <p className="hint" style={{ marginTop: 0 }}>
            Supplied to the model so it states these instead of inventing specs. Leave blank
            and it stays silent about them.
          </p>
          <div className="grid" style={{ gridTemplateColumns: '1fr 1fr' }}>
            <div>
              <label htmlFor="category">Category</label>
              <input id="category" type="text" name="category" defaultValue={p.category} placeholder="Graphic Tees" />
              <label htmlFor="materials">Materials</label>
              <input id="materials" type="text" name="materials" defaultValue={p.materials} placeholder="100% combed cotton, 180gsm" />
              <label htmlFor="fit">Fit</label>
              <input id="fit" type="text" name="fit" defaultValue={p.fit} placeholder="unisex, oversized" />
              <label htmlFor="printMethod">Print method</label>
              <input id="printMethod" type="text" name="printMethod" defaultValue={p.printMethod} placeholder="DTG" />
            </div>
            <div>
              <label htmlFor="sizes">Sizes</label>
              <input id="sizes" type="text" name="sizes" defaultValue={p.sizes} placeholder="S–XXL" />
              <label htmlFor="colors">Colors</label>
              <input id="colors" type="text" name="colors" defaultValue={p.colors} placeholder="black, bone" />
              <label htmlFor="price">Price</label>
              <input id="price" type="text" name="price" defaultValue={p.price} placeholder="30.00 $" />
              <label htmlFor="extraNotes">Other details</label>
              <input id="extraNotes" type="text" name="extraNotes" defaultValue={p.extraNotes} placeholder="care, print placement…" />
            </div>
          </div>
        </div>

        {/* ---------- generated ---------- */}
        <div className="card">
          <h2 style={{ marginTop: 0 }}>Generated copy</h2>

          <label htmlFor="slug">Slug (URL handle)</label>
          <input id="slug" type="text" name="slug" defaultValue={p.slug} />

          <label htmlFor="metaTitle">Meta title</label>
          <input
            id="metaTitle"
            type="text"
            name="metaTitle"
            value={metaTitle}
            onChange={(e) => setMetaTitle(e.target.value)}
          />
          <Counter value={metaTitle} max={META_TITLE_MAX} />

          <label htmlFor="seoDescription">Meta description</label>
          <textarea
            id="seoDescription"
            name="seoDescription"
            value={metaDesc}
            onChange={(e) => setMetaDesc(e.target.value)}
            style={{ minHeight: 70 }}
          />
          <Counter value={metaDesc} max={META_DESC_MAX} />

          <label htmlFor="shortDescription">Short description (next to Add to Cart)</label>
          <textarea
            id="shortDescription"
            name="shortDescription"
            defaultValue={p.shortDescription}
            style={{ minHeight: 70 }}
          />

          <label htmlFor="description">Description — two sentences</label>
          <textarea id="description" name="description" defaultValue={p.description} style={{ minHeight: 110 }} />
          <p className="hint">
            Exactly two sentences — this brand does not want long copy. Anything longer is
            rejected and regenerated.
          </p>

          <label htmlFor="tagsRaw">Tags (3–5)</label>
          <textarea id="tagsRaw" name="tagsRaw" defaultValue={p.tags.join('\n')} style={{ minHeight: 70 }} />

          <div className="grid" style={{ gridTemplateColumns: '1fr 1fr' }}>
            <div>
              <label htmlFor="altRaw">Image alt texts (5: front, back, detail, worn, flat lay)</label>
              <textarea id="altRaw" name="altRaw" defaultValue={p.imagesAlt.join('\n')} style={{ minHeight: 110 }} />
            </div>
            <div>
              <label htmlFor="filesRaw">Image filenames (5)</label>
              <textarea id="filesRaw" name="filesRaw" defaultValue={p.imageFilenames.join('\n')} style={{ minHeight: 110 }} />
            </div>
          </div>

          {p.keywordsUsed && (
            <>
              <label>Keywords actually used</label>
              <div>
                {p.keywordsUsed.primary && <span className="kw">{p.keywordsUsed.primary}</span>}
                {(p.keywordsUsed.secondary || []).map((k) => (
                  <span className="kw sec" key={k}>
                    {k}
                  </span>
                ))}
              </div>
            </>
          )}

          <label htmlFor="status">Status</label>
          <select id="status" name="status" defaultValue={p.status}>
            <option value="DRAFT">Draft</option>
            <option value="GENERATED">Generated</option>
            <option value="APPROVED">Approved</option>
          </select>

          <div className="row">
            <button type="button" onClick={generate} disabled={gen || !p.imageMime}>
              {gen ? 'Generating…' : 'Generate with Gemini Flash'}
            </button>
            <button className="ghost" disabled={busy}>
              {busy ? 'Saving…' : 'Save'}
            </button>
            <button type="button" className="ghost" onClick={exportWc}>
              Copy WooCommerce JSON
            </button>
            <button type="button" className="ghost" onClick={translate} disabled={translating}>
              {translating ? 'Translating…' : ru ? 'Hide Russian' : 'View in Russian'}
            </button>
            <button type="button" className="danger" onClick={remove}>
              Delete
            </button>
          </div>
        </div>
      </form>

      {ru && (
        <div className="card" style={{ background: '#faf6ff', borderColor: '#e3d3f7' }}>
          <h2 style={{ marginTop: 0 }}>Russian preview</h2>
          <div className="alert warn" style={{ marginTop: 0 }}>
            Reading aid only. This translation is <strong>not saved</strong> and is
            <strong> never exported</strong> — the storefront is English, so the English copy
            above stays the source of truth.
          </div>

          <label>Description</label>
          <p>{ru.description}</p>

          <label>Short description</label>
          <p>{ru.shortDescription}</p>

          <label>Meta title</label>
          <p>{ru.metaTitle}</p>

          <label>Meta description</label>
          <p>{ru.metaDescription}</p>

          <label>Tags</label>
          <div>
            {ru.tags.map((t) => (
              <span className="kw sec" key={t}>
                {t}
              </span>
            ))}
          </div>

          <label>Image alt texts</label>
          <ol className="hint" style={{ paddingLeft: 18 }}>
            {ru.imagesAlt.map((a, i) => (
              <li key={i}>{a}</li>
            ))}
          </ol>
        </div>
      )}
    </>
  );
}

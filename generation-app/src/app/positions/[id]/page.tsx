'use client';

import { useEffect, useState } from 'react';

interface Position {
  id: string;
  title: string;
  seoTitle: string;
  primaryKeyword: string;
  secondaryKeywords: string[];
  description: string;
  seoDescription: string;
  keywordsUsed: { primary?: string; secondary?: string[] } | null;
  warnings: string[];
  status: 'DRAFT' | 'GENERATED' | 'APPROVED';
  imageMime: string | null;
}

interface Violation {
  rule: string;
  detail: string;
}

const META_MAX = 155;

export default function PositionCard({ params }: { params: { id: string } }) {
  const [p, setP] = useState<Position | null>(null);
  const [busy, setBusy] = useState(false);
  const [gen, setGen] = useState(false);
  const [error, setError] = useState('');
  const [violations, setViolations] = useState<Violation[]>([]);
  const [ok, setOk] = useState('');

  async function load() {
    const res = await fetch(`/api/positions/${params.id}`);
    if (res.ok) setP(await res.json());
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
    const raw = String(form.get('secondaryRaw') || '');
    form.delete('secondaryRaw');
    raw
      .split(/[\n,]/)
      .map((s) => s.trim())
      .filter(Boolean)
      .forEach((k) => form.append('secondaryKeywords', k));

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
    if (data.ok) setOk(`Generated cleanly in ${data.attempts} attempt(s) — all keyword rules passed.`);
    load();
  }

  async function remove() {
    if (!confirm('Delete this position?')) return;
    await fetch(`/api/positions/${params.id}`, { method: 'DELETE' });
    window.location.href = '/';
  }

  if (!p) return <div className="empty">Loading…</div>;

  const metaLen = p.seoDescription.length;

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
          <strong>Rejected — the copy still breaks hard keyword rules after retries.</strong>
          <ul>
            {violations.map((v, i) => (
              <li key={i}>
                <code>{v.rule}</code> — {v.detail}
              </li>
            ))}
          </ul>
          It was saved as a DRAFT, not published. Adjust the keywords and generate again.
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
        <div className="card">
          <div className="grid">
            <div>
              <label>Print image</label>
              {p.imageMime ? (
                <img className="preview" src={`/api/positions/${p.id}/image`} alt="" />
              ) : (
                <div className="alert warn">No image yet — upload one, the copy is written from it.</div>
              )}
              <label htmlFor="image">Replace image</label>
              <input id="image" type="file" name="image" accept="image/*" />
            </div>

            <div>
              <label htmlFor="title">Title — main print name</label>
              <input id="title" type="text" name="title" defaultValue={p.title} />

              <label htmlFor="seoTitle">SEO title</label>
              <input id="seoTitle" type="text" name="seoTitle" defaultValue={p.seoTitle} />

              <label htmlFor="primaryKeyword">Primary keyword</label>
              <input
                id="primaryKeyword"
                type="text"
                name="primaryKeyword"
                defaultValue={p.primaryKeyword}
              />
              <p className="hint">Used exactly once in the description and once in the meta.</p>

              <label htmlFor="secondaryRaw">Secondary keywords</label>
              <textarea
                id="secondaryRaw"
                name="secondaryRaw"
                defaultValue={p.secondaryKeywords.join('\n')}
                style={{ minHeight: 90 }}
              />
              <p className="hint">Each used at most once — any that do not fit are dropped, not forced.</p>
            </div>
          </div>
        </div>

        <div className="card">
          <h2 style={{ marginTop: 0 }}>Generated copy</h2>

          <label htmlFor="description">Description</label>
          <textarea id="description" name="description" defaultValue={p.description} />

          <label htmlFor="seoDescription">SEO / meta description</label>
          <textarea
            id="seoDescription"
            name="seoDescription"
            defaultValue={p.seoDescription}
            style={{ minHeight: 70 }}
          />
          <span className={`counter ${metaLen > META_MAX ? 'over' : ''}`}>
            {metaLen} / {META_MAX} characters
          </span>

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
            <button type="button" className="danger" onClick={remove}>
              Delete
            </button>
          </div>
        </div>
      </form>
    </>
  );
}

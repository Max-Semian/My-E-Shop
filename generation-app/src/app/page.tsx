'use client';

import { useEffect, useState } from 'react';

interface Row {
  id: string;
  title: string;
  seoTitle: string;
  /** Null until the artwork has been read — a product primary is derived, not typed in. */
  primaryKeyword: string | null;
  secondaryKeywords: string[];
  status: 'DRAFT' | 'GENERATED' | 'APPROVED';
  warnings: string[];
  imageMime: string | null;
  updatedAt: string;
}

export default function PositionsPage() {
  const [rows, setRows] = useState<Row[]>([]);
  const [loading, setLoading] = useState(true);
  const [creating, setCreating] = useState(false);
  const [error, setError] = useState('');

  async function load() {
    const res = await fetch('/api/positions');
    if (res.ok) setRows(await res.json());
    setLoading(false);
  }
  useEffect(() => {
    load();
  }, []);

  async function create(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setCreating(true);
    setError('');
    const form = new FormData(e.currentTarget);
    // Secondary keywords come in as one comma/newline separated field.
    const raw = String(form.get('secondaryRaw') || '');
    form.delete('secondaryRaw');
    raw
      .split(/[\n,]/)
      .map((s) => s.trim())
      .filter(Boolean)
      .forEach((k) => form.append('secondaryKeywords', k));

    const res = await fetch('/api/positions', { method: 'POST', body: form });
    setCreating(false);
    if (res.ok) {
      const { id } = await res.json();
      window.location.href = `/positions/${id}`;
    } else {
      const { error } = await res.json().catch(() => ({ error: 'Failed' }));
      setError(error);
    }
  }

  return (
    <>
      <h1>Positions</h1>

      <div className="card">
        <h2 style={{ marginTop: 0 }}>New position</h2>
        <form onSubmit={create}>
          <div className="grid">
            <div>
              <label htmlFor="image">Print image</label>
              <input id="image" type="file" name="image" accept="image/*" />
              <p className="hint">The copy is written from this image — upload it before generating.</p>
            </div>
            <div>
              <label htmlFor="title">Title — main print name</label>
              <input id="title" type="text" name="title" required />

              <label htmlFor="seoTitle">SEO title</label>
              <input id="seoTitle" type="text" name="seoTitle" />

              <label htmlFor="primaryKeyword">Primary keyword (optional)</label>
              <input id="primaryKeyword" type="text" name="primaryKeyword" />
              <p className="hint">
                Leave it empty and derive it from the print instead. A product&apos;s primary is
                a long-tail query anchored on the motif that is actually drawn on the shirt —
                it cannot honestly be typed in before the artwork has been read. Head terms
                like <em>gothic t-shirt</em> are owned by the category pages and are refused
                here: they carry browsing intent, so they belong on a page that shows a range.
              </p>

              <label htmlFor="secondaryRaw">Secondary keywords</label>
              <textarea
                id="secondaryRaw"
                name="secondaryRaw"
                placeholder="One per line, or comma separated. Pick them from the Keyword list."
                style={{ minHeight: 80 }}
              />
            </div>
          </div>

          {error && <div className="alert err">{error}</div>}

          <div className="row">
            <button disabled={creating}>{creating ? 'Creating…' : 'Create position'}</button>
          </div>
        </form>
      </div>

      {loading ? (
        <div className="empty">Loading…</div>
      ) : rows.length === 0 ? (
        <div className="empty">No positions yet. Create the first one above.</div>
      ) : (
        <table className="table">
          <thead>
            <tr>
              <th />
              <th>Title</th>
              <th>Primary keyword</th>
              <th>Secondary</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((r) => (
              <tr key={r.id} onClick={() => (window.location.href = `/positions/${r.id}`)}>
                <td>
                  {r.imageMime ? (
                    <img className="thumb" src={`/api/positions/${r.id}/image`} alt="" />
                  ) : (
                    <div className="thumb-empty" />
                  )}
                </td>
                <td>
                  <strong>{r.title}</strong>
                  {r.warnings.length > 0 && (
                    <div className="hint" style={{ color: 'var(--warn)' }}>
                      {r.warnings.length} warning{r.warnings.length > 1 ? 's' : ''}
                    </div>
                  )}
                </td>
                <td>
                  {r.primaryKeyword ? (
                    <span className="kw">{r.primaryKeyword}</span>
                  ) : (
                    <span className="hint" style={{ color: 'var(--warn)' }}>
                      not derived yet
                    </span>
                  )}
                </td>
                <td>
                  {r.secondaryKeywords.slice(0, 3).map((k) => (
                    <span className="kw sec" key={k}>
                      {k}
                    </span>
                  ))}
                  {r.secondaryKeywords.length > 3 && (
                    <span className="hint"> +{r.secondaryKeywords.length - 3}</span>
                  )}
                </td>
                <td>
                  <span className={`badge ${r.status}`}>{r.status}</span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
    </>
  );
}

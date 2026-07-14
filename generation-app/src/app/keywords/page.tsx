'use client';

import { useEffect, useState } from 'react';

interface Kw {
  id: string;
  text: string;
  type: 'PRIMARY' | 'SECONDARY';
  topic: string | null;
  /** Title of the position that already owns this keyword as its PRIMARY. */
  claimedBy: string | null;
}

export default function KeywordsPage() {
  const [rows, setRows] = useState<Kw[]>([]);
  const [busy, setBusy] = useState(false);

  async function load() {
    const res = await fetch('/api/keywords');
    if (res.ok) setRows(await res.json());
  }
  useEffect(() => {
    load();
  }, []);

  async function add(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setBusy(true);
    const form = new FormData(e.currentTarget);
    await fetch('/api/keywords', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        text: form.get('text'),
        type: form.get('type'),
        topic: form.get('topic'),
      }),
    });
    setBusy(false);
    (e.target as HTMLFormElement).reset();
    load();
  }

  async function remove(id: string) {
    await fetch('/api/keywords', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id }),
    });
    load();
  }

  return (
    <>
      <h1>Keyword list</h1>

      <div className="card">
        <form onSubmit={add}>
          <label htmlFor="text">Add keywords</label>
          <textarea id="text" name="text" placeholder="One per line, or comma separated" />
          <div className="grid" style={{ gridTemplateColumns: '1fr 1fr' }}>
            <div>
              <label htmlFor="type">Type</label>
              <select id="type" name="type" defaultValue="SECONDARY">
                <option value="SECONDARY">Secondary</option>
                <option value="PRIMARY">Primary</option>
              </select>
            </div>
            <div>
              <label htmlFor="topic">Topic (optional)</label>
              <input id="topic" type="text" name="topic" placeholder="e.g. Witch Core" />
            </div>
          </div>
          <div className="row">
            <button disabled={busy}>{busy ? 'Adding…' : 'Add'}</button>
          </div>
        </form>
      </div>

      {rows.length === 0 ? (
        <div className="empty">No keywords yet.</div>
      ) : (
        <table className="table">
          <thead>
            <tr>
              <th>Keyword</th>
              <th>Type</th>
              <th>Topic</th>
              <th>Availability</th>
              <th />
            </tr>
          </thead>
          <tbody>
            {rows.map((k) => (
              <tr key={k.id} style={{ cursor: 'default' }}>
                <td>
                  <span className={`kw ${k.type === 'SECONDARY' ? 'sec' : ''}`}>{k.text}</span>
                </td>
                <td className="hint">{k.type}</td>
                <td className="hint">{k.topic || '—'}</td>
                <td>
                  {k.claimedBy ? (
                    <span className="hint" style={{ color: 'var(--bad)' }}>
                      Taken as primary by “{k.claimedBy}” — do not target again
                    </span>
                  ) : (
                    <span className="hint" style={{ color: 'var(--ok)' }}>
                      Free
                    </span>
                  )}
                </td>
                <td>
                  <button className="ghost" type="button" onClick={() => remove(k.id)}>
                    Remove
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
    </>
  );
}

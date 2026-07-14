'use client';

import { useEffect, useState } from 'react';

type Tier = 'COMMERCIAL' | 'SEMANTIC' | 'MOTIF';

interface Kw {
  id: string;
  text: string;
  type: 'PRIMARY' | 'SECONDARY';
  tier: Tier;
  /** Set when a listing page owns this head term — products may then never target it. */
  reservedFor: string | null;
  topic: string | null;
  /** Title of the position that already owns this keyword as its PRIMARY. */
  claimedBy: string | null;
}

/** The level is not a label — it decides where the keyword is allowed to be placed. */
const TIER_JOB: Record<Tier, string> = {
  COMMERCIAL: 'Real buying intent — goes in the description, once.',
  SEMANTIC: 'Topical context for search engines and AI — description or tags.',
  MOTIF: 'The object visible on the print — must land in an alt text or a tag.',
};

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
        tier: form.get('tier'),
        reservedFor: form.get('reservedFor'),
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

      <div className="alert warn">
        A secondary keyword&apos;s <strong>level</strong> decides where it is allowed to be
        placed — it is enforced in code, not suggested. <strong>Commercial</strong> keywords
        live in the description; <strong>semantic</strong> ones give the product its topical
        world; <strong>motif</strong> ones name what is actually drawn on the print and must
        land where the image is described. A set with all three defines the product as an
        entity, which is what semantic search, Google&apos;s product understanding and LLM
        retrieval actually read.
      </div>

      <div className="card">
        <form onSubmit={add}>
          <label htmlFor="text">Add keywords</label>
          <textarea id="text" name="text" placeholder="One per line, or comma separated" />
          <div className="grid" style={{ gridTemplateColumns: '1fr 1fr 1fr' }}>
            <div>
              <label htmlFor="type">Type</label>
              <select id="type" name="type" defaultValue="SECONDARY">
                <option value="SECONDARY">Secondary</option>
                <option value="PRIMARY">Primary</option>
              </select>
            </div>
            <div>
              <label htmlFor="tier">Level (secondary only)</label>
              <select id="tier" name="tier" defaultValue="COMMERCIAL">
                <option value="COMMERCIAL">Commercial — buying intent</option>
                <option value="SEMANTIC">Semantic — topical support</option>
                <option value="MOTIF">Motif — object on the print</option>
              </select>
            </div>
            <div>
              <label htmlFor="topic">Topic (optional)</label>
              <input id="topic" type="text" name="topic" placeholder="e.g. Witch Core" />
            </div>
          </div>

          <label htmlFor="reservedFor">Owned by a listing page (optional)</label>
          <input
            id="reservedFor"
            type="text"
            name="reservedFor"
            placeholder="e.g. Category: Gothic — leave empty for a normal keyword"
          />
          <p className="hint">
            Name a page here and this becomes a <strong>head term</strong>: every product page
            is then forbidden from targeting it, in the database and in the generated copy.
            Head terms like <em>gothic t-shirt</em> carry browsing intent — the searcher wants
            to pick from a range — so they belong on a page that shows a range. A product
            parked on one both disappoints the searcher and fights its own category for the
            query.
          </p>
          <div className="row">
            <button disabled={busy}>{busy ? 'Adding…' : 'Add'}</button>
          </div>
          <p className="hint">
            Adding an existing keyword updates its level, so a mistiered keyword can be
            corrected by re-adding it.
          </p>
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
              <th>Level</th>
              <th>Owner</th>
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
                <td>
                  {k.type === 'SECONDARY' ? (
                    <span className={`tier ${k.tier}`} title={TIER_JOB[k.tier]}>
                      {k.tier}
                    </span>
                  ) : (
                    <span className="hint">—</span>
                  )}
                </td>
                <td>
                  {k.reservedFor ? (
                    <span className="kw">{k.reservedFor}</span>
                  ) : (
                    <span className="hint">{k.topic || '—'}</span>
                  )}
                </td>
                <td>
                  {k.reservedFor ? (
                    <span className="hint" style={{ color: 'var(--bad)' }}>
                      Head term — off-limits to every product page
                    </span>
                  ) : k.claimedBy ? (
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

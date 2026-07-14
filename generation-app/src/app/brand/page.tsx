'use client';

import { useEffect, useState } from 'react';

interface Brand {
  brandName: string;
  concept: string;
  archetype: string;
  archetypeNotes: string;
  audience: string;
  toneOfVoice: string;
  valueProps: string;
  vocabulary: string;
  bannedWords: string[];
}

export default function BrandPage() {
  const [b, setB] = useState<Brand | null>(null);
  const [busy, setBusy] = useState(false);
  const [ok, setOk] = useState('');

  async function load() {
    const res = await fetch('/api/brand');
    if (res.ok) setB(await res.json());
  }
  useEffect(() => {
    load();
  }, []);

  async function save(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setBusy(true);
    setOk('');
    const res = await fetch('/api/brand', { method: 'PUT', body: new FormData(e.currentTarget) });
    setBusy(false);
    if (res.ok) {
      setOk('Saved. Every future generation now uses this.');
      load();
    }
  }

  if (!b) return <div className="empty">Loading…</div>;

  return (
    <>
      <h1>Brand foundation</h1>
      <div className="alert warn">
        This is the ground everything stands on. The <strong>concept</strong> decides what the
        copy is about; the <strong>archetype</strong> decides how it sounds. It is passed to the
        model as the system instruction, before the print and before the keywords. Change it and
        every future generation changes with it — already-generated copy is left untouched.
      </div>

      {ok && <div className="alert ok">{ok}</div>}

      <form onSubmit={save}>
        <div className="card">
          <label htmlFor="brandName">Brand name</label>
          <input id="brandName" type="text" name="brandName" defaultValue={b.brandName} />

          <label htmlFor="concept">Concept — the core idea, the manifesto</label>
          <textarea id="concept" name="concept" defaultValue={b.concept} style={{ minHeight: 110 }} />

          <label htmlFor="archetype">Archetype</label>
          <input id="archetype" type="text" name="archetype" defaultValue={b.archetype} />

          <label htmlFor="archetypeNotes">
            How the archetype shows up in copy — and what it must never sound like
          </label>
          <textarea
            id="archetypeNotes"
            name="archetypeNotes"
            defaultValue={b.archetypeNotes}
            style={{ minHeight: 180 }}
          />
        </div>

        <div className="card">
          <label htmlFor="audience">Audience</label>
          <textarea id="audience" name="audience" defaultValue={b.audience} style={{ minHeight: 90 }} />

          <label htmlFor="toneOfVoice">Tone of voice</label>
          <textarea id="toneOfVoice" name="toneOfVoice" defaultValue={b.toneOfVoice} style={{ minHeight: 90 }} />

          <label htmlFor="valueProps">What we actually promise</label>
          <textarea id="valueProps" name="valueProps" defaultValue={b.valueProps} style={{ minHeight: 90 }} />

          <label htmlFor="vocabulary">Vocabulary — preferred terms</label>
          <textarea id="vocabulary" name="vocabulary" defaultValue={b.vocabulary} style={{ minHeight: 90 }} />

          <label htmlFor="bannedWordsRaw">Banned words — never allowed in copy</label>
          <textarea
            id="bannedWordsRaw"
            name="bannedWordsRaw"
            defaultValue={b.bannedWords.join('\n')}
            style={{ minHeight: 100 }}
          />
          <p className="hint">
            One per line. These are enforced in code — copy containing any of them is rejected
            and regenerated, not just discouraged in the prompt.
          </p>

          <div className="row">
            <button disabled={busy}>{busy ? 'Saving…' : 'Save brand foundation'}</button>
          </div>
        </div>
      </form>
    </>
  );
}

'use client';

import { useState } from 'react';

export default function LoginPage() {
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setBusy(true);
    setError('');
    const res = await fetch('/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ password }),
    });
    setBusy(false);
    if (res.ok) window.location.href = '/';
    else setError('Wrong password');
  }

  return (
    <div className="card" style={{ maxWidth: 380, margin: '60px auto' }}>
      <h1 style={{ fontSize: 20 }}>Sign in</h1>
      <form onSubmit={submit}>
        <label htmlFor="pw">Password</label>
        <input
          id="pw"
          type="text"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          autoFocus
          style={{ WebkitTextSecurity: 'disc' } as React.CSSProperties}
        />
        {error && <div className="alert err">{error}</div>}
        <div className="row">
          <button disabled={busy || !password}>{busy ? 'Checking…' : 'Sign in'}</button>
        </div>
      </form>
    </div>
  );
}

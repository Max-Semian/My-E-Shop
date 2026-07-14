import { createHmac, timingSafeEqual } from 'crypto';
import { cookies } from 'next/headers';

const COOKIE = 'cretho_gen_session';

function secret(): string {
  const s = process.env.SESSION_SECRET;
  if (!s) throw new Error('SESSION_SECRET is not set');
  return s;
}

function sign(value: string): string {
  return createHmac('sha256', secret()).update(value).digest('hex');
}

function safeEqual(a: string, b: string): boolean {
  const ba = Buffer.from(a);
  const bb = Buffer.from(b);
  if (ba.length !== bb.length) return false;
  return timingSafeEqual(ba, bb);
}

/** Constant-time check of the shared password. */
export function passwordMatches(input: string): boolean {
  const expected = process.env.APP_PASSWORD;
  if (!expected) throw new Error('APP_PASSWORD is not set');
  return safeEqual(input, expected);
}

export function issueSession(): void {
  const issuedAt = String(Date.now());
  const token = `${issuedAt}.${sign(issuedAt)}`;
  cookies().set(COOKIE, token, {
    httpOnly: true,
    sameSite: 'lax',
    secure: process.env.NODE_ENV === 'production',
    path: '/',
    maxAge: 60 * 60 * 24 * 30,
  });
}

export function clearSession(): void {
  cookies().delete(COOKIE);
}

export function isAuthenticated(): boolean {
  const raw = cookies().get(COOKIE)?.value;
  if (!raw) return false;
  const [issuedAt, sig] = raw.split('.');
  if (!issuedAt || !sig) return false;
  return safeEqual(sig, sign(issuedAt));
}

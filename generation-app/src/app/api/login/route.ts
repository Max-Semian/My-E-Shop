import { NextResponse } from 'next/server';
import { issueSession, passwordMatches } from '@/lib/auth';

export async function POST(req: Request) {
  const { password } = await req.json().catch(() => ({ password: '' }));
  if (!password || !passwordMatches(String(password))) {
    return NextResponse.json({ error: 'Wrong password' }, { status: 401 });
  }
  issueSession();
  return NextResponse.json({ ok: true });
}

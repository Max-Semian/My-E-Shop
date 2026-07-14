import { NextResponse, type NextRequest } from 'next/server';

/**
 * Gate the whole app behind the session cookie. The cookie is HMAC-signed, but the
 * signature is verified in the route handlers (Node crypto is unavailable in the edge
 * middleware runtime) — here we only bounce requests that carry no cookie at all.
 */
const PUBLIC = ['/login', '/api/login'];

export function middleware(req: NextRequest) {
  const { pathname } = req.nextUrl;
  if (PUBLIC.some((p) => pathname.startsWith(p))) return NextResponse.next();

  const hasSession = Boolean(req.cookies.get('cretho_gen_session')?.value);
  if (hasSession) return NextResponse.next();

  if (pathname.startsWith('/api/')) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }
  const url = req.nextUrl.clone();
  url.pathname = '/login';
  return NextResponse.redirect(url);
}

export const config = {
  matcher: ['/((?!_next/static|_next/image|favicon.ico).*)'],
};

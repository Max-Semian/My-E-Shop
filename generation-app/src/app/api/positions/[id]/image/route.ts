import { NextResponse } from 'next/server';
import { prisma } from '@/lib/db';
import { isAuthenticated } from '@/lib/auth';

/** Serves the stored print image so the table/card can show a thumbnail. */
export async function GET(_req: Request, { params }: { params: { id: string } }) {
  if (!isAuthenticated()) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  const p = await prisma.position.findUnique({
    where: { id: params.id },
    select: { imageData: true, imageMime: true },
  });
  if (!p?.imageData || !p.imageMime) {
    return NextResponse.json({ error: 'No image' }, { status: 404 });
  }

  return new NextResponse(Buffer.from(p.imageData), {
    headers: {
      'Content-Type': p.imageMime,
      'Cache-Control': 'private, max-age=300',
    },
  });
}

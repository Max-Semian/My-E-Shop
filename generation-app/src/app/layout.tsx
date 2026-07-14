import type { Metadata } from 'next';
import './globals.css';

export const metadata: Metadata = {
  title: 'Cretho — Print Copy Generator',
  description: 'Controlled description & SEO copy generation for T-shirt prints.',
  robots: { index: false, follow: false },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <body>
        <header className="topbar">
          <a className="brand" href="/">
            CRETHO <span>Print Copy Generator</span>
          </a>
          <nav>
            <a href="/">Positions</a>
            <a href="/keywords">Keyword list</a>
          </nav>
        </header>
        <main className="wrap">{children}</main>
      </body>
    </html>
  );
}

/** @type {import('next').NextConfig} */
const nextConfig = {
  // No `output: 'standalone'` — that mode is for slim multi-stage Docker images and is
  // incompatible with `next start` (Next warns and the served app can misbehave).
  // Nixpacks keeps node_modules, so the normal server is the right one here.
  experimental: {
    // Print images are posted as multipart form data.
    serverActions: { bodySizeLimit: '10mb' },
  },
};
module.exports = nextConfig;

/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'standalone',
  experimental: {
    // Print images are posted as multipart form data.
    serverActions: { bodySizeLimit: '10mb' },
  },
};
module.exports = nextConfig;

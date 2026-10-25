# Pinned to a Node release that includes the October 2026 security fixes;
# Node 18 is EOL and receives no patches.
FROM node:22.23.4-bookworm-slim

# Prisma's query engine needs OpenSSL.
RUN apt-get update && apt-get install -y --no-install-recommends openssl ca-certificates \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY package.json package-lock.json ./
COPY prisma ./prisma
RUN npm ci

COPY . .
RUN npm run build

ENV NODE_ENV=production
CMD ["npm", "run", "start"]

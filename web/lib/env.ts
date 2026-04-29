function required(name: string): string {
  const value = process.env[name];
  if (!value) {
    throw new Error(`Missing required env var: ${name}`);
  }
  return value;
}

export const env = {
  WP_API_URL: required("WP_API_URL").replace(/\/$/, ""),
  REVALIDATE_SECRET: required("REVALIDATE_SECRET"),
  SITE_URL: (process.env.SITE_URL ?? "http://localhost:3000").replace(/\/$/, ""),
};

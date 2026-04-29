import type { MetadataRoute } from "next";
import { env } from "@/lib/env";
import {
  getDivisions,
  getAdvisors,
  getInsights,
} from "@/lib/api";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const base = env.SITE_URL;
  const now = new Date();

  const [divisions, advisors, insights] = await Promise.all([
    getDivisions(),
    getAdvisors(),
    getInsights({ per_page: 50 }),
  ]);

  const staticRoutes: MetadataRoute.Sitemap = [
    { url: `${base}/`, lastModified: now, changeFrequency: "weekly", priority: 1 },
    { url: `${base}/divisions`, lastModified: now, changeFrequency: "weekly", priority: 0.8 },
    { url: `${base}/advisors`, lastModified: now, changeFrequency: "monthly", priority: 0.7 },
    { url: `${base}/insights`, lastModified: now, changeFrequency: "daily", priority: 0.8 },
    { url: `${base}/search`, lastModified: now, changeFrequency: "yearly", priority: 0.3 },
  ];

  const divisionRoutes: MetadataRoute.Sitemap = divisions.items.map((d) => ({
    url: `${base}/divisions/${d.slug}`,
    lastModified: now,
    changeFrequency: "weekly",
    priority: 0.9,
  }));

  const advisorRoutes: MetadataRoute.Sitemap = advisors.items.map((a) => ({
    url: `${base}/advisors/${a.slug}`,
    lastModified: now,
    changeFrequency: "monthly",
    priority: 0.6,
  }));

  const articleRoutes: MetadataRoute.Sitemap = insights.items.map((a) => ({
    url: `${base}/insights/${a.slug}`,
    lastModified: new Date(a.publish_date),
    changeFrequency: "yearly",
    priority: 0.6,
  }));

  return [
    ...staticRoutes,
    ...divisionRoutes,
    ...advisorRoutes,
    ...articleRoutes,
  ];
}

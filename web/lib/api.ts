import { env } from "./env";
import type {
  DivisionsIndexResponse,
  DivisionHubResponse,
  ServiceDetailResponse,
  InsightsIndexResponse,
  InsightDetailResponse,
  CampaignDetailResponse,
  AdvisorsIndexResponse,
  AdvisorDetailResponse,
  SearchResponse,
} from "./types";

class ApiError extends Error {
  constructor(public status: number, public path: string, message: string) {
    super(`[Northium API] ${status} ${path}: ${message}`);
    this.name = "ApiError";
  }
}

type FetchOpts = {
  /** Cache lifetime in seconds. Default 300 (5 min). */
  revalidate?: number;
  /** Cache tags for on-demand revalidation. */
  tags?: string[];
  /** Treat 404 as a return value, not a throw. */
  allow404?: boolean;
};

async function get<T>(path: string, opts: FetchOpts = {}): Promise<T | null> {
  const url = `${env.WP_API_URL}/wp-json/northium/v1${path}`;
  const res = await fetch(url, {
    cache: "force-cache",
    next: {
      revalidate: opts.revalidate ?? 300,
      tags: opts.tags,
    },
  });

  if (res.status === 404 && opts.allow404) {
    return null;
  }

  if (!res.ok) {
    throw new ApiError(res.status, path, await res.text().catch(() => ""));
  }

  return (await res.json()) as T;
}

// ---------- Divisions ----------

export async function getDivisions(): Promise<DivisionsIndexResponse> {
  const data = await get<DivisionsIndexResponse>("/divisions", {
    tags: ["divisions"],
  });
  return data!;
}

export async function getDivision(
  slug: string
): Promise<DivisionHubResponse | null> {
  return get<DivisionHubResponse>(`/division/${slug}`, {
    tags: ["divisions", `division:${slug}`],
    allow404: true,
  });
}

// ---------- Services ----------

export async function getService(
  slug: string
): Promise<ServiceDetailResponse | null> {
  return get<ServiceDetailResponse>(`/service/${slug}`, {
    tags: ["services", `service:${slug}`],
    allow404: true,
  });
}

// ---------- Insights (articles) ----------

export type InsightsParams = {
  division?: string;
  category?: string;
  page?: number;
  per_page?: number;
};

export async function getInsights(
  params: InsightsParams = {}
): Promise<InsightsIndexResponse> {
  const search = new URLSearchParams();
  if (params.division) search.set("division", params.division);
  if (params.category) search.set("category", params.category);
  if (params.page) search.set("page", String(params.page));
  if (params.per_page) search.set("per_page", String(params.per_page));

  const qs = search.toString();
  const path = qs ? `/insights?${qs}` : "/insights";

  const data = await get<InsightsIndexResponse>(path, {
    tags: ["articles"],
  });
  return data!;
}

export async function getInsight(
  slug: string
): Promise<InsightDetailResponse | null> {
  return get<InsightDetailResponse>(`/insights/${slug}`, {
    tags: ["articles", `article:${slug}`],
    allow404: true,
  });
}

// ---------- Campaigns ----------

export async function getCampaign(
  slug: string
): Promise<CampaignDetailResponse | null> {
  return get<CampaignDetailResponse>(`/campaign/${slug}`, {
    tags: ["campaigns", `campaign:${slug}`],
    allow404: true,
  });
}

// ---------- Advisors ----------

export async function getAdvisors(): Promise<AdvisorsIndexResponse> {
  const data = await get<AdvisorsIndexResponse>("/advisors", {
    tags: ["advisors"],
  });
  return data!;
}

export async function getAdvisor(
  slug: string
): Promise<AdvisorDetailResponse | null> {
  return get<AdvisorDetailResponse>(`/advisor/${slug}`, {
    tags: ["advisors", `advisor:${slug}`],
    allow404: true,
  });
}

// ---------- Search ----------

export async function search(query: string): Promise<SearchResponse> {
  if (!query.trim()) {
    return {
      query: "",
      results: { divisions: [], services: [], advisors: [], articles: [] },
    };
  }
  const data = await get<SearchResponse>(
    `/search?q=${encodeURIComponent(query)}`,
    { revalidate: 60 }
  );
  return data!;
}

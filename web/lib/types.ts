/**
 * TypeScript mirrors of the northium/v1 REST responses.
 * Source of truth: wp-content/plugins/northium-cms/includes/rest/helpers.php
 *
 * Keep this file in sync with the formatters in helpers.php — every field added
 * there should appear here with the same key.
 */

export type Image = {
  id: number;
  url: string;
  alt: string;
};

export type Division = {
  id: number;
  slug: string;
  title: string;
  short_description: string;
  hero_image: Image | null;
  accent_color: string | null;
  order: number;
  full_description?: string;
};

export type Service = {
  id: number;
  slug: string;
  title: string;
  summary: string;
  icon: Image | null;
  featured_image: Image | null;
  cta_label: string;
  cta_url: string;
  division_id: number;
  body?: string;
};

export type Advisor = {
  id: number;
  slug: string;
  name: string;
  role: string;
  profile_image: Image | null;
  division_ids: number[];
  email: string;
  linkedin_url: string;
  bio?: string;
};

export type Article = {
  id: number;
  slug: string;
  title: string;
  excerpt: string;
  featured_image: Image | null;
  publish_date: string;
  division_id: number;
  author_advisor_id: number;
  content?: string;
};

export type CampaignSection = {
  heading?: string;
  body?: string;
  image_id?: number;
};

export type CampaignTestimonial = {
  quote?: string;
  attribution?: string;
  role?: string;
};

export type Campaign = {
  id: number;
  slug: string;
  title: string;
  hero_image: Image | null;
  hero_headline: string;
  target_division_id: number;
  hero_subtext?: string;
  cta_text?: string;
  cta_url?: string;
  sections?: CampaignSection[];
  testimonials?: CampaignTestimonial[];
};

// ---------- Composed endpoint responses ----------

export type DivisionsIndexResponse = {
  items: Division[];
};

export type DivisionHubResponse = {
  division: Division;
  services: Service[];
  advisors: Advisor[];
  articles: Article[];
  campaigns: Campaign[];
};

export type ServiceDetailResponse = {
  service: Service;
  division: Division | null;
  related_articles: Article[];
};

export type InsightsIndexResponse = {
  items: Article[];
  page: number;
  per_page: number;
  total: number;
  total_pages: number;
};

export type InsightDetailResponse = {
  article: Article;
  division: Division | null;
  author_advisor: Advisor | null;
  related: Article[];
};

export type CampaignDetailResponse = {
  campaign: Campaign;
  division: Division | null;
};

export type AdvisorsIndexResponse = {
  items: Advisor[];
};

export type AdvisorDetailResponse = {
  advisor: Advisor;
  divisions: Division[];
};

export type SearchResponse = {
  query: string;
  results: {
    divisions: Division[];
    services: Service[];
    advisors: Advisor[];
    articles: Article[];
  };
};

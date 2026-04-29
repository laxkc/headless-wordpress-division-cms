import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Link from "next/link";
import { getCampaign } from "@/lib/api";
import { Container } from "@/components/Container";

type Params = { slug: string };

export async function generateMetadata({
  params,
}: {
  params: Promise<Params>;
}): Promise<Metadata> {
  const { slug } = await params;
  const data = await getCampaign(slug);
  if (!data) return { title: "Not found" };
  return {
    title: data.campaign.title,
    description: data.campaign.hero_subtext ?? data.campaign.hero_headline,
  };
}

export default async function CampaignPage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { slug } = await params;
  const data = await getCampaign(slug);
  if (!data) notFound();

  const { campaign, division } = data;
  const accent = division?.accent_color ?? "var(--color-accent)";

  return (
    <>
      <section
        className="relative border-b border-border"
        style={{
          background: `linear-gradient(180deg, ${accent}10 0%, transparent 100%)`,
        }}
      >
        <Container className="py-24 sm:py-32">
          {division ? (
            <Link
              href={`/divisions/${division.slug}`}
              className="text-xs uppercase tracking-[0.2em]"
              style={{ color: accent }}
            >
              ← {division.title}
            </Link>
          ) : null}
          <h1 className="mt-8 max-w-3xl text-5xl font-semibold leading-tight tracking-tight sm:text-6xl">
            {campaign.hero_headline || campaign.title}
          </h1>
          {campaign.hero_subtext ? (
            <p className="mt-6 max-w-2xl text-xl text-muted">
              {campaign.hero_subtext}
            </p>
          ) : null}
          {campaign.cta_text && campaign.cta_url ? (
            <div className="mt-10">
              <Link
                href={campaign.cta_url}
                className="inline-flex items-center rounded-full px-6 py-3 text-sm font-medium text-white transition-opacity hover:opacity-90"
                style={{ background: accent }}
              >
                {campaign.cta_text}
              </Link>
            </div>
          ) : null}
        </Container>
      </section>

      {campaign.sections && campaign.sections.length > 0 ? (
        <div className="divide-y divide-border">
          {campaign.sections.map((section, i) => (
            <section key={i} className="py-20">
              <Container className="grid gap-10 sm:grid-cols-12">
                <div className="sm:col-span-4">
                  {section.heading ? (
                    <h2 className="text-2xl font-semibold tracking-tight sm:text-3xl">
                      {section.heading}
                    </h2>
                  ) : null}
                </div>
                <div className="sm:col-span-8">
                  {section.body ? (
                    <p className="text-lg leading-relaxed text-text/90">
                      {section.body}
                    </p>
                  ) : null}
                </div>
              </Container>
            </section>
          ))}
        </div>
      ) : null}

      {campaign.testimonials && campaign.testimonials.length > 0 ? (
        <section className="border-t border-border py-20">
          <Container>
            <p className="text-xs uppercase tracking-[0.2em] text-muted">
              What clients say
            </p>
            <ul className="mt-8 grid gap-6 sm:grid-cols-2">
              {campaign.testimonials.map((t, i) => (
                <li
                  key={i}
                  className="rounded-2xl border border-border bg-surface p-6"
                >
                  {t.quote ? (
                    <p className="text-lg leading-relaxed">“{t.quote}”</p>
                  ) : null}
                  {t.attribution || t.role ? (
                    <p className="mt-4 text-sm text-muted">
                      {t.attribution}
                      {t.attribution && t.role ? " · " : ""}
                      {t.role}
                    </p>
                  ) : null}
                </li>
              ))}
            </ul>
          </Container>
        </section>
      ) : null}

      {campaign.cta_text && campaign.cta_url ? (
        <section className="border-t border-border py-20">
          <Container className="flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p className="text-2xl font-semibold tracking-tight">
              Ready when you are.
            </p>
            <Link
              href={campaign.cta_url}
              className="inline-flex items-center rounded-full px-6 py-3 text-sm font-medium text-white transition-opacity hover:opacity-90"
              style={{ background: accent }}
            >
              {campaign.cta_text}
            </Link>
          </Container>
        </section>
      ) : null}
    </>
  );
}

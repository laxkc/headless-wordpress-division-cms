import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Link from "next/link";
import { getDivision, getDivisions } from "@/lib/api";
import { Container } from "@/components/Container";
import { ServiceCard } from "@/components/ServiceCard";
import { AdvisorCard } from "@/components/AdvisorCard";
import { ArticleCard } from "@/components/ArticleCard";
import { JsonLd } from "@/components/JsonLd";
import { env } from "@/lib/env";

type Params = { slug: string };

export async function generateStaticParams(): Promise<Params[]> {
  const { items } = await getDivisions();
  return items.map((d) => ({ slug: d.slug }));
}

export async function generateMetadata({
  params,
}: {
  params: Promise<Params>;
}): Promise<Metadata> {
  const { slug } = await params;
  const data = await getDivision(slug);
  if (!data) return { title: "Not found" };
  return {
    title: data.division.title,
    description: data.division.short_description,
  };
}

export default async function DivisionHubPage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { slug } = await params;
  const data = await getDivision(slug);
  if (!data) notFound();

  const { division, services, advisors, articles, campaigns } = data;
  const accent = division.accent_color ?? "var(--color-accent)";

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "FinancialService",
    name: division.title,
    description: division.short_description,
    url: `${env.SITE_URL}/divisions/${division.slug}`,
    parentOrganization: {
      "@type": "Organization",
      name: "Northium Financial",
      url: env.SITE_URL,
    },
    hasOfferCatalog: {
      "@type": "OfferCatalog",
      name: `${division.title} Services`,
      itemListElement: services.map((s) => ({
        "@type": "Offer",
        name: s.title,
        description: s.summary,
        url: `${env.SITE_URL}/services/${s.slug}`,
      })),
    },
  };

  return (
    <>
      <JsonLd data={jsonLd} />
      <section
        className="border-b border-border"
        style={{ ["--division-accent" as string]: accent }}
      >
        <Container className="py-20">
          <Link
            href="/divisions"
            className="text-xs uppercase tracking-[0.2em] text-muted hover:text-foreground"
          >
            ← All divisions
          </Link>
          <div
            aria-hidden
            className="mt-8 h-1 w-16 rounded-full"
            style={{ background: accent }}
          />
          <h1 className="mt-6 max-w-3xl text-4xl font-semibold tracking-tight sm:text-5xl">
            {division.title}
          </h1>
          <p className="mt-5 max-w-2xl text-lg text-muted">
            {division.short_description}
          </p>
        </Container>
      </section>

      {division.full_description ? (
        <section className="py-16">
          <Container>
            <div
              className="prose max-w-3xl text-foreground/90 [&_p]:text-lg [&_p]:leading-relaxed [&_p]:mb-4"
              dangerouslySetInnerHTML={{ __html: division.full_description }}
            />
          </Container>
        </section>
      ) : null}

      {services.length > 0 ? (
        <Section
          eyebrow="Services"
          title={`What ${division.title} does`}
        >
          <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            {services.map((service) => (
              <ServiceCard key={service.id} service={service} />
            ))}
          </div>
        </Section>
      ) : null}

      {advisors.length > 0 ? (
        <Section eyebrow="Advisors" title="Meet the team" border>
          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {advisors.map((advisor) => (
              <AdvisorCard key={advisor.id} advisor={advisor} />
            ))}
          </div>
        </Section>
      ) : null}

      {articles.length > 0 ? (
        <Section eyebrow="Insights" title={`Latest from ${division.title}`} border>
          <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            {articles.map((article) => (
              <ArticleCard key={article.id} article={article} />
            ))}
          </div>
        </Section>
      ) : null}

      {campaigns.length > 0 ? (
        <Section eyebrow="Campaigns" title="Active right now" border>
          <ul className="space-y-3">
            {campaigns.map((campaign) => (
              <li key={campaign.id}>
                <Link
                  href={`/campaigns/${campaign.slug}`}
                  className="block rounded-2xl border border-border bg-surface p-5 hover:shadow-lg transition-shadow"
                >
                  <p className="text-sm uppercase tracking-wider text-muted">
                    Campaign
                  </p>
                  <p className="mt-1 text-lg font-semibold">
                    {campaign.title}
                  </p>
                  {campaign.hero_headline ? (
                    <p className="mt-1 text-sm text-muted">
                      {campaign.hero_headline}
                    </p>
                  ) : null}
                </Link>
              </li>
            ))}
          </ul>
        </Section>
      ) : null}
    </>
  );
}

function Section({
  eyebrow,
  title,
  border,
  children,
}: {
  eyebrow: string;
  title: string;
  border?: boolean;
  children: React.ReactNode;
}) {
  return (
    <section
      className={`py-16 ${
        border ? "border-t border-border" : ""
      }`}
    >
      <Container>
        <p className="text-xs uppercase tracking-[0.2em] text-muted">
          {eyebrow}
        </p>
        <h2 className="mt-2 text-3xl font-semibold tracking-tight">{title}</h2>
        <div className="mt-10">{children}</div>
      </Container>
    </section>
  );
}

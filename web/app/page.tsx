import Link from "next/link";
import { getDivisions, getInsights } from "@/lib/api";
import { Container } from "@/components/Container";
import { DivisionCard } from "@/components/DivisionCard";
import { ArticleCard } from "@/components/ArticleCard";
import { JsonLd } from "@/components/JsonLd";
import { env } from "@/lib/env";

export default async function HomePage() {
  const [divisionsRes, insightsRes] = await Promise.all([
    getDivisions(),
    getInsights({ per_page: 3 }),
  ]);

  const orgJsonLd = {
    "@context": "https://schema.org",
    "@type": "FinancialService",
    name: "Northium Financial",
    url: env.SITE_URL,
    description:
      "Independent, fee-only financial advice for professionals, families, and founders.",
    slogan: "Financial advice without the bank.",
    department: divisionsRes.items.map((d) => ({
      "@type": "FinancialService",
      name: d.title,
      url: `${env.SITE_URL}/divisions/${d.slug}`,
      description: d.short_description,
    })),
  };

  return (
    <>
      <JsonLd data={orgJsonLd} />
      <Hero />
      <DivisionsSection divisions={divisionsRes.items} />
      <InsightsSection articles={insightsRes.items} />
    </>
  );
}

function Hero() {
  return (
    <section className="border-b border-border">
      <Container className="py-24 sm:py-32">
        <p className="text-xs uppercase tracking-[0.2em] text-muted">
          Northium Financial · Independent · Fee-only
        </p>
        <h1 className="mt-4 max-w-3xl text-4xl font-semibold leading-tight tracking-tight sm:text-5xl">
          Financial advice without the bank.
        </h1>
        <p className="mt-6 max-w-2xl text-lg text-muted">
          We don&apos;t sell products, we don&apos;t take kickbacks, and we
          don&apos;t pretend the work is harder than it is. Five practices
          covering the financial questions professionals, families, and
          founders actually face.
        </p>
        <div className="mt-8 flex flex-wrap gap-3">
          <Link
            href="/divisions"
            className="inline-flex items-center rounded-full bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-strong transition-colors"
          >
            See our practices
          </Link>
          <Link
            href="/advisors"
            className="inline-flex items-center rounded-full border border-border px-5 py-2.5 text-sm font-medium text-text hover:border-text hover:bg-surface transition-colors"
          >
            Meet the advisors
          </Link>
        </div>
      </Container>
    </section>
  );
}

function DivisionsSection({
  divisions,
}: {
  divisions: Awaited<ReturnType<typeof getDivisions>>["items"];
}) {
  return (
    <section className="py-20">
      <Container>
        <div className="flex items-end justify-between">
          <div>
            <p className="text-xs uppercase tracking-[0.2em] text-muted">
              Practices
            </p>
            <h2 className="mt-2 text-3xl font-semibold tracking-tight">
              Five practices. Five specific jobs.
            </h2>
          </div>
          <Link
            href="/divisions"
            className="hidden text-sm text-muted hover:text-text sm:inline-block"
          >
            All practices →
          </Link>
        </div>
        <div className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {divisions.map((division) => (
            <DivisionCard key={division.id} division={division} />
          ))}
        </div>
      </Container>
    </section>
  );
}

function InsightsSection({
  articles,
}: {
  articles: Awaited<ReturnType<typeof getInsights>>["items"];
}) {
  if (articles.length === 0) {
    return null;
  }
  return (
    <section className="border-t border-border py-20">
      <Container>
        <div className="flex items-end justify-between">
          <div>
            <p className="text-xs uppercase tracking-[0.2em] text-muted">
              Insights
            </p>
            <h2 className="mt-2 text-3xl font-semibold tracking-tight">
              Plain-language briefs from our advisors
            </h2>
          </div>
          <Link
            href="/insights"
            className="hidden text-sm text-muted hover:text-text sm:inline-block"
          >
            All insights →
          </Link>
        </div>
        <div className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {articles.map((article) => (
            <ArticleCard key={article.id} article={article} />
          ))}
        </div>
      </Container>
    </section>
  );
}

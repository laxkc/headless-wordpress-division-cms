import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Link from "next/link";
import { getService } from "@/lib/api";
import { Container } from "@/components/Container";
import { ArticleCard } from "@/components/ArticleCard";

type Params = { slug: string };

export async function generateMetadata({
  params,
}: {
  params: Promise<Params>;
}): Promise<Metadata> {
  const { slug } = await params;
  const data = await getService(slug);
  if (!data) return { title: "Not found" };
  return {
    title: data.service.title,
    description: data.service.summary,
  };
}

export default async function ServiceDetailPage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { slug } = await params;
  const data = await getService(slug);
  if (!data) notFound();

  const { service, division, related_articles } = data;
  const accent = division?.accent_color ?? "var(--color-accent)";

  return (
    <>
      <section className="border-b border-border">
        <Container className="py-16">
          <nav className="text-xs uppercase tracking-[0.2em] text-muted">
            <Link href="/divisions">Divisions</Link>
            {division ? (
              <>
                <span className="mx-2">›</span>
                <Link href={`/divisions/${division.slug}`}>
                  {division.title}
                </Link>
              </>
            ) : null}
            <span className="mx-2">›</span>
            <span className="text-text">
              {service.title}
            </span>
          </nav>

          <div
            aria-hidden
            className="mt-8 h-1 w-16 rounded-full"
            style={{ background: accent }}
          />
          <h1 className="mt-6 max-w-3xl text-4xl font-semibold tracking-tight sm:text-5xl">
            {service.title}
          </h1>
          <p className="mt-5 max-w-2xl text-lg text-muted">
            {service.summary}
          </p>

          {service.cta_label && service.cta_url ? (
            <div className="mt-8">
              <Link
                href={service.cta_url}
                className="inline-flex items-center rounded-full bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-strong transition-colors"
              >
                {service.cta_label}
              </Link>
            </div>
          ) : null}
        </Container>
      </section>

      {service.body ? (
        <section className="py-16">
          <Container>
            <div
              className="prose max-w-3xl text-text/90 [&_p]:text-lg [&_p]:leading-relaxed [&_p]:mb-4"
              dangerouslySetInnerHTML={{ __html: service.body }}
            />
          </Container>
        </section>
      ) : null}

      {related_articles.length > 0 ? (
        <section className="border-t border-border py-16">
          <Container>
            <p className="text-xs uppercase tracking-[0.2em] text-muted">
              Related insights
            </p>
            <h2 className="mt-2 text-2xl font-semibold tracking-tight">
              More on {division?.title ?? "this topic"}
            </h2>
            <div className="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
              {related_articles.map((article) => (
                <ArticleCard key={article.id} article={article} />
              ))}
            </div>
          </Container>
        </section>
      ) : null}
    </>
  );
}

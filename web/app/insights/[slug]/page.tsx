import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Link from "next/link";
import { getInsight, getInsights } from "@/lib/api";
import { Container } from "@/components/Container";
import { ArticleCard } from "@/components/ArticleCard";
import { JsonLd } from "@/components/JsonLd";
import { env } from "@/lib/env";

type Params = { slug: string };

export async function generateStaticParams(): Promise<Params[]> {
  const { items } = await getInsights({ per_page: 50 });
  return items.map((a) => ({ slug: a.slug }));
}

export async function generateMetadata({
  params,
}: {
  params: Promise<Params>;
}): Promise<Metadata> {
  const { slug } = await params;
  const data = await getInsight(slug);
  if (!data) return { title: "Not found" };
  return {
    title: data.article.title,
    description: data.article.excerpt,
  };
}

export default async function InsightDetailPage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { slug } = await params;
  const data = await getInsight(slug);
  if (!data) notFound();

  const { article, division, author_advisor, related } = data;
  const date = new Date(article.publish_date);

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "Article",
    headline: article.title,
    description: article.excerpt,
    datePublished: article.publish_date,
    url: `${env.SITE_URL}/insights/${article.slug}`,
    ...(author_advisor && {
      author: {
        "@type": "Person",
        name: author_advisor.name,
        jobTitle: author_advisor.role,
        url: `${env.SITE_URL}/advisors/${author_advisor.slug}`,
      },
    }),
    publisher: {
      "@type": "Organization",
      name: "Northium Financial",
      url: env.SITE_URL,
    },
    ...(division && {
      about: {
        "@type": "Thing",
        name: division.title,
        url: `${env.SITE_URL}/divisions/${division.slug}`,
      },
    }),
  };

  return (
    <>
      <JsonLd data={jsonLd} />
      <article>
        <header className="border-b border-border">
          <Container className="py-16">
            <Link
              href="/insights"
              className="text-xs uppercase tracking-[0.2em] text-muted hover:text-text"
            >
              ← All insights
            </Link>
            <h1 className="mt-8 max-w-3xl text-4xl font-semibold leading-tight tracking-tight sm:text-5xl">
              {article.title}
            </h1>
            <div className="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-muted">
              <time dateTime={article.publish_date}>
                {date.toLocaleDateString("en-CA", {
                  year: "numeric",
                  month: "long",
                  day: "numeric",
                })}
              </time>
              {author_advisor ? (
                <Link
                  href={`/advisors/${author_advisor.slug}`}
                  className="hover:text-text"
                >
                  By {author_advisor.name}
                </Link>
              ) : null}
              {division ? (
                <Link
                  href={`/divisions/${division.slug}`}
                  className="inline-flex items-center gap-2 rounded-full border border-border px-3 py-1 text-xs text-text hover:border-text"
                >
                  {division.accent_color ? (
                    <span
                      aria-hidden
                      className="h-1.5 w-1.5 rounded-full"
                      style={{ background: division.accent_color }}
                    />
                  ) : null}
                  {division.title}
                </Link>
              ) : null}
            </div>
          </Container>
        </header>

        {article.content ? (
          <Container className="py-16">
            <div
              className="prose mx-auto max-w-3xl text-text/90 [&_p]:text-lg [&_p]:leading-relaxed [&_p]:mb-4"
              dangerouslySetInnerHTML={{ __html: article.content }}
            />
          </Container>
        ) : null}
      </article>

      {related.length > 0 ? (
        <section className="border-t border-border py-16">
          <Container>
            <p className="text-xs uppercase tracking-[0.2em] text-muted">
              Related
            </p>
            <h2 className="mt-2 text-2xl font-semibold tracking-tight">
              More from {division?.title ?? "the firm"}
            </h2>
            <div className="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
              {related.map((article) => (
                <ArticleCard key={article.id} article={article} />
              ))}
            </div>
          </Container>
        </section>
      ) : null}
    </>
  );
}
